package com.nagarrakshak

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.nagarrakshak.ui.navigation.NagarRakshakNavGraph
import com.nagarrakshak.ui.navigation.Screen
import com.nagarrakshak.ui.theme.NagarRakshakTheme
import com.nagarrakshak.ui.theme.PrimaryColor

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.TIRAMISU) {
            androidx.core.app.ActivityCompat.requestPermissions(
                this,
                arrayOf(android.Manifest.permission.POST_NOTIFICATIONS),
                101
            )
        }

        try {
            com.google.firebase.messaging.FirebaseMessaging.getInstance().subscribeToTopic("all")
            com.google.firebase.messaging.FirebaseMessaging.getInstance().subscribeToTopic("alerts")
        } catch (e: Exception) {
            e.printStackTrace()
        }

        setContent {
            NagarRakshakTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    AppMainScreen()
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AppMainScreen() {
    val navController = rememberNavController()
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    val navigationItems = listOf(
        NavigationItem(Screen.Home, Icons.Outlined.Home),
        NavigationItem(Screen.Map, Icons.Outlined.LocationOn),
        NavigationItem(Screen.Alerts, Icons.Outlined.Notifications),
        NavigationItem(Screen.Profile, Icons.Outlined.Person)
    )

    val shouldShowBottomBar = currentRoute != Screen.Splash.route && 
                              currentRoute != Screen.Auth.route && 
                              currentRoute != Screen.Maintenance.route &&
                              currentRoute?.startsWith("app_update") == false &&
                              !com.nagarrakshak.ui.screens.NavigationState.isRideModeActive

    Scaffold(
        bottomBar = {
            if (shouldShowBottomBar) {
                Column(
                    modifier = Modifier.background(Color.White)
                ) {
                    HorizontalDivider(color = Color(0xFFE5E7EB), thickness = 1.dp)
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(56.dp)
                            .padding(vertical = 4.dp),
                        horizontalArrangement = Arrangement.SpaceEvenly,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        navigationItems.forEach { item ->
                            val isSelected = currentRoute == item.screen.route || 
                                    (item.screen == Screen.Profile && currentRoute == Screen.Settings.route) ||
                                    (item.screen == Screen.Alerts && currentRoute == Screen.Report.route)
                            val tintColor = if (isSelected) Color(0xFF1B4FD8) else Color(0xFF6B7280)
                            Column(
                                modifier = Modifier
                                    .clickable {
                                        if (currentRoute != item.screen.route) {
                                            navController.navigate(item.screen.route) {
                                                popUpTo(navController.graph.findStartDestination().id) {
                                                    saveState = true
                                                }
                                                launchSingleTop = true
                                                restoreState = item.screen.route != Screen.Home.route
                                            }
                                        }
                                    }
                                    .weight(1f),
                                horizontalAlignment = Alignment.CenterHorizontally,
                                verticalArrangement = Arrangement.Center
                            ) {
                                Icon(
                                    imageVector = item.icon,
                                    contentDescription = item.screen.title,
                                    tint = tintColor,
                                    modifier = Modifier.size(24.dp)
                                )
                                Spacer(modifier = Modifier.height(2.dp))
                                Text(
                                    text = item.screen.title,
                                    color = tintColor,
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Medium
                                )
                            }
                        }
                    }
                }
            }
        },
        floatingActionButton = {
            if (shouldShowBottomBar) {
                FloatingActionButton(
                    onClick = {
                        navController.navigate(Screen.Report.route) {
                            popUpTo(navController.graph.findStartDestination().id) {
                                saveState = true
                            }
                            launchSingleTop = true
                            restoreState = true
                        }
                    },
                    containerColor = Color(0xFF1B4FD8),
                    contentColor = Color.White,
                    shape = RoundedCornerShape(16.dp),
                    elevation = FloatingActionButtonDefaults.elevation(defaultElevation = 6.dp, pressedElevation = 12.dp),
                    modifier = Modifier.padding(bottom = 16.dp, end = 8.dp)
                ) {
                    Box(modifier = Modifier.size(36.dp), contentAlignment = Alignment.Center) {
                        CameraLineIcon(color = Color.White)
                        Box(
                            modifier = Modifier
                                .size(13.dp)
                                .background(Color(0xFF22C55E), CircleShape)
                                .align(Alignment.TopEnd),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                text = "+",
                                color = Color.White,
                                fontSize = 9.sp,
                                fontWeight = FontWeight.Bold,
                                style = androidx.compose.ui.text.TextStyle(
                                    platformStyle = androidx.compose.ui.text.PlatformTextStyle(
                                        includeFontPadding = false
                                    )
                                )
                            )
                        }
                    }
                }
            }
        }
    ) { innerPadding ->
        Surface(
            modifier = Modifier.padding(innerPadding)
        ) {
            NagarRakshakNavGraph(navController = navController)
        }
    }
}

@Composable
fun CameraLineIcon(color: Color = Color.White) {
    Canvas(modifier = Modifier.size(24.dp)) {
        val strokeWidth = 2.dp.toPx()
        val w = size.width
        val h = size.height
        
        // Camera body dimensions
        val bodyW = w * 0.75f
        val bodyH = h * 0.55f
        val bodyX = (w - bodyW) / 2f
        val bodyY = (h - bodyH) / 2f + 2.dp.toPx()
        
        // Draw camera body
        drawRoundRect(
            color = color,
            topLeft = Offset(bodyX, bodyY),
            size = Size(bodyW, bodyH),
            cornerRadius = CornerRadius(4.dp.toPx(), 4.dp.toPx()),
            style = Stroke(width = strokeWidth)
        )
        
        // Draw lens
        drawCircle(
            color = color,
            radius = bodyH * 0.32f,
            center = Offset(w / 2f, bodyY + bodyH / 2f),
            style = Stroke(width = strokeWidth)
        )
        
        // Draw flash indicator dot
        drawCircle(
            color = color,
            radius = 1.5.dp.toPx(),
            center = Offset(bodyX + bodyW - 6.dp.toPx(), bodyY + 6.dp.toPx())
        )
        
        // Draw camera top tab
        val tabW = bodyW * 0.28f
        val tabH = 3.dp.toPx()
        val tabX = (w - tabW) / 2f
        val tabY = bodyY - tabH
        drawRoundRect(
            color = color,
            topLeft = Offset(tabX, tabY),
            size = Size(tabW, tabH + 2.dp.toPx()),
            cornerRadius = CornerRadius(2.dp.toPx(), 2.dp.toPx())
        )
    }
}

data class NavigationItem(
    val screen: Screen,
    val icon: ImageVector
)
