package com.nagarrakshak.ui.screens

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.nagarrakshak.data.BackendClient
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun VerificationDetailsScreen(
    onBackClicked: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()

    var aadhaarNumber by remember { mutableStateOf("") }
    var isVerified by remember { mutableStateOf(false) }
    var isLoading by remember { mutableStateOf(true) }
    var isSaving by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) {
        isLoading = true
        val statsObj = BackendClient.fetchProfileStats()
        if (statsObj != null) {
            isVerified = statsObj.optBoolean("id_card_verified", false)
            val fullAadhaar = statsObj.optString("aadhaar_number", "")
            if (fullAadhaar.length == 12) {
                aadhaarNumber = "XXXX XXXX " + fullAadhaar.takeLast(4)
            }
        }
        isLoading = false
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Verification Details", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White) },
                navigationIcon = {
                    IconButton(onClick = onBackClicked) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back", tint = Color.White)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color(0xFF1E2A3A))
            )
        },
        containerColor = Color(0xFFF5F7FA)
    ) { paddingValues ->
        if (isLoading) {
            Box(Modifier.fillMaxSize().padding(paddingValues), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = Color(0xFF1B4FD8))
            }
        } else {
            Column(
                modifier = Modifier.fillMaxSize().padding(paddingValues).padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                if (isVerified) {
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        colors = CardDefaults.cardColors(containerColor = Color(0xFFECFDF5)),
                        shape = RoundedCornerShape(12.dp),
                        border = BorderStroke(1.dp, Color(0xFFA7F3D0))
                    ) {
                        Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
                            Icon(Icons.Default.CheckCircle, "Verified", tint = Color(0xFF059669), modifier = Modifier.size(36.dp))
                            Spacer(Modifier.width(16.dp))
                            Column {
                                Text("Verification Status", fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color(0xFF065F46))
                                Text("Your identity has been verified successfully.", fontSize = 12.sp, color = Color(0xFF047857))
                            }
                        }
                    }

                    OutlinedTextField(
                        value = aadhaarNumber,
                        onValueChange = {},
                        label = { Text("Aadhaar / ID Card Number") },
                        modifier = Modifier.fillMaxWidth(),
                        enabled = false,
                        shape = RoundedCornerShape(8.dp)
                    )
                } else {
                    Text(
                        "Verify your identity to unlock features like safe routing verification and high authority hazard posting.",
                        fontSize = 13.sp,
                        color = Color(0xFF64748B)
                    )

                    OutlinedTextField(
                        value = aadhaarNumber,
                        onValueChange = { if (it.length <= 12) aadhaarNumber = it },
                        label = { Text("12-Digit Aadhaar Number") },
                        placeholder = { Text("Enter Aadhaar number") },
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(8.dp)
                    )

                    Spacer(Modifier.height(8.dp))

                    Button(
                        onClick = {
                            if (aadhaarNumber.length != 12) {
                                Toast.makeText(context, "Aadhaar must be exactly 12 digits!", Toast.LENGTH_SHORT).show()
                                return@Button
                            }
                            isSaving = true
                            coroutineScope.launch {
                                val success = BackendClient.updateVerification(aadhaarNumber)
                                if (success) {
                                    isVerified = true
                                    aadhaarNumber = "XXXX XXXX " + aadhaarNumber.takeLast(4)
                                    Toast.makeText(context, "Verification details submitted successfully!", Toast.LENGTH_SHORT).show()
                                } else {
                                    Toast.makeText(context, "Verification failed", Toast.LENGTH_SHORT).show()
                                }
                                isSaving = false
                            }
                        },
                        modifier = Modifier.fillMaxWidth().height(50.dp),
                        shape = RoundedCornerShape(8.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8)),
                        enabled = !isSaving
                    ) {
                        if (isSaving) {
                            CircularProgressIndicator(color = Color.White, modifier = Modifier.size(24.dp))
                        } else {
                            Text("Verify Identity", fontWeight = FontWeight.Bold, color = Color.White)
                        }
                    }
                }
            }
        }
    }
}
