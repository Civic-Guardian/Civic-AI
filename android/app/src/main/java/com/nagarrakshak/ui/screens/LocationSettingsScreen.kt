package com.nagarrakshak.ui.screens

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
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
import org.json.JSONObject
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LocationSettingsScreen(
    onBackClicked: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()

    var highAccuracy by remember { mutableStateOf(true) }
    var backgroundLocation by remember { mutableStateOf(false) }
    var isLoading by remember { mutableStateOf(true) }

    LaunchedEffect(Unit) {
        isLoading = true
        val prefs = BackendClient.fetchPreferences()
        if (prefs != null) {
            highAccuracy = prefs.optBoolean("high_accuracy_location", true)
            backgroundLocation = prefs.optBoolean("background_location", false)
        }
        isLoading = false
    }

    fun savePreferences() {
        coroutineScope.launch {
            val body = JSONObject().apply {
                put("high_accuracy_location", highAccuracy)
                put("background_location", backgroundLocation)
            }
            val success = BackendClient.updatePreferences(body)
            if (success) {
                Toast.makeText(context, "Location preferences updated!", Toast.LENGTH_SHORT).show()
            }
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Location Settings", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White) },
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
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(12.dp),
                    border = BorderStroke(1.dp, Color(0xFFE2E8F0))
                ) {
                    Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
                        PreferenceSwitchRow(
                            title = "High Accuracy Mode",
                            description = "Use GPS, Wi-Fi, and mobile networks to determine precise location",
                            checked = highAccuracy,
                            onCheckedChange = {
                                highAccuracy = it
                                savePreferences()
                            }
                        )
                        HorizontalDivider(color = Color(0xFFF1F5F9))
                        PreferenceSwitchRow(
                            title = "Background Scanning",
                            description = "Allow the app to query location while closed to warn you about hazards",
                            checked = backgroundLocation,
                            onCheckedChange = {
                                backgroundLocation = it
                                savePreferences()
                            }
                        )
                    }
                }
            }
        }
    }
}
