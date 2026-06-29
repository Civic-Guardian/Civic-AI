package com.nagarrakshak.ui.screens

import androidx.compose.animation.core.Animatable
import androidx.compose.animation.core.tween
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.remember
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import coil.compose.AsyncImage
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.nagarrakshak.R
import com.nagarrakshak.data.AuthManager
import com.nagarrakshak.ui.theme.PrimaryColor
import kotlinx.coroutines.delay

@Composable
fun SplashScreen(
    onNavigateToHome: () -> Unit,
    onNavigateToAuth: () -> Unit,
    onNavigateToMaintenance: () -> Unit,
    onNavigateToUpdate: (String) -> Unit
) {
    val context = LocalContext.current
    val authManager = remember { AuthManager(context) }
    
    val alphaAnim = remember { Animatable(0f) }
    val scaleAnim = remember { Animatable(0.7f) }

    LaunchedEffect(Unit) {
        // Run animations concurrently
        alphaAnim.animateTo(1f, animationSpec = tween(1200))
        scaleAnim.animateTo(1f, animationSpec = tween(1200))
        
        var maintenanceMode = false
        var mandatoryUpdate = false
        var updateVersion = "1.2.0"
        
        try {
            val settings = com.nagarrakshak.data.BackendClient.fetchSettings()
            maintenanceMode = settings.optBoolean("maintenance_mode", false)
            updateVersion = settings.optString("app_version", "1.2.0")
            mandatoryUpdate = settings.optBoolean("app_update_mandatory", false)
        } catch (e: Exception) {
            // Ignore offline network errors
        }

        delay(800) // Keep splash screen visible for a moment

        // Fetch current version dynamically from PackageInfo
        val currentVersion = try {
            val packageInfo = context.packageManager.getPackageInfo(context.packageName, 0)
            packageInfo.versionName ?: "1.0"
        } catch (e: Exception) {
            "1.0"
        }

        if (maintenanceMode) {
            onNavigateToMaintenance()
        } else if (mandatoryUpdate && isOlderVersion(updateVersion, currentVersion)) {
            onNavigateToUpdate(updateVersion)
        } else {
            if (authManager.isLoggedIn || authManager.isGuest) {
                onNavigateToHome()
            } else {
                onNavigateToAuth()
            }
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
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center,
            modifier = Modifier.padding(24.dp)
        ) {
            // App Logo with modern glowing shadow/blur effect
            Box(
                modifier = Modifier
                    .size(130.dp)
                    .scale(scaleAnim.value)
                    .alpha(alphaAnim.value),
                contentAlignment = Alignment.Center
            ) {
                // Radial Glow Backdrop
                Box(
                    modifier = Modifier
                        .size(110.dp)
                        .background(
                            Brush.radialGradient(
                                colors = listOf(Color(0xFF1B4FD8).copy(alpha = 0.35f), Color.Transparent)
                            )
                        )
                )
                
                val appIcon = remember(context) {
                    try {
                        context.packageManager.getApplicationIcon(context.packageName)
                    } catch (e: Exception) {
                        null
                    }
                }

                if (appIcon != null) {
                    AsyncImage(
                        model = appIcon,
                        contentDescription = "NagarRakshak Logo",
                        modifier = Modifier
                            .size(100.dp)
                            .clip(RoundedCornerShape(24.dp))
                    )
                }
            }
            
            Spacer(modifier = Modifier.height(28.dp))
            
            Text(
                text = "NAGAR RAKSHAK",
                fontSize = 26.sp,
                fontWeight = FontWeight.ExtraBold,
                color = Color.White,
                modifier = Modifier
                    .alpha(alphaAnim.value)
                    .scale(scaleAnim.value),
                letterSpacing = 4.sp
            )
            
            Spacer(modifier = Modifier.height(6.dp))
            
            Text(
                text = "CIVIC SAFETY & HAZARD NETWORK",
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold,
                color = Color(0xFF38BDF8), // Premium Sky Blue accent
                modifier = Modifier.alpha(alphaAnim.value),
                letterSpacing = 2.sp
            )
        }

        // Circular progress loader at bottom
        CircularProgressIndicator(
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .padding(bottom = 64.dp)
                .size(28.dp),
            color = Color(0xFF1B4FD8),
            strokeWidth = 3.dp
        )
    }
}

fun isOlderVersion(required: String, current: String): Boolean {
    try {
        val reqParts = required.split(".").map { it.toInt() }
        val curParts = current.split(".").map { it.toInt() }
        for (i in 0 until minOf(reqParts.size, curParts.size)) {
            if (reqParts[i] > curParts[i]) return true
            if (reqParts[i] < curParts[i]) return false
        }
        return reqParts.size > curParts.size
    } catch (e: Exception) {
        return required != current
    }
}
