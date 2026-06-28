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
fun DataUsageScreen(
    onBackClicked: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()

    var offlineMapDownloaded by remember { mutableStateOf(false) }
    var isLoading by remember { mutableStateOf(true) }
    var isDownloading by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) {
        isLoading = true
        val prefs = BackendClient.fetchPreferences()
        if (prefs != null) {
            offlineMapDownloaded = prefs.optBoolean("offline_map_downloaded", false)
        }
        isLoading = false
    }

    fun savePreferences(downloaded: Boolean) {
        coroutineScope.launch {
            val body = JSONObject().apply {
                put("offline_map_downloaded", downloaded)
            }
            val success = BackendClient.updatePreferences(body)
            if (success) {
                offlineMapDownloaded = downloaded
                Toast.makeText(context, "Preferences updated!", Toast.LENGTH_SHORT).show()
            }
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Data Usage", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White) },
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
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column(modifier = Modifier.weight(1f)) {
                                Text("Offline Map", fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color(0xFF0F172A))
                                Spacer(Modifier.height(4.dp))
                                Text(
                                    if (offlineMapDownloaded) "Map data for Kota is cached (12MB)." 
                                    else "Download Kota map data to view safety routes and active alerts offline.", 
                                    fontSize = 12.sp, color = Color(0xFF64748B)
                                )
                            }
                            Spacer(Modifier.width(16.dp))
                            Switch(
                                checked = offlineMapDownloaded,
                                onCheckedChange = { checked ->
                                    if (checked) {
                                        isDownloading = true
                                        coroutineScope.launch {
                                            // Simulate map download delay
                                            kotlinx.coroutines.delay(2000)
                                            isDownloading = false
                                            savePreferences(true)
                                            Toast.makeText(context, "Offline map downloaded successfully!", Toast.LENGTH_SHORT).show()
                                        }
                                    } else {
                                        savePreferences(false)
                                    }
                                },
                                colors = SwitchDefaults.colors(checkedThumbColor = Color.White, checkedTrackColor = Color(0xFF1B4FD8)),
                                enabled = !isDownloading
                            )
                        }

                        if (isDownloading) {
                            Spacer(Modifier.height(8.dp))
                            Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                                LinearProgressIndicator(modifier = Modifier.fillMaxWidth(), color = Color(0xFF1B4FD8))
                                Text("Downloading map data (12MB)...", fontSize = 11.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }
        }
    }
}
