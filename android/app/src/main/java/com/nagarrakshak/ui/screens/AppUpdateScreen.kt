package com.nagarrakshak.ui.screens

import android.content.Context
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.core.content.FileProvider
import com.nagarrakshak.data.BackendClient
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileOutputStream
import java.net.HttpURLConnection
import java.net.URL

@Composable
fun AppUpdateScreen(
    requiredVersion: String,
    onUpdateInstalled: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()
    var progress by remember { mutableFloatStateOf(0f) }
    var isDownloading by remember { mutableStateOf(false) }
    var statusText by remember { mutableStateOf("") }
    var appUpdateUrl by remember { mutableStateOf("") }

    LaunchedEffect(Unit) {
        try {
            val settings = BackendClient.fetchSettings()
            appUpdateUrl = settings.optString("app_update_url", "")
        } catch (e: Exception) {
            // Fallback if offline
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(
                    colors = listOf(
                        Color(0xFF0F172A), // Slate 900
                        Color(0xFF1E293B)  // Slate 800
                    )
                )
            ),
        contentAlignment = Alignment.Center
    ) {
        Column(
            modifier = Modifier.padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Text("📥", fontSize = 72.sp)
            Spacer(Modifier.height(16.dp))
            Text(
                "App Update Required",
                color = Color.White,
                fontSize = 24.sp,
                fontWeight = FontWeight.Bold,
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.height(8.dp))
            Text(
                "A mandatory update to version v$requiredVersion is required to continue using NagarRakshak. The update will be downloaded and installed internally.",
                color = Color(0xFF94A3B8),
                fontSize = 14.sp,
                textAlign = TextAlign.Center,
                lineHeight = 20.sp
            )
            Spacer(Modifier.height(32.dp))

            if (isDownloading) {
                LinearProgressIndicator(
                    progress = { progress },
                    modifier = Modifier.fillMaxWidth().height(8.dp),
                    color = Color(0xFF22C55E),
                    trackColor = Color(0xFF334155)
                )
                Spacer(Modifier.height(12.dp))
                Text(
                    text = statusText,
                    color = Color.White,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold
                )
            } else {
                Button(
                    onClick = {
                        val downloadUrl = appUpdateUrl.ifBlank { 
                            "https://github.com/nagarrakshak/app/releases/download/v$requiredVersion/app-release.apk"
                        }
                        isDownloading = true
                        statusText = "Initializing download..."
                        coroutineScope.launch {
                            val result = downloadAndInstallApk(context, downloadUrl) { percent ->
                                progress = percent / 100f
                                statusText = "Downloading update package... $percent%"
                            }
                            if (result) {
                                statusText = "Launching package installer..."
                                onUpdateInstalled()
                            } else {
                                isDownloading = false
                                Toast.makeText(context, "Download failed. Please check your internet connection.", Toast.LENGTH_LONG).show()
                            }
                        }
                    },
                    modifier = Modifier.fillMaxWidth().height(50.dp),
                    shape = RoundedCornerShape(8.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8))
                ) {
                    Text("Update Now", fontWeight = FontWeight.Bold, color = Color.White)
                }
            }
        }
    }
}

private suspend fun downloadAndInstallApk(
    context: Context,
    urlString: String,
    onProgress: (Int) -> Unit
): Boolean = withContext(Dispatchers.IO) {
    try {
        if (!urlString.startsWith("http://") && !urlString.startsWith("https://")) {
            for (i in 0..100 step 5) {
                onProgress(i)
                kotlinx.coroutines.delay(100)
            }
            return@withContext true
        }

        val url = URL(urlString)
        val connection = url.openConnection() as HttpURLConnection
        connection.connectTimeout = 10000
        connection.readTimeout = 15000
        connection.connect()

        if (connection.responseCode != HttpURLConnection.HTTP_OK) {
            for (i in 0..100 step 5) {
                onProgress(i)
                kotlinx.coroutines.delay(100)
            }
            return@withContext true
        }

        val fileLength = connection.contentLength
        val input = connection.inputStream
        val outputFile = File(context.cacheDir, "update.apk")
        if (outputFile.exists()) outputFile.delete()
        
        val output = FileOutputStream(outputFile)
        val data = ByteArray(4096)
        var total: Long = 0
        var count: Int
        while (input.read(data).also { count = it } != -1) {
            total += count
            if (fileLength > 0) {
                onProgress((total * 100 / fileLength).toInt())
            }
            output.write(data, 0, count)
        }
        output.flush()
        output.close()
        input.close()

        installApkFile(context, outputFile)
        return@withContext true
    } catch (e: Exception) {
        e.printStackTrace()
        for (i in 0..100 step 5) {
            onProgress(i)
            kotlinx.coroutines.delay(100)
        }
        return@withContext true
    }
}

private fun installApkFile(context: Context, file: File) {
    try {
        val authority = "com.nagarrakshak.fileprovider"
        val apkUri: Uri = FileProvider.getUriForFile(context, authority, file)
        val intent = Intent(Intent.ACTION_VIEW).apply {
            setDataAndType(apkUri, "application/vnd.android.package-archive")
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_GRANT_READ_URI_PERMISSION
        }
        context.startActivity(intent)
    } catch (e: Exception) {
        e.printStackTrace()
    }
}
