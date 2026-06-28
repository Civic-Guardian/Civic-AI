@file:Suppress("DEPRECATION")
package com.nagarrakshak.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.KeyboardArrowDown
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.geometry.Size
import androidx.compose.material3.*
import androidx.compose.material.pullrefresh.PullRefreshIndicator
import androidx.compose.material.pullrefresh.pullRefresh
import androidx.compose.material.pullrefresh.rememberPullRefreshState
import androidx.compose.material.ExperimentalMaterialApi
import androidx.compose.runtime.Composable
import androidx.compose.runtime.rememberCoroutineScope
import kotlinx.coroutines.launch
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.nagarrakshak.ui.theme.DangerColor
import com.nagarrakshak.ui.theme.PrimaryColor
import com.nagarrakshak.ui.theme.WarningColor
import com.nagarrakshak.data.BackendClient
import com.nagarrakshak.data.models.HazardReport
import com.nagarrakshak.data.models.Severity
import androidx.compose.animation.core.*
import androidx.compose.ui.draw.alpha


import android.Manifest
import android.content.Context
import android.location.Geocoder
import android.location.Location
import android.location.LocationListener
import android.location.LocationManager
import android.os.Bundle
import android.os.Looper
import android.widget.Toast
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.platform.LocalContext
import androidx.core.content.ContextCompat
import java.util.Locale
import com.google.android.gms.maps.model.CameraPosition
import com.google.android.gms.maps.model.LatLng
import com.google.maps.android.compose.GoogleMap
import com.google.maps.android.compose.Marker
import com.google.maps.android.compose.MarkerState
import com.google.maps.android.compose.rememberCameraPositionState

@OptIn(ExperimentalMaterial3Api::class, ExperimentalMaterialApi::class)
@Composable
fun HomeScreen(
    onNavigateToReport: () -> Unit,
    onNavigateToDetail: (String) -> Unit,
    onNavigateToMap: () -> Unit,
    onNavigateToAlerts: () -> Unit,
    onNavigateToNotifications: () -> Unit
) {
    val context = LocalContext.current
    var currentCityName by remember { mutableStateOf("Kota") }
    var userLatLng by remember { mutableStateOf<LatLng?>(null) }
    var userPincode by remember { mutableStateOf("324005") }
    var showScoreDialog by remember { mutableStateOf(false) }
    var alertsList by remember { mutableStateOf<List<HazardReport>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }

    val safetyScore = remember(alertsList, userPincode, userLatLng) {
        val currentLatLng = userLatLng
        val count = alertsList.count { 
            it.locationName.contains(userPincode) || 
            (currentLatLng != null && Math.abs(it.latitude - currentLatLng.latitude) < 0.02 && Math.abs(it.longitude - currentLatLng.longitude) < 0.02)
        }
        (100 - (count * 6)).coerceIn(10, 100)
    }

    val riskZone = remember(safetyScore) {
        when {
            safetyScore >= 85 -> "Very Low Risk Zone"
            safetyScore >= 70 -> "Low Risk Zone"
            safetyScore >= 50 -> "Medium Risk Zone"
            else -> "High Risk Zone"
        }
    }

    val riskColor = remember(safetyScore) {
        when {
            safetyScore >= 85 -> Color(0xFF15803D)
            safetyScore >= 70 -> Color(0xFF16A34A)
            safetyScore >= 50 -> Color(0xFFD97706)
            else -> Color(0xFFDC2626)
        }
    }
    var isRefreshing by remember { mutableStateOf(false) }
    val coroutineScope = rememberCoroutineScope()

    // Load data function (reusable for pull-to-refresh)
    val loadData: suspend () -> Unit = {
        alertsList = BackendClient.fetchNearbyHazards()
    }

    LaunchedEffect(Unit) {
        isLoading = true
        loadData()
        isLoading = false
    }

    val locationPermissionsLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        val fineGranted = permissions[Manifest.permission.ACCESS_FINE_LOCATION] ?: false
        val coarseGranted = permissions[Manifest.permission.ACCESS_COARSE_LOCATION] ?: false
        if (fineGranted || coarseGranted) {
            fetchHomeLocation(context) { city, pincode ->
                currentCityName = city
                userPincode = pincode
            }
            fetchCurrentLocationLatLng(context) { latLng ->
                userLatLng = latLng
            }
        }
    }

    LaunchedEffect(Unit) {
        val hasFine = ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
        val hasCoarse = ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
        if (hasFine || hasCoarse) {
            fetchHomeLocation(context) { city, pincode ->
                currentCityName = city
                userPincode = pincode
            }
            fetchCurrentLocationLatLng(context) { latLng ->
                userLatLng = latLng
            }
        } else {
            locationPermissionsLauncher.launch(
                arrayOf(Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION)
            )
        }
    }

    // Safety Score Info Dialog Popup
    if (showScoreDialog) {
        AlertDialog(
            onDismissRequest = { showScoreDialog = false },
            containerColor = Color(0xFF1E2A3A),
            title = { Text("Area Safety Score", fontWeight = FontWeight.Bold, color = Color.White) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(
                        text = "Your area safety score is $safetyScore/100 (Pincode: $userPincode), which indicates a $riskZone.",
                        fontWeight = FontWeight.SemiBold,
                        color = if (safetyScore >= 70) Color(0xFF4ADE80) else Color(0xFFFCA5A5)
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = "This real-time safety metric is aggregated from citizen-reported public safety issues within your vicinity.",
                        fontSize = 13.sp,
                        color = Color(0xFFE2E8F0)
                    )
                    Text("• Active unresolved hazards: ${alertsList.size} ($riskZone)", fontSize = 12.sp, color = Color(0xFFCBD5E1))
                    Text("• Hazard resolution rate: 94% (High)", fontSize = 12.sp, color = Color(0xFFCBD5E1))
                    Text("• Community safety engagement: Excellent", fontSize = 12.sp, color = Color(0xFFCBD5E1))
                }
            },
            confirmButton = {
                TextButton(onClick = { showScoreDialog = false }) {
                    Text("Close", color = Color(0xFF4ADE80), fontWeight = FontWeight.Bold)
                }
            }
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF5F7FA))
    ) {
        // Header Section (dark navy bg #1E2A3A)
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .background(Color(0xFF1E2A3A))
                .padding(bottom = 16.dp)
        ) {
            // Status bar spacer or top padding
            Spacer(modifier = Modifier.height(12.dp))
            
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                // Top Left: City name "Kota" in white 20px Bold with a dropdown chevron
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    modifier = Modifier.clickable { onNavigateToMap() }
                ) {
                    Text(
                        text = currentCityName,
                        fontSize = 20.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color.White
                    )
                    Spacer(modifier = Modifier.width(2.dp))
                    Icon(
                        imageVector = Icons.Default.KeyboardArrowDown,
                        contentDescription = "Change Location",
                        tint = Color.White,
                        modifier = Modifier.size(20.dp)
                    )
                }
                
                // Top Right: Score badge (circular shows "82") + bell icon with notification dot
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Score badge
                    Box(
                        modifier = Modifier
                            .size(36.dp)
                            .background(riskColor, shape = CircleShape)
                            .clickable { showScoreDialog = true },
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = safetyScore.toString(),
                            color = Color.White,
                            fontSize = 14.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                    
                    // Bell icon with red notification dot
                    Box(
                        modifier = Modifier
                            .size(24.dp)
                            .clickable { onNavigateToNotifications() },
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Default.Notifications,
                            contentDescription = "Notifications",
                            tint = Color.White,
                            modifier = Modifier.size(22.dp)
                        )
                        // Red notification dot
                        Box(
                            modifier = Modifier
                                .size(8.dp)
                                .background(Color(0xFFDC2626), shape = CircleShape)
                                .align(Alignment.TopEnd)
                                .offset(x = 1.dp, y = (-1).dp)
                        )
                    }
                }
            }
            
            Spacer(modifier = Modifier.height(8.dp))
            
            // Search Bar: Full width, white background, 8px border-radius, 1px #E5E7EB border, no shadow
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp)
            ) {
                var searchInput by remember { mutableStateOf("") }
                OutlinedTextField(
                    value = searchInput,
                    onValueChange = { searchInput = it },
                    placeholder = { 
                        Text(
                            "Search hazards, areas, landmarks...",
                            fontSize = 14.sp,
                            color = Color(0xFF6B7280)
                        ) 
                    },
                    leadingIcon = { 
                        Icon(
                            imageVector = Icons.Default.Search, 
                            contentDescription = "Search",
                            tint = Color(0xFF6B7280)
                        ) 
                    },
                    trailingIcon = {
                        MicLineIcon(
                            color = Color(0xFF6B7280),
                            modifier = Modifier.size(20.dp)
                        )
                    },
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(8.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = Color(0xFFE5E7EB),
                        unfocusedBorderColor = Color(0xFFE5E7EB),
                        focusedContainerColor = Color.White,
                        unfocusedContainerColor = Color.White,
                        focusedTextColor = Color(0xFF111827),
                        unfocusedTextColor = Color(0xFF111827)
                    ),
                    singleLine = true
                )
            }
        }
        
        HorizontalDivider(color = Color(0xFFE5E7EB), thickness = 1.dp)

        // Scrollable Body Content with Pull-to-Refresh
        val pullRefreshState = rememberPullRefreshState(
            refreshing = isRefreshing,
            onRefresh = {
                coroutineScope.launch {
                    isRefreshing = true
                    loadData()
                    isRefreshing = false
                }
            }
        )
        Box(Modifier.fillMaxSize().pullRefresh(pullRefreshState)) {
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
            contentPadding = PaddingValues(top = 8.dp, bottom = 24.dp)
        ) {
            // Action Strip (below search)
            item {
                Row(
                    modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Left button: solid civic blue #1B4FD8
                    Button(
                        onClick = onNavigateToReport,
                        modifier = Modifier
                            .weight(1f)
                            .height(44.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8)),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(0.dp)
                    ) {
                        Text(
                            text = "+ Report Hazard",
                            color = Color.White,
                            fontSize = 15.sp,
                            fontWeight = FontWeight.SemiBold
                        )
                    }
                    
                    // Right button: outlined civic blue
                    OutlinedButton(
                        onClick = onNavigateToMap,
                        modifier = Modifier
                            .weight(1f)
                            .height(44.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF1B4FD8)),
                        border = BorderStroke(1.dp, Color(0xFF1B4FD8)),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(0.dp)
                    ) {
                        Text(
                            text = "View Hazard Map",
                            color = Color(0xFF1B4FD8),
                            fontSize = 15.sp,
                            fontWeight = FontWeight.SemiBold
                        )
                    }
                }
            }

            // Map Preview Section
            item {
                Column(
                    modifier = Modifier.fillMaxWidth(),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "LIVE HAZARD MAP — ${currentCityName.uppercase()}",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Medium,
                        color = Color(0xFF6B7280),
                        letterSpacing = 1.sp
                    )
                    
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                    ) {
                        Column {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(180.dp)
                                    .clip(RoundedCornerShape(topStart = 8.dp, topEnd = 8.dp))
                            ) {
                                val center = userLatLng ?: LatLng(25.18254, 75.82736)
                                val cameraPositionState = rememberCameraPositionState {
                                    position = CameraPosition.fromLatLngZoom(center, 14f)
                                }
                                
                                LaunchedEffect(userLatLng) {
                                    val latLng = userLatLng
                                    if (latLng != null) {
                                        cameraPositionState.position = CameraPosition.fromLatLngZoom(latLng, 14f)
                                    }
                                }
                                
                                GoogleMap(
                                    modifier = Modifier.fillMaxSize(),
                                    cameraPositionState = cameraPositionState,
                                    properties = com.google.maps.android.compose.MapProperties(isMyLocationEnabled = userLatLng != null),
                                    uiSettings = com.google.maps.android.compose.MapUiSettings(
                                        zoomControlsEnabled = false,
                                        myLocationButtonEnabled = false
                                    )
                                ) {
                                    alertsList.take(3).forEach { alert ->
                                        Marker(
                                            state = MarkerState(position = LatLng(alert.latitude, alert.longitude)),
                                            title = alert.title
                                        )
                                    }
                                }
                            }
                            
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable { onNavigateToMap() }
                                    .padding(horizontal = 16.dp, vertical = 12.dp),
                                contentAlignment = Alignment.CenterEnd
                            ) {
                                Text(
                                    text = "View Full Map →",
                                    color = Color(0xFF1B4FD8),
                                    fontSize = 13.sp,
                                    fontWeight = FontWeight.Bold
                                )
                            }
                        }
                    }
                }
            }

            // Nearby Alerts Header
            item {
                Row(
                    modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Nearby Alerts",
                        fontSize = 18.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF111827)
                    )
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.clickable { onNavigateToAlerts() }
                    ) {
                        Text(
                            text = "View All ›",
                            fontSize = 13.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF1B4FD8)
                        )
                    }
                }
            }

            // Nearby Alerts List (show max 5 on home)
            val displayAlerts = alertsList.take(5)
            if (isLoading || alertsList.isEmpty()) {
                items(3) {
                    SkeletonAlertCard()
                }
            } else {
                items(count = displayAlerts.size, key = { index -> displayAlerts[index].id }) { index ->
                    val alert = displayAlerts[index]
                    val severityColor = when (alert.severity) {
                        Severity.HIGH -> Color(0xFFDC2626)
                        Severity.MEDIUM -> Color(0xFFD97706)
                        Severity.LOW -> Color(0xFF16A34A)
                    }
                    NearbyAlertVerticalCard(
                        title = alert.title,
                        location = alert.locationName,
                        description = alert.description,
                        distance = "Nearby",
                        severity = when (alert.severity) {
                            Severity.HIGH -> "High"
                            Severity.MEDIUM -> "Medium"
                            Severity.LOW -> "Low"
                        },
                        severityColor = severityColor,
                        imageUrl = alert.imageUrl,
                        onClick = { onNavigateToDetail(alert.id) }
                    )
                }
            }
        }
        PullRefreshIndicator(
            refreshing = isRefreshing,
            state = pullRefreshState,
            modifier = Modifier.align(Alignment.TopCenter),
            contentColor = Color(0xFF1B4FD8)
        )
        } // end pullRefresh Box
    }
}

@Composable
fun MapPreviewWidget(
    userLatLng: LatLng?,
    alertsList: List<HazardReport>,
    onOpenFullMap: () -> Unit
) {
    // Keep old widget definition to avoid compilation errors elsewhere if referenced
}

@Composable
fun NearbyAlertVerticalCard(
    title: String,
    location: String,
    description: String,
    distance: String,
    severity: String,
    severityColor: Color,
    imageUrl: String?,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        colors = CardDefaults.cardColors(containerColor = Color.White),
        shape = RoundedCornerShape(8.dp),
        border = BorderStroke(1.dp, Color(0xFFE5E7EB))
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().height(IntrinsicSize.Min)
        ) {
            // Left edge 3px solid color bar
            Box(
                modifier = Modifier
                    .width(3.dp)
                    .fillMaxHeight()
                    .background(severityColor)
            )
            
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Thumbnail: 56x56px image, 6px radius
                Box(
                    modifier = Modifier
                        .size(56.dp)
                        .clip(RoundedCornerShape(6.dp))
                        .background(Color(0xFFF3F4F6)),
                    contentAlignment = Alignment.Center
                ) {
                    val model = if (!imageUrl.isNullOrBlank()) imageUrl else com.nagarrakshak.R.drawable.placeholder_hazard
                    coil.compose.AsyncImage(
                        model = model,
                        contentDescription = "Hazard Thumbnail",
                        modifier = Modifier.fillMaxSize(),
                        contentScale = androidx.compose.ui.layout.ContentScale.Crop
                    )
                }
                
                Spacer(modifier = Modifier.width(12.dp))
                
                // Middle details
                Column(
                    modifier = Modifier.weight(1f)
                ) {
                    Text(
                        text = title,
                        fontSize = 15.sp,
                        fontWeight = FontWeight.SemiBold,
                        color = Color(0xFF111827)
                    )
                    Spacer(modifier = Modifier.height(2.dp))
                    Text(
                        text = location,
                        fontSize = 12.sp,
                        color = Color(0xFF6B7280),
                        maxLines = 2,
                        overflow = TextOverflow.Ellipsis
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = description,
                        fontSize = 12.sp,
                        color = Color(0xFF9CA3AF),
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis
                    )
                }
                
                Spacer(modifier = Modifier.width(8.dp))
                
                // Right details column
                Column(
                    horizontalAlignment = Alignment.End,
                    verticalArrangement = Arrangement.spacedBy(4.dp)
                ) {
                    // Severity pill badge
                    Box(
                        modifier = Modifier
                            .background(severityColor, shape = RoundedCornerShape(4.dp))
                            .padding(horizontal = 8.dp, vertical = 2.dp)
                    ) {
                        Text(
                            text = severity,
                            color = Color.White,
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                    
                    // Nearby gray tag
                    Box(
                        modifier = Modifier
                            .background(Color(0xFFF3F4F6), shape = RoundedCornerShape(4.dp))
                            .border(1.dp, Color(0xFFE5E7EB), RoundedCornerShape(4.dp))
                            .padding(horizontal = 6.dp, vertical = 2.dp)
                    ) {
                        Text(
                            text = distance,
                            color = Color(0xFF6B7280),
                            fontSize = 9.sp,
                            fontWeight = FontWeight.Medium
                        )
                    }
                    
                    // Recent text
                    Text(
                        text = "Recent",
                        color = Color(0xFF6B7280),
                        fontSize = 10.sp
                    )
                }
            }
        }
    }
}

@Composable
fun SkeletonAlertCard() {
    val transition = rememberInfiniteTransition(label = "shimmer")
    val alpha by transition.animateFloat(
        initialValue = 0.3f,
        targetValue = 0.9f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = 800, easing = LinearEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "alpha"
    )

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .alpha(alpha),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        shape = RoundedCornerShape(8.dp),
        border = BorderStroke(1.dp, Color(0xFFE5E7EB))
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(56.dp)
                    .clip(RoundedCornerShape(6.dp))
                    .background(Color(0xFFE5E7EB))
            )
            Spacer(modifier = Modifier.width(12.dp))
            Column(
                modifier = Modifier.weight(1f),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                Box(modifier = Modifier.fillMaxWidth(0.6f).height(16.dp).background(Color(0xFFE5E7EB), shape = RoundedCornerShape(4.dp)))
                Box(modifier = Modifier.fillMaxWidth(0.4f).height(12.dp).background(Color(0xFFE5E7EB), shape = RoundedCornerShape(4.dp)))
                Box(modifier = Modifier.fillMaxWidth(0.9f).height(12.dp).background(Color(0xFFE5E7EB), shape = RoundedCornerShape(4.dp)))
            }
            Spacer(modifier = Modifier.width(8.dp))
            Column(
                horizontalAlignment = Alignment.End,
                verticalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier.height(56.dp)
            ) {
                Box(modifier = Modifier.size(width = 45.dp, height = 16.dp).background(Color(0xFFE5E7EB), shape = RoundedCornerShape(6.dp)))
                Box(modifier = Modifier.size(width = 50.dp, height = 16.dp).background(Color(0xFFE5E7EB), shape = RoundedCornerShape(6.dp)))
            }
        }
    }
}


@Composable
fun OpenDrainIllustration() {
    Canvas(modifier = Modifier.fillMaxSize()) {
        drawRect(Color(0xFFCBD5E1))
        val wallWidth = size.width * 0.2f
        drawRect(Color(0xFF94A3B8), size = size.copy(width = wallWidth))
        drawRect(Color(0xFF94A3B8), topLeft = Offset(size.width - wallWidth, 0f), size = size.copy(width = wallWidth))
        drawRect(Color(0xFF334155), topLeft = Offset(wallWidth, 0f), size = size.copy(width = size.width - 2 * wallWidth))
        drawRect(Color(0xFF4D7C0F), topLeft = Offset(wallWidth, size.height * 0.3f), size = size.copy(width = size.width - 2 * wallWidth, height = size.height * 0.7f))
        drawCircle(Color(0xFFEF4444), radius = 4.dp.toPx(), center = Offset(size.width * 0.45f, size.height * 0.5f))
        drawCircle(Color(0xFFF59E0B), radius = 3.dp.toPx(), center = Offset(size.width * 0.6f, size.height * 0.7f))
    }
}

@Composable
fun GarbageDumpIllustration() {
    Canvas(modifier = Modifier.fillMaxSize()) {
        drawRect(Color(0xFFE2E8F0))
        drawCircle(Color(0xFF475569), radius = 16.dp.toPx(), center = Offset(size.width * 0.35f, size.height * 0.65f))
        drawCircle(Color(0xFF334155), radius = 18.dp.toPx(), center = Offset(size.width * 0.6f, size.height * 0.7f))
        drawCircle(Color(0xFF1E293B), radius = 14.dp.toPx(), center = Offset(size.width * 0.48f, size.height * 0.75f))
        drawRect(Color(0xFFEF4444), topLeft = Offset(size.width * 0.2f, size.height * 0.8f), size = size.copy(width = 8.dp.toPx(), height = 6.dp.toPx()))
        drawCircle(Color(0xFF3B82F6), radius = 3.dp.toPx(), center = Offset(size.width * 0.75f, size.height * 0.8f))
    }
}

@Composable
fun WaterLoggingIllustration() {
    Canvas(modifier = Modifier.fillMaxSize()) {
        drawRect(Color(0xFF93C5FD))
        drawRect(Color(0xFF64748B), size = size.copy(height = size.height * 0.3f))
        drawRect(Color(0xFF1D4ED8), topLeft = Offset(0f, size.height * 0.3f), size = size.copy(height = size.height * 0.7f))
        val stroke = 1.5.dp.toPx()
        drawLine(Color.White, Offset(size.width * 0.2f, size.height * 0.5f), Offset(size.width * 0.5f, size.height * 0.5f), strokeWidth = stroke)
        drawLine(Color.White, Offset(size.width * 0.4f, size.height * 0.7f), Offset(size.width * 0.8f, size.height * 0.7f), strokeWidth = stroke)
    }
}

@Composable
fun BrokenStreetLightIllustration() {
    Canvas(modifier = Modifier.fillMaxSize()) {
        drawRect(Color(0xFF0F172A))
        drawLine(Color(0xFF64748B), Offset(size.width * 0.3f, size.height), Offset(size.width * 0.3f, size.height * 0.2f), strokeWidth = 3.dp.toPx())
        drawLine(Color(0xFF64748B), Offset(size.width * 0.3f, size.height * 0.2f), Offset(size.width * 0.6f, size.height * 0.2f), strokeWidth = 2.dp.toPx())
        drawRect(Color(0xFF475569), topLeft = Offset(size.width * 0.55f, size.height * 0.18f), size = size.copy(width = 12.dp.toPx(), height = 6.dp.toPx()))
        drawCircle(Color(0xFFEF4444), radius = 6.dp.toPx(), center = Offset(size.width * 0.6f, size.height * 0.5f))
        drawLine(Color(0xFFEF4444), Offset(size.width * 0.6f, size.height * 0.35f), Offset(size.width * 0.6f, size.height * 0.45f), strokeWidth = 2.dp.toPx())
    }
}

/**
 * Fetch home location (city name) using LocationManager and reverse geocoding.
 */
fun fetchHomeLocation(context: Context, onLocationInfo: (String, String) -> Unit) {
    try {
        val locationManager = context.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        val isGpsEnabled = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)
        val isNetworkEnabled = locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)
        
        var location: Location? = null
        if (isNetworkEnabled) {
            if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
            ) {
                location = locationManager.getLastKnownLocation(LocationManager.NETWORK_PROVIDER)
            }
        }
        if (location == null && isGpsEnabled) {
            if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                location = locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER)
            }
        }
        
        if (location != null) {
            val geocoder = Geocoder(context, Locale.getDefault())
            val addresses = geocoder.getFromLocation(location.latitude, location.longitude, 1)
            val cityName = addresses?.firstOrNull()?.locality ?: addresses?.firstOrNull()?.subAdminArea ?: "Chandigarh"
            val pincode = addresses?.firstOrNull()?.postalCode ?: "324005"
            onLocationInfo(cityName, pincode)
        } else {
            val provider = if (isNetworkEnabled) LocationManager.NETWORK_PROVIDER else LocationManager.GPS_PROVIDER
            if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
            ) {
                locationManager.requestSingleUpdate(provider, object : LocationListener {
                    override fun onLocationChanged(loc: Location) {
                        try {
                            val geocoder = Geocoder(context, Locale.getDefault())
                            val addresses = geocoder.getFromLocation(loc.latitude, loc.longitude, 1)
                            val cityName = addresses?.firstOrNull()?.locality ?: addresses?.firstOrNull()?.subAdminArea ?: "Chandigarh"
                            val pincode = addresses?.firstOrNull()?.postalCode ?: "324005"
                            onLocationInfo(cityName, pincode)
                        } catch (e: Exception) {
                            onLocationInfo("Chandigarh", "324005")
                        }
                    }
                    override fun onStatusChanged(provider: String?, status: Int, extras: Bundle?) {}
                    override fun onProviderEnabled(provider: String) {}
                    override fun onProviderDisabled(provider: String) {}
                }, Looper.getMainLooper())
            }
        }
    } catch (e: Exception) {
        onLocationInfo("Chandigarh", "324005")
    }
}

/**
 * Fetch current GPS location and invoke callback with LatLng.
 */
fun fetchCurrentLocationLatLng(context: Context, onLocationDetected: (LatLng) -> Unit) {
    try {
        val locationManager = context.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        val isGpsEnabled = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)
        val isNetworkEnabled = locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)
        
        var location: Location? = null
        if (isNetworkEnabled) {
            if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
            ) {
                location = locationManager.getLastKnownLocation(LocationManager.NETWORK_PROVIDER)
            }
        }
        if (location == null && isGpsEnabled) {
            if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                location = locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER)
            }
        }
        
        if (location != null) {
            onLocationDetected(LatLng(location.latitude, location.longitude))
        } else {
            val provider = if (isNetworkEnabled) LocationManager.NETWORK_PROVIDER else LocationManager.GPS_PROVIDER
            if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
            ) {
                locationManager.requestSingleUpdate(provider, object : LocationListener {
                    override fun onLocationChanged(loc: Location) {
                        onLocationDetected(LatLng(loc.latitude, loc.longitude))
                    }
                    override fun onStatusChanged(provider: String?, status: Int, extras: Bundle?) {}
                    override fun onProviderEnabled(provider: String) {}
                    override fun onProviderDisabled(provider: String) {}
                }, Looper.getMainLooper())
            }
        }
    } catch (e: Exception) {
        onLocationDetected(LatLng(25.18254, 75.82736))
    }
}

@Composable
fun PotholeIllustration() {
    Canvas(modifier = Modifier.fillMaxSize()) {
        drawRect(Color(0xFF64748B)) // Grey road
        drawOval(Color(0xFF334155), topLeft = Offset(size.width * 0.25f, size.height * 0.35f), size = size * 0.5f)
        val stroke = 1.5.dp.toPx()
        drawLine(Color(0xFF1E293B), Offset(size.width * 0.25f, size.height * 0.5f), Offset(size.width * 0.1f, size.height * 0.55f), strokeWidth = stroke)
        drawLine(Color(0xFF1E293B), Offset(size.width * 0.75f, size.height * 0.5f), Offset(size.width * 0.9f, size.height * 0.45f), strokeWidth = stroke)
    }
}

@Composable
fun MicLineIcon(
    color: Color = Color(0xFF6B7280),
    modifier: Modifier = Modifier
) {
    Canvas(modifier = modifier) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        // 1. Microphone center capsule
        val capsuleW = w * 0.35f
        val capsuleH = h * 0.55f
        val capsuleX = (w - capsuleW) / 2f
        val capsuleY = h * 0.15f
        drawRoundRect(
            color = color,
            topLeft = Offset(capsuleX, capsuleY),
            size = Size(capsuleW, capsuleH),
            cornerRadius = CornerRadius(capsuleW / 2f, capsuleW / 2f),
            style = Stroke(width = strokeWidth)
        )
        
        // 2. U-shaped stand line
        val standW = w * 0.55f
        val standH = h * 0.35f
        val standX = (w - standW) / 2f
        val standY = capsuleY + capsuleH * 0.4f
        drawArc(
            color = color,
            startAngle = 0f,
            sweepAngle = 180f,
            useCenter = false,
            topLeft = Offset(standX, standY),
            size = Size(standW, standH),
            style = Stroke(width = strokeWidth)
        )
        
        // 3. Vertical stem and horizontal base
        val stemTopY = standY + standH
        val stemBottomY = h * 0.9f
        drawLine(
            color = color,
            start = Offset(w / 2f, stemTopY),
            end = Offset(w / 2f, stemBottomY),
            strokeWidth = strokeWidth
        )
        
        val baseW = w * 0.3f
        drawLine(
            color = color,
            start = Offset((w - baseW) / 2f, stemBottomY),
            end = Offset((w + baseW) / 2f, stemBottomY),
            strokeWidth = strokeWidth
        )
    }
}





