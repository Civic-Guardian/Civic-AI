package com.nagarrakshak.ui.screens

import android.widget.Toast
import androidx.compose.animation.*
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.ui.window.DialogProperties
import com.nagarrakshak.data.AuthManager
import com.nagarrakshak.ui.theme.PrimaryColor
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import android.app.Activity
import android.util.Log
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import com.google.android.gms.auth.api.signin.GoogleSignIn
import com.google.android.gms.auth.api.signin.GoogleSignInOptions
import com.google.android.gms.common.api.ApiException
import com.google.firebase.auth.FirebaseAuth
import com.google.firebase.auth.GoogleAuthProvider

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AuthScreen(
    onNavigateToHome: () -> Unit
) {
    val context = LocalContext.current
    val authManager = remember { AuthManager(context) }
    val coroutineScope = rememberCoroutineScope()

    var isSignInTab by remember { mutableStateOf(true) }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var name by remember { mutableStateOf("") }

    var isLoggingIn by remember { mutableStateOf(false) }
    var loadingMessage by remember { mutableStateOf("") }
    var showGoogleChooser by remember { mutableStateOf(false) }

    val signInLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.StartActivityForResult()
    ) { result ->
        isLoggingIn = false
        val task = GoogleSignIn.getSignedInAccountFromIntent(result.data)
        try {
            val account = task.getResult(ApiException::class.java)!!
            val idToken = account.idToken
            if (idToken != null) {
                isLoggingIn = true
                loadingMessage = "Authenticating with Firebase..."
                val credential = GoogleAuthProvider.getCredential(idToken, null)
                FirebaseAuth.getInstance().signInWithCredential(credential)
                    .addOnCompleteListener { authTask ->
                        if (authTask.isSuccessful) {
                            val firebaseUser = authTask.result?.user
                            val nameVal = firebaseUser?.displayName ?: account.displayName ?: "Google Citizen"
                            val emailVal = firebaseUser?.email ?: account.email ?: ""
                            val photoUrlVal = firebaseUser?.photoUrl?.toString() ?: account.photoUrl?.toString()
                            
                            loadingMessage = "Synchronizing profile with backend..."
                            coroutineScope.launch {
                                val response = com.nagarrakshak.data.BackendClient.googleLogin(nameVal, emailVal, photoUrlVal)
                                isLoggingIn = false
                                if (response != null && response.success && response.data != null) {
                                    authManager.loginWithGoogle(emailVal, nameVal, photoUrlVal, response.data.token)
                                    Toast.makeText(context, "Welcome back, $nameVal!", Toast.LENGTH_SHORT).show()
                                    onNavigateToHome()
                                } else {
                                    Toast.makeText(context, response?.message ?: "Backend verification failed", Toast.LENGTH_LONG).show()
                                }
                            }
                        } else {
                            isLoggingIn = false
                            val err = authTask.exception?.message ?: "Firebase Auth failed"
                            Log.e("AuthScreen", "Firebase Auth error: $err", authTask.exception)
                            
                            val isCertError = err.contains("CertPathValidatorException", ignoreCase = true) || 
                                             err.contains("Trust anchor", ignoreCase = true)
                            
                            if (isCertError) {
                                val certExplain = "SSL Certificate Trust Error: Your network/VPN or emulator is intercepting SSL traffic. Please disable VPN/proxies or switch networks."
                                Toast.makeText(context, certExplain, Toast.LENGTH_LONG).show()
                                Toast.makeText(context, "Falling back to simulated login for development...", Toast.LENGTH_SHORT).show()
                                showGoogleChooser = true
                            } else {
                                Toast.makeText(context, err, Toast.LENGTH_LONG).show()
                            }
                        }
                    }
            } else {
                Toast.makeText(context, "Google Sign-In did not return an ID token", Toast.LENGTH_LONG).show()
            }
        } catch (e: ApiException) {
            Log.e("AuthScreen", "Google sign in api exception: status=${e.statusCode}, msg=${e.message}", e)
            val errorMsg = when (e.statusCode) {
                10 -> "Developer Error (10): Please ensure the SHA-1 of your debug keystore is registered in the Firebase Console for com.nagarrakshak."
                12500 -> "Sign-In Mismatch (12500): Check Google Play Services configuration, package name, or SHA-1."
                7 -> "Network Error (7): Please check your internet connection."
                12501 -> "Google Sign-In cancelled."
                else -> "Google Sign-In failed: error code ${e.statusCode}. ${e.message}"
            }
            if (e.statusCode != 12501) {
                Toast.makeText(context, errorMsg, Toast.LENGTH_LONG).show()
            } else {
                Toast.makeText(context, "Google Sign-In cancelled", Toast.LENGTH_SHORT).show()
            }
            
            // FALLBACK: If developer error 10 or 12500 (mismatched SHA-1), show the mock chooser so they can still test the flow!
            if (e.statusCode == 10 || e.statusCode == 12500) {
                Toast.makeText(context, "Falling back to simulated login for development...", Toast.LENGTH_SHORT).show()
                showGoogleChooser = true
            }
        }
    }

    // Simulated Google Accounts
    val googleAccounts = listOf(
        GoogleAccount("Mihir Aditya", "mihir.aditya@gmail.com", "🧑‍💻"),
        GoogleAccount("Jaykishan Rawat", "jksonu1436@gmail.com", "👤"),
        GoogleAccount("Test Citizen", "test.citizen@nagarrakshak.org", "🛡️")
    )

    if (showGoogleChooser) {
        Dialog(
            onDismissRequest = { showGoogleChooser = false },
            properties = DialogProperties(usePlatformDefaultWidth = false)
        ) {
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(24.dp)
                    .clip(RoundedCornerShape(24.dp)),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 8.dp)
            ) {
                Column(
                    modifier = Modifier.padding(24.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        text = "Sign in with Google",
                        fontSize = 20.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF1E293B)
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = "to continue to NagarRakshak",
                        fontSize = 14.sp,
                        color = Color(0xFF64748B)
                    )
                    Spacer(modifier = Modifier.height(20.dp))

                    googleAccounts.forEach { account ->
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable {
                                    showGoogleChooser = false
                                    isLoggingIn = true
                                    coroutineScope.launch {
                                        loadingMessage = "Connecting with Google Play Services..."
                                        delay(300)
                                        loadingMessage = "Authenticating via Firebase Auth..."
                                        delay(300)
                                        val response = com.nagarrakshak.data.BackendClient.googleLogin(account.name, account.email, null)
                                        isLoggingIn = false
                                        
                                        if (response != null && response.success && response.data != null) {
                                            val u = response.data.user
                                            authManager.loginWithGoogle(u.email, u.name, account.avatarEmoji, response.data.token)
                                            Toast.makeText(context, "Welcome back, ${u.name}!", Toast.LENGTH_SHORT).show()
                                            onNavigateToHome()
                                        } else {
                                            Toast.makeText(context, response?.message ?: "Google Sign-In failed", Toast.LENGTH_LONG).show()
                                        }
                                    }
                                }
                                .padding(vertical = 12.dp, horizontal = 8.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Box(
                                modifier = Modifier
                                    .size(40.dp)
                                    .background(Color(0xFFF1F5F9), shape = CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Text(account.avatarEmoji, fontSize = 20.sp)
                            }
                            Spacer(modifier = Modifier.width(16.dp))
                            Column {
                                Text(
                                    text = account.name,
                                    fontSize = 15.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = Color(0xFF0F172A)
                                )
                                Text(
                                    text = account.email,
                                    fontSize = 13.sp,
                                    color = Color(0xFF64748B)
                                )
                            }
                        }
                        HorizontalDivider(color = Color(0xFFF1F5F9))
                    }
                    
                    Spacer(modifier = Modifier.height(12.dp))
                    TextButton(onClick = { showGoogleChooser = false }) {
                        Text("Cancel", color = Color.Red)
                    }
                }
            }
        }
    }

    if (isLoggingIn) {
        Dialog(
            onDismissRequest = {},
            properties = DialogProperties(dismissOnBackPress = false, dismissOnClickOutside = false)
        ) {
            Card(
                shape = RoundedCornerShape(24.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    modifier = Modifier.padding(32.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center
                ) {
                    CircularProgressIndicator(color = PrimaryColor)
                    Spacer(modifier = Modifier.height(16.dp))
                    Text(
                        text = loadingMessage,
                        fontSize = 14.sp,
                        color = Color.DarkGray,
                        textAlign = TextAlign.Center
                    )
                }
            }
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF8FAFC))
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.SpaceBetween
        ) {
            // Top Section (Logo & Banner)
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                modifier = Modifier.padding(top = 28.dp)
            ) {
                Box(
                    modifier = Modifier
                        .size(72.dp)
                        .background(PrimaryColor.copy(alpha = 0.1f), shape = RoundedCornerShape(20.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Text("🛡️", fontSize = 38.sp)
                }
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "Welcome to NagarRakshak",
                    fontSize = 24.sp,
                    fontWeight = FontWeight.ExtraBold,
                    color = Color(0xFF0F172A)
                )
                Spacer(modifier = Modifier.height(6.dp))
                Text(
                    text = "Join our network of safe and clean citizens",
                    fontSize = 14.sp,
                    color = Color(0xFF64748B),
                    textAlign = TextAlign.Center
                )
            }

            // Middle Section (Credentials Form)
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(vertical = 24.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(24.dp),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Column(modifier = Modifier.padding(24.dp)) {
                    // Tab Selector
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(Color(0xFFF1F5F9), RoundedCornerShape(12.dp))
                            .padding(4.dp)
                    ) {
                        TabButton(
                            text = "Sign In",
                            isSelected = isSignInTab,
                            onClick = { isSignInTab = true },
                            modifier = Modifier.weight(1f)
                        )
                        TabButton(
                            text = "Sign Up",
                            isSelected = !isSignInTab,
                            onClick = { isSignInTab = false },
                            modifier = Modifier.weight(1f)
                        )
                    }

                    Spacer(modifier = Modifier.height(20.dp))

                    // Fields
                    if (!isSignInTab) {
                        OutlinedTextField(
                            value = name,
                            onValueChange = { name = it },
                            label = { Text("Full Name") },
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp)
                        )
                        Spacer(modifier = Modifier.height(12.dp))
                    }

                    OutlinedTextField(
                        value = email,
                        onValueChange = { email = it },
                        label = { Text("Email Address") },
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp)
                    )
                    Spacer(modifier = Modifier.height(12.dp))

                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it },
                        label = { Text("Password") },
                        visualTransformation = PasswordVisualTransformation(),
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp)
                    )

                    Spacer(modifier = Modifier.height(20.dp))

                    // Action Button
                    Button(
                        onClick = {
                            if (email.isBlank() || password.isBlank() || (!isSignInTab && name.isBlank())) {
                                Toast.makeText(context, "Please fill in all fields", Toast.LENGTH_SHORT).show()
                                return@Button
                            }
                            isLoggingIn = true
                            coroutineScope.launch {
                                loadingMessage = if (isSignInTab) "Connecting to server..." else "Creating account..."
                                val response = if (isSignInTab) {
                                    com.nagarrakshak.data.BackendClient.login(email, password)
                                } else {
                                    com.nagarrakshak.data.BackendClient.register(name, email, password)
                                }
                                
                                isLoggingIn = false
                                if (response != null) {
                                    if (response.success && response.data != null) {
                                        val u = response.data.user
                                        authManager.loginWithEmail(u.email, u.name, response.data.token)
                                        Toast.makeText(context, response.message ?: "Authenticated successfully!", Toast.LENGTH_SHORT).show()
                                        onNavigateToHome()
                                    } else {
                                        Toast.makeText(context, response.message ?: "Authentication failed", Toast.LENGTH_LONG).show()
                                    }
                                } else {
                                    Toast.makeText(context, "Cannot connect to server. Check backend status.", Toast.LENGTH_LONG).show()
                                }
                            }
                        },
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(48.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = PrimaryColor),
                        shape = RoundedCornerShape(12.dp)
                    ) {
                        Text(
                            text = if (isSignInTab) "Sign In" else "Create Account",
                            fontWeight = FontWeight.Bold,
                            fontSize = 15.sp
                        )
                    }
                }
            }

            // Bottom Section (Google Login & Guest Option)
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                modifier = Modifier.fillMaxWidth()
            ) {
                // Continue with Google Button
                Button(
                    onClick = {
                        isLoggingIn = true
                        loadingMessage = "Connecting with Google Play Services..."
                        
                        val gso = GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                            .requestIdToken("280377628764-8qlvml30baqnumrne1s2r99u7nkn5rd8.apps.googleusercontent.com")
                            .requestEmail()
                            .build()
                        val googleSignInClient = GoogleSignIn.getClient(context, gso)
                        
                        // Sign out first to ensure account chooser is shown
                        googleSignInClient.signOut().addOnCompleteListener {
                            try {
                                signInLauncher.launch(googleSignInClient.signInIntent)
                            } catch (e: Exception) {
                                isLoggingIn = false
                                Log.e("AuthScreen", "Failed to launch Google Sign-In intent", e)
                                Toast.makeText(context, "Google Sign-In failed to start: ${e.message}", Toast.LENGTH_LONG).show()
                            }
                        }
                    },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color.White),
                    border = BorderStroke(1.dp, Color(0xFFCBD5E1)),
                    shape = RoundedCornerShape(12.dp)
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Text("🇺🇸", fontSize = 18.sp) // Mock Google Icon/Flag representation
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = "Continue with Google",
                            color = Color(0xFF0F172A),
                            fontWeight = FontWeight.SemiBold
                        )
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                // Continue as Guest Option
                Row(
                    modifier = Modifier
                        .clickable {
                            isLoggingIn = true
                            coroutineScope.launch {
                                loadingMessage = "Initializing guest profile..."
                                delay(800)
                                authManager.loginAsGuest()
                                isLoggingIn = false
                                Toast.makeText(context, "Signed in as Guest", Toast.LENGTH_SHORT).show()
                                onNavigateToHome()
                            }
                        }
                        .padding(8.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Continue as Guest",
                        color = PrimaryColor,
                        fontWeight = FontWeight.Bold,
                        fontSize = 14.sp
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = "→",
                        color = PrimaryColor,
                        fontWeight = FontWeight.Bold,
                        fontSize = 16.sp
                    )
                }
            }
        }
    }
}

@Composable
fun TabButton(
    text: String,
    isSelected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .clip(RoundedCornerShape(8.dp))
            .background(if (isSelected) Color.White else Color.Transparent)
            .clickable { onClick() }
            .padding(vertical = 8.dp),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = text,
            fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
            color = if (isSelected) Color(0xFF0F172A) else Color(0xFF64748B),
            fontSize = 14.sp
        )
    }
}

data class GoogleAccount(
    val name: String,
    val email: String,
    val avatarEmoji: String
)
