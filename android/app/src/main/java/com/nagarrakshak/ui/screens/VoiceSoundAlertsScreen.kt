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
fun VoiceSoundAlertsScreen(
    onBackClicked: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()

    var voiceAlerts by remember { mutableStateOf(true) }
    var soundAlerts by remember { mutableStateOf(true) }
    var isLoading by remember { mutableStateOf(true) }

    LaunchedEffect(Unit) {
        isLoading = true
        val prefs = BackendClient.fetchPreferences()
        if (prefs != null) {
            voiceAlerts = prefs.optBoolean("voice_alerts_enabled", true)
            soundAlerts = prefs.optBoolean("sound_alerts_enabled", true)
        }
        isLoading = false
    }

    fun savePreferences() {
        coroutineScope.launch {
            val body = JSONObject().apply {
                put("voice_alerts_enabled", voiceAlerts)
                put("sound_alerts_enabled", soundAlerts)
            }
            val success = BackendClient.updatePreferences(body)
            if (success) {
                Toast.makeText(context, "Voice & sound preferences updated!", Toast.LENGTH_SHORT).show()
            }
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Voices & Sound Alerts", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White) },
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
                            title = "Voice Prompts",
                            description = "Play spoken voice alerts when approaching a hazard or turning",
                            checked = voiceAlerts,
                            onCheckedChange = {
                                voiceAlerts = it
                                savePreferences()
                            }
                        )
                        HorizontalDivider(color = Color(0xFFF1F5F9))
                        PreferenceSwitchRow(
                            title = "Vibrate & Sound Alert",
                            description = "Double beep beep audio sounds when entering danger warning zone",
                            checked = soundAlerts,
                            onCheckedChange = {
                                soundAlerts = it
                                savePreferences()
                            }
                        )
                    }
                }
            }
        }
    }
}
