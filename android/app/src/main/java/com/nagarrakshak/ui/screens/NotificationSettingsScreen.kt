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
fun NotificationSettingsScreen(
    onBackClicked: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()

    var emailNotifications by remember { mutableStateOf(true) }
    var pushNotifications by remember { mutableStateOf(true) }
    var hazardAlerts by remember { mutableStateOf(true) }
    var isLoading by remember { mutableStateOf(true) }

    LaunchedEffect(Unit) {
        isLoading = true
        val prefs = BackendClient.fetchPreferences()
        if (prefs != null) {
            emailNotifications = prefs.optBoolean("email_notifications", true)
            pushNotifications = prefs.optBoolean("push_notifications", true)
            hazardAlerts = prefs.optBoolean("hazard_alerts", true)
        }
        isLoading = false
    }

    fun savePreferences() {
        coroutineScope.launch {
            val body = JSONObject().apply {
                put("email_notifications", emailNotifications)
                put("push_notifications", pushNotifications)
                put("hazard_alerts", hazardAlerts)
            }
            val success = BackendClient.updatePreferences(body)
            if (success) {
                Toast.makeText(context, "Notification preferences updated!", Toast.LENGTH_SHORT).show()
            }
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Notification Settings", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White) },
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
                            title = "Email Notifications",
                            description = "Receive weekly reports and system alerts via email",
                            checked = emailNotifications,
                            onCheckedChange = {
                                emailNotifications = it
                                savePreferences()
                            }
                        )
                        HorizontalDivider(color = Color(0xFFF1F5F9))
                        PreferenceSwitchRow(
                            title = "Push Notifications",
                            description = "Receive instant reports and alert updates on your device",
                            checked = pushNotifications,
                            onCheckedChange = {
                                pushNotifications = it
                                savePreferences()
                            }
                        )
                        HorizontalDivider(color = Color(0xFFF1F5F9))
                        PreferenceSwitchRow(
                            title = "Nearby Hazard Alerts",
                            description = "Get notified if a hazard is reported within 2km of your location",
                            checked = hazardAlerts,
                            onCheckedChange = {
                                hazardAlerts = it
                                savePreferences()
                            }
                        )
                    }
                }
            }
        }
    }
}

@Composable
fun PreferenceSwitchRow(
    title: String,
    description: String,
    checked: Boolean,
    onCheckedChange: (Boolean) -> Unit
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Column(modifier = Modifier.weight(1f)) {
            Text(title, fontWeight = FontWeight.Bold, fontSize = 14.sp, color = Color(0xFF0F172A))
            Spacer(Modifier.height(2.dp))
            Text(description, fontSize = 11.sp, color = Color(0xFF64748B))
        }
        Spacer(Modifier.width(16.dp))
        Switch(
            checked = checked,
            onCheckedChange = onCheckedChange,
            colors = SwitchDefaults.colors(checkedThumbColor = Color.White, checkedTrackColor = Color(0xFF1B4FD8))
        )
    }
}
