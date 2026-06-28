package com.nagarrakshak.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.KeyboardArrowDown
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material.icons.filled.Close
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.nagarrakshak.data.BackendClient
import com.nagarrakshak.data.GoogleRouteInfo
import com.nagarrakshak.data.PlaceSuggestion
import com.nagarrakshak.data.models.HazardReport
import com.nagarrakshak.data.models.Severity
import com.nagarrakshak.data.models.VerificationStatus
import com.nagarrakshak.ui.theme.PrimaryColor
import com.google.android.gms.maps.model.CameraPosition
import com.google.android.gms.maps.model.LatLng
import com.google.android.gms.maps.model.BitmapDescriptorFactory
import com.google.maps.android.compose.GoogleMap
import com.google.maps.android.compose.Marker
import com.google.maps.android.compose.MarkerState
import com.google.maps.android.compose.Polyline
import com.google.maps.android.compose.rememberCameraPositionState
import com.google.maps.android.compose.MapProperties
import com.google.maps.android.compose.MapUiSettings
import com.google.maps.android.compose.MapType
import com.google.maps.android.compose.MarkerComposable
import com.google.maps.android.compose.rememberMarkerState
import com.google.maps.android.compose.Circle
import android.Manifest
import android.widget.Toast
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.ui.platform.LocalContext
import androidx.core.content.ContextCompat
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.graphics.StrokeCap
import androidx.compose.ui.graphics.StrokeJoin
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.RoundRect
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.geometry.Size
import androidx.compose.animation.core.*
import androidx.compose.ui.draw.scale
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

// Hides bottom navigation bar in MainActivity when in Ride Mode
object NavigationState {
    var isRideModeActive by mutableStateOf(false)
}

// Preset locations in Kota for user selection
data class PresetLocation(val name: String, val latLng: LatLng)

// Structure needed for DetailScreen map compatibility
data class HazardMarker(
    val id: String,
    val title: String,
    val latitude: Double,
    val longitude: Double,
    val severity: String,
    val snippet: String
)

@Composable
fun GoogleMapView(
    modifier: Modifier = Modifier,
    markers: List<HazardMarker>,
    centerLat: Double,
    centerLng: Double,
    zoom: Int = 13,
    showMyLocation: Boolean = false,
    onNavigateToDetail: (String) -> Unit
) {
    val center = LatLng(centerLat, centerLng)
    val cameraPositionState = rememberCameraPositionState {
        position = CameraPosition.fromLatLngZoom(center, zoom.toFloat())
    }

    LaunchedEffect(centerLat, centerLng) {
        cameraPositionState.position = CameraPosition.fromLatLngZoom(LatLng(centerLat, centerLng), zoom.toFloat())
    }

    GoogleMap(
        modifier = modifier,
        cameraPositionState = cameraPositionState,
        properties = MapProperties(isMyLocationEnabled = showMyLocation),
        uiSettings = MapUiSettings(myLocationButtonEnabled = showMyLocation)
    ) {
        markers.forEach { marker ->
            val position = LatLng(marker.latitude, marker.longitude)
            Marker(
                state = MarkerState(position = position),
                title = marker.title,
                snippet = marker.snippet,
                onClick = {
                    onNavigateToDetail(marker.id)
                    false
                }
            )
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MapScreen(onNavigateToDetail: (String) -> Unit) {
    val context = LocalContext.current
    var userLatLng by remember { mutableStateOf<LatLng?>(null) }
    
    // UI controls state
    var isTrafficEnabled by remember { mutableStateOf(false) }
    var isSatelliteEnabled by remember { mutableStateOf(false) }
    var isNavigationMode by remember { mutableStateOf(false) }
    var isRouteActive by remember { mutableStateOf(false) }
    var isAlertsEnabled by remember { mutableStateOf(true) }
    var isVoiceAlertsEnabled by remember { mutableStateOf(true) }
    var isVibrationEnabled by remember { mutableStateOf(true) }
    var isRouteAvoidEnabled by remember { mutableStateOf(true) }
    var tripSeconds by remember { mutableStateOf(0) }
    var selectedHazardForSheet by remember { mutableStateOf<HazardReport?>(null) }
    
    // Origin & Destination selection state
    var originLatLng by remember { mutableStateOf(LatLng(25.182, 75.828)) } // Default: Talwandi, Kota
    var originName by remember { mutableStateOf("Talwandi, Kota") }
    var destinationLatLng by remember { mutableStateOf(LatLng(25.166, 75.858)) } // Default: Indraprastha Ind. Area
    var destinationName by remember { mutableStateOf("Indraprastha Ind. Area") }

    var showOriginDialog by remember { mutableStateOf(false) }
    var showDestinationDialog by remember { mutableStateOf(false) }

    // Search & Filter state
    var searchQuery by remember { mutableStateOf("") }
    var selectedCategoryFilter by remember { mutableStateOf("All Issues") }
    var selectedSeverityFilter by remember { mutableStateOf("All Severity") }
    var selectedStatusFilter by remember { mutableStateOf("Live") }
    
    // Dropdown visibility state
    var isIssuesDropdownExpanded by remember { mutableStateOf(false) }
    var isSeverityDropdownExpanded by remember { mutableStateOf(false) }
    var isStatusDropdownExpanded by remember { mutableStateOf(false) }

    // Live hazards list
    var hazardsList by remember { mutableStateOf<List<HazardReport>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }

    // Directions / Route selection state
    var routesList by remember { mutableStateOf<List<GoogleRouteInfo>>(emptyList()) }
    var selectedRouteIndex by remember { mutableStateOf(0) }
    var isFetchingRoutes by remember { mutableStateOf(false) }

    // Location Permission Request
    val locationPermissionsLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        val fineGranted = permissions[Manifest.permission.ACCESS_FINE_LOCATION] ?: false
        val coarseGranted = permissions[Manifest.permission.ACCESS_COARSE_LOCATION] ?: false
        if (fineGranted || coarseGranted) {
            fetchCurrentLocationLatLng(context) { latLng ->
                userLatLng = latLng
            }
        }
    }

    LaunchedEffect(Unit) {
        val hasFine = ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
        val hasCoarse = ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
        if (hasFine || hasCoarse) {
            fetchCurrentLocationLatLng(context) { latLng ->
                userLatLng = latLng
            }
        } else {
            locationPermissionsLauncher.launch(
                arrayOf(Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION)
            )
        }
    }

    // Fetch hazards from backend
    LaunchedEffect(Unit) {
        isLoading = true
        hazardsList = BackendClient.fetchNearbyHazards()
        isLoading = false
    }

    // Fallback hazards matching seeder data
    val resolvedHazards = remember(hazardsList) {
        if (hazardsList.isNotEmpty()) {
            hazardsList
        } else {
            listOf(
                HazardReport(
                    id = "2",
                    title = "Open Drain",
                    category = "Open Drain",
                    locationName = "Talwandi, Kota",
                    latitude = 25.182,
                    longitude = 75.828,
                    severity = Severity.HIGH,
                    verificationStatus = VerificationStatus.VERIFIED,
                    verificationCount = 12,
                    reportTime = "2h ago",
                    description = "Open drain causing foul smell and mosquito issue."
                ),
                HazardReport(
                    id = "3",
                    title = "Garbage Dump",
                    category = "Garbage Dump",
                    locationName = "Mahaveer Nagar, Kota",
                    latitude = 25.172,
                    longitude = 75.842,
                    severity = Severity.LOW,
                    verificationStatus = VerificationStatus.VERIFIED,
                    verificationCount = 18,
                    reportTime = "5h ago",
                    description = "Garbage not collected from 3 days."
                ),
                HazardReport(
                    id = "4",
                    title = "Water Logging",
                    category = "Water Logging",
                    locationName = "Shrinath Puram, Kota",
                    latitude = 25.176,
                    longitude = 75.830,
                    severity = Severity.HIGH,
                    verificationStatus = VerificationStatus.VERIFIED,
                    verificationCount = 22,
                    reportTime = "6h ago",
                    description = "Heavy water logging after rain."
                ),
                HazardReport(
                    id = "5",
                    title = "Broken Street Light",
                    category = "Broken Street Light",
                    locationName = "Vivekananda Nagar, Kota",
                    latitude = 25.174,
                    longitude = 75.836,
                    severity = Severity.MEDIUM,
                    verificationStatus = VerificationStatus.PENDING,
                    verificationCount = 9,
                    reportTime = "1d ago",
                    description = "Street light is not working since 2 days."
                )
            )
        }
    }

    // Filter hazards based on selections
    val filteredHazards = remember(resolvedHazards, selectedCategoryFilter, selectedSeverityFilter, selectedStatusFilter, searchQuery) {
        resolvedHazards.filter { hazard ->
            val matchCategory = selectedCategoryFilter == "All Issues" || hazard.category.equals(selectedCategoryFilter, ignoreCase = true)
            
            val matchSeverity = when (selectedSeverityFilter) {
                "All Severity" -> true
                "High" -> hazard.severity == Severity.HIGH
                "Medium" -> hazard.severity == Severity.MEDIUM
                "Low" -> hazard.severity == Severity.LOW
                else -> true
            }
            
            val matchStatus = when (selectedStatusFilter) {
                "Live" -> true
                "Active" -> hazard.verificationStatus == VerificationStatus.PENDING
                "Verified" -> hazard.verificationStatus == VerificationStatus.VERIFIED
                else -> true
            }
            
            val matchSearch = searchQuery.isBlank() || 
                    hazard.title.contains(searchQuery, ignoreCase = true) || 
                    hazard.locationName.contains(searchQuery, ignoreCase = true)
            
            matchCategory && matchSeverity && matchStatus && matchSearch
        }
    }

    // Fetch Google Routes dynamically on Origin/Destination change
    LaunchedEffect(isRouteActive, originLatLng, destinationLatLng, resolvedHazards) {
        if (isRouteActive) {
            isFetchingRoutes = true
            val apiRoutes = BackendClient.fetchGoogleRoutes(originLatLng, destinationLatLng, resolvedHazards)
            if (apiRoutes.isNotEmpty()) {
                routesList = apiRoutes
                selectedRouteIndex = 0
            } else {
                // Draw real road-following route fallbacks between Talwandi and Indraprastha Ind. Area
                val path1 = listOf(
                    LatLng(25.182, 75.828), // Origin
                    LatLng(25.181, 75.832),
                    LatLng(25.176, 75.830), // Waterlogging
                    LatLng(25.174, 75.835), // Broken Streetlight
                    LatLng(25.172, 75.842), // Garbage Dump
                    LatLng(25.170, 75.848),
                    LatLng(25.166, 75.858)  // Destination
                )
                
                val path2 = listOf(
                    LatLng(25.182, 75.828), // Origin
                    LatLng(25.188, 75.825),
                    LatLng(25.192, 75.835),
                    LatLng(25.184, 75.850),
                    LatLng(25.174, 75.854),
                    LatLng(25.166, 75.858)  // Destination
                )

                val risk1 = BackendClient.calculateRouteRiskScore(path1, resolvedHazards)
                val risk2 = BackendClient.calculateRouteRiskScore(path2, resolvedHazards)

                val route1 = GoogleRouteInfo(
                    points = path1,
                    durationText = "28 min",
                    durationSeconds = 1680,
                    distanceText = "12.4 km",
                    distanceMeters = 12400,
                    riskScore = risk1
                )
                val route2 = GoogleRouteInfo(
                    points = path2,
                    durationText = "32 min",
                    durationSeconds = 1920,
                    distanceText = "14.2 km",
                    distanceMeters = 14200,
                    riskScore = risk2
                )

                // Put routes in list and sort by Safest Priority
                routesList = listOf(route1, route2)
                
                // Auto select safest route
                selectedRouteIndex = if (risk2 <= risk1) 1 else 0
            }
            isFetchingRoutes = false
        } else {
            routesList = emptyList()
            selectedRouteIndex = 0
            isFetchingRoutes = false
        }
    }

    val activeRoute = remember(routesList, selectedRouteIndex) {
        routesList.getOrNull(selectedRouteIndex)
    }

    val routePoints = remember(activeRoute) {
        activeRoute?.points ?: emptyList()
    }

    // Centering Map on Active Route bounds or user location
    val centerLatLng = remember(routePoints, userLatLng) {
        if (routePoints.isNotEmpty()) {
            val mid = routePoints[routePoints.size / 2]
            LatLng(mid.latitude, mid.longitude)
        } else {
            userLatLng ?: LatLng(25.18, 75.83)
        }
    }

    val cameraPositionState = rememberCameraPositionState {
        position = CameraPosition.fromLatLngZoom(centerLatLng, 14f)
    }

    // Move camera to user's real GPS location once detected
    LaunchedEffect(userLatLng) {
        if (userLatLng != null && !isRouteActive) {
            cameraPositionState.animate(
                com.google.android.gms.maps.CameraUpdateFactory.newLatLngZoom(userLatLng!!, 14.5f)
            )
        }
    }

    // Ride Navigation Mode Simulation variables
    var currentSimulationIndex by remember { mutableIntStateOf(0) }
    var currentSimulationLatLng by remember { mutableStateOf<LatLng?>(null) }
    var approachingHazard by remember { mutableStateOf<HazardReport?>(null) }
    var approachingHazardDistance by remember { mutableDoubleStateOf(0.0) }
    var isVoiceMuted by remember { mutableStateOf(false) }

    // TextToSpeech for voice alerts
    val tts = remember { mutableStateOf<android.speech.tts.TextToSpeech?>(null) }
    LaunchedEffect(Unit) {
        tts.value = android.speech.tts.TextToSpeech(context) { status ->
            if (status == android.speech.tts.TextToSpeech.SUCCESS) {
                tts.value?.language = java.util.Locale.US
            }
        }
    }

    DisposableEffect(Unit) {
        onDispose {
            tts.value?.shutdown()
        }
    }

    // Run active simulation when Ride Mode is triggered
    LaunchedEffect(isNavigationMode) {
        if (isNavigationMode) {
            tripSeconds = 0
            while (isNavigationMode) {
                delay(1000)
                tripSeconds++
            }
        }
    }

    LaunchedEffect(isNavigationMode, routePoints) {
        if (isNavigationMode && routePoints.isNotEmpty()) {
            currentSimulationIndex = 0
            val spokenHazardIds = mutableSetOf<String>()
            NavigationState.isRideModeActive = true

            while (isNavigationMode && currentSimulationIndex < routePoints.size) {
                val currentPoint = routePoints[currentSimulationIndex]
                currentSimulationLatLng = currentPoint
                
                // Move map camera smoothly along the ride path
                cameraPositionState.position = CameraPosition.fromLatLngZoom(currentPoint, 15.5f)

                // Scan for any hazard dynamically within threshold (e.g. 500m alert radius from dashboard settings)
                var closestHazard: HazardReport? = null
                var minDist = Double.MAX_VALUE
                
                for (hazard in filteredHazards) {
                    val dist = BackendClient.distanceInMeters(
                        hazard.latitude, hazard.longitude,
                        currentPoint.latitude, currentPoint.longitude
                    )
                    
                    // If alerts are enabled, check distance within 500m. If disabled, threshold is 0.
                    val threshold = if (isAlertsEnabled) 500.0 else 0.0
                    if (dist <= threshold) {
                        if (dist < minDist) {
                            minDist = dist
                            closestHazard = hazard
                        }
                    }
                }

                approachingHazard = closestHazard
                approachingHazardDistance = if (closestHazard != null) minDist else 0.0

                // Speak voice alert if hazard detected within 200m, and voice alerts are ON (isVoiceAlertsEnabled)
                if (closestHazard != null && minDist <= 200.0 && isVoiceAlertsEnabled) {
                    val id = closestHazard.id
                    if (!spokenHazardIds.contains(id)) {
                        spokenHazardIds.add(id)
                        val speechText = "Warning: ${closestHazard.title} ahead. Slow down immediately."
                        tts.value?.speak(speechText, android.speech.tts.TextToSpeech.QUEUE_FLUSH, null, null)
                    }
                }

                // Trigger device double-beep vibration alert if enabled when hazard within 200m
                if (closestHazard != null && minDist <= 200.0 && isVibrationEnabled) {
                    val vibrator = context.getSystemService(android.content.Context.VIBRATOR_SERVICE) as? android.os.Vibrator
                    if (vibrator != null && vibrator.hasVibrator()) {
                        val pattern = longArrayOf(0, 100, 100, 100)
                        if (android.os.Build.VERSION.SDK_INT >= 26) {
                            vibrator.vibrate(android.os.VibrationEffect.createWaveform(pattern, -1))
                        } else {
                            vibrator.vibrate(pattern, -1)
                        }
                    }
                }

                delay(3000)
                currentSimulationIndex++
            }
            // End Ride Mode
            isNavigationMode = false
            NavigationState.isRideModeActive = false
        } else {
            currentSimulationLatLng = null
            approachingHazard = null
            NavigationState.isRideModeActive = false
        }
    }

    // Enhanced Location Picker triggers
    if (showOriginDialog) {
        LocationSelectionBottomSheet(
            title = "Select Origin",
            initialLatLng = originLatLng,
            initialAddress = originName,
            userLatLng = userLatLng,
            onConfirm = { latLng, address ->
                originLatLng = latLng
                originName = address
                showOriginDialog = false
            },
            onDismiss = { showOriginDialog = false }
        )
    }

    if (showDestinationDialog) {
        LocationSelectionBottomSheet(
            title = "Select Destination",
            initialLatLng = destinationLatLng,
            initialAddress = destinationName,
            userLatLng = userLatLng,
            onConfirm = { latLng, address ->
                destinationLatLng = latLng
                destinationName = address
                // Set origin to user's current location automatically
                if (userLatLng != null) {
                    originLatLng = userLatLng!!
                    originName = "My Location"
                }
                isRouteActive = true
                showDestinationDialog = false
            },
            onDismiss = { showDestinationDialog = false }
        )
    }



    val totalCount = filteredHazards.size
    val viewportHazardsCount = remember(cameraPositionState.isMoving, filteredHazards) {
        val projection = cameraPositionState.projection
        val region = projection?.visibleRegion
        if (region != null) {
            filteredHazards.count { region.latLngBounds.contains(LatLng(it.latitude, it.longitude)) }
        } else {
            filteredHazards.size
        }
    }
    val highCount = filteredHazards.count { it.severity == Severity.HIGH }
    val mediumCount = filteredHazards.count { it.severity == Severity.MEDIUM }
    val lowCount = filteredHazards.count { it.severity == Severity.LOW }

    Box(modifier = Modifier.fillMaxSize()) {
        // 1. Live Google Map Component
        GoogleMap(
            modifier = Modifier.fillMaxSize(),
            cameraPositionState = cameraPositionState,
            properties = MapProperties(
                isMyLocationEnabled = userLatLng != null,
                isTrafficEnabled = isTrafficEnabled,
                mapType = if (isSatelliteEnabled) MapType.SATELLITE else MapType.NORMAL
            ),
            uiSettings = MapUiSettings(
                myLocationButtonEnabled = false,
                zoomControlsEnabled = false
            )
        ) {
            // Draw all loaded route alternatives on map
            routesList.forEachIndexed { index, route ->
                val isSelected = index == selectedRouteIndex
                Polyline(
                    points = route.points,
                    color = if (isSelected) Color(0xFF3B82F6) else Color(0xFFCBD5E1),
                    width = if (isSelected) 8f else 4f,
                    zIndex = if (isSelected) 1f else 0f
                )
            }

            // 2 km safety radius area visual circle
            Circle(
                center = userLatLng ?: centerLatLng,
                radius = 2000.0, // 2 km
                fillColor = Color(0x131B4FD8), // ~7% opacity Civic Blue
                strokeColor = Color(0x331B4FD8), // 20% opacity Civic Blue
                strokeWidth = 2.5f
            )

            // Render active simulation indicator on route if in ride mode (blue circle arrow)
            currentSimulationLatLng?.let { simLatLng ->
                MarkerComposable(
                    state = rememberMarkerState(position = simLatLng)
                ) {
                    Box(
                        modifier = Modifier
                            .size(32.dp)
                            .background(Color(0xFF1B4FD8), shape = CircleShape)
                            .border(3.dp, Color.White, CircleShape),
                        contentAlignment = Alignment.Center
                    ) {
                        Text("➔", color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            // Dynamic Animated Hazard Markers
            filteredHazards.forEach { hazard ->
                val position = LatLng(hazard.latitude, hazard.longitude)
                
                // Infinite Transition for the glowing pulse animation
                val infiniteTransition = rememberInfiniteTransition(label = "pulse_${hazard.id}")
                val pulseScale by infiniteTransition.animateFloat(
                    initialValue = 1.0f,
                    targetValue = 1.8f,
                    animationSpec = infiniteRepeatable(
                        animation = tween(1200, easing = LinearEasing),
                        repeatMode = RepeatMode.Restart
                    ),
                    label = "scale"
                )
                val pulseAlpha by infiniteTransition.animateFloat(
                    initialValue = 0.6f,
                    targetValue = 0.0f,
                    animationSpec = infiniteRepeatable(
                        animation = tween(1200, easing = LinearEasing),
                        repeatMode = RepeatMode.Restart
                    ),
                    label = "alpha"
                )
                
                val severityColor = when (hazard.severity) {
                    Severity.HIGH -> Color(0xFFDC2626)
                    Severity.MEDIUM -> Color(0xFFD97706)
                    Severity.LOW -> Color(0xFF16A34A)
                }

                if (isNavigationMode) {
                    // Mode 2 flag style pin
                    MarkerComposable(
                        state = rememberMarkerState(position = position),
                        title = hazard.title
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Box(
                                modifier = Modifier
                                    .size(28.dp)
                                    .background(severityColor, shape = RoundedCornerShape(4.dp))
                                    .border(1.dp, Color.White, RoundedCornerShape(4.dp)),
                                contentAlignment = Alignment.Center
                            ) {
                                Text("🚩", fontSize = 12.sp)
                            }
                        }
                    }
                } else {
                    // Mode 1 custom pulsing pin
                    MarkerComposable(
                        state = rememberMarkerState(position = position),
                        onClick = {
                            selectedHazardForSheet = hazard
                            true
                        }
                    ) {
                        Box(contentAlignment = Alignment.Center) {
                            // Fading expanding circular pulse
                            Box(
                                modifier = Modifier
                                    .size(48.dp)
                                    .scale(pulseScale)
                                    .background(severityColor.copy(alpha = pulseAlpha), shape = CircleShape)
                            )
                            
                            // Static inner severity pin
                            Box(
                                modifier = Modifier
                                    .size(28.dp)
                                    .background(severityColor, shape = CircleShape)
                                    .border(2.dp, Color.White, CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                val isCluster = hazard.verificationCount > 10
                                if (isCluster) {
                                    Text(
                                        text = (hazard.verificationCount / 2).toString(),
                                        color = Color.White,
                                        fontSize = 11.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                } else {
                                    Text(
                                        text = "!",
                                        color = Color.White,
                                        fontSize = 13.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                }
                            }
                        }
                    }
                }
            }

            // Draw route Endpoint Pin markers
            if (routePoints.isNotEmpty() && !isNavigationMode) {
                Marker(
                    state = MarkerState(position = routePoints.first()),
                    title = "Start: $originName",
                    icon = BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_BLUE)
                )
                Marker(
                    state = MarkerState(position = routePoints.last()),
                    title = "End: $destinationName",
                    icon = BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_ROSE)
                )
            }
        }

        // ==========================================
        // MODE 1: Normal View Overlays (Light Theme)
        // ==========================================
        if (!isNavigationMode) {
            // Title Header Bar
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.TopCenter)
                    .padding(16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(12.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 14.dp, vertical = 10.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Icon(
                        imageVector = Icons.Default.ArrowBack,
                        contentDescription = "Back",
                        tint = Color(0xFF111827),
                        modifier = Modifier
                            .size(20.dp)
                            .clickable { /* Go Back */ }
                    )
                    Spacer(modifier = Modifier.width(14.dp))
                    Column(modifier = Modifier.weight(1f)) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text(
                                text = "Hazard Map",
                                fontWeight = FontWeight.Bold,
                                fontSize = 17.sp,
                                color = Color(0xFF111827)
                            )
                            Spacer(modifier = Modifier.width(4.dp))
                            Icon(
                                imageVector = Icons.Default.KeyboardArrowDown,
                                contentDescription = "Dropdown",
                                tint = Color(0xFF111827),
                                modifier = Modifier.size(16.dp)
                            )
                        }
                        Text(
                            text = "$viewportHazardsCount hazards in this area",
                            fontSize = 11.sp,
                            color = Color(0xFF6B7280)
                        )
                    }
                    
                    IconButton(onClick = { /* Filter Toggle */ }) {
                        MapFilterIcon(color = Color(0xFF1B4FD8))
                    }
                    IconButton(onClick = { isSatelliteEnabled = !isSatelliteEnabled }) {
                        LayersIcon(color = Color(0xFF111827))
                    }
                }
            }

            // Search / Destination Planner Card
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.TopCenter)
                    .padding(top = 80.dp, start = 16.dp, end = 16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(12.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable { showDestinationDialog = true }
                        .padding(horizontal = 14.dp, vertical = 12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Icon(
                        imageVector = Icons.Default.Search,
                        contentDescription = "Search",
                        tint = Color(0xFF9CA3AF),
                        modifier = Modifier.size(20.dp)
                    )
                    Spacer(modifier = Modifier.width(10.dp))
                    Text(
                        text = if (isRouteActive) "Routing to: $destinationName" else "Search destination to navigate...",
                        color = if (isRouteActive) Color(0xFF111827) else Color(0xFF9CA3AF),
                        fontSize = 14.sp,
                        modifier = Modifier.weight(1f)
                    )
                    if (isRouteActive) {
                        IconButton(
                            onClick = {
                                isRouteActive = false
                                isNavigationMode = false
                                NavigationState.isRideModeActive = false
                            },
                            modifier = Modifier.size(20.dp)
                        ) {
                            Icon(
                                imageVector = Icons.Default.Close,
                                contentDescription = "Clear",
                                tint = Color(0xFF6B7280),
                                modifier = Modifier.size(16.dp)
                            )
                        }
                    }
                }
            }

            // Right side Floating Actions
            Column(
                modifier = Modifier
                    .align(Alignment.CenterEnd)
                    .padding(end = 16.dp, bottom = 48.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Current location target
                FloatingActionButton(
                    onClick = {
                        userLatLng?.let { latLng ->
                            cameraPositionState.position = CameraPosition.fromLatLngZoom(latLng, 15f)
                        }
                    },
                    containerColor = Color.White,
                    contentColor = Color(0xFF1B4FD8),
                    shape = CircleShape,
                    modifier = Modifier.size(46.dp),
                    elevation = FloatingActionButtonDefaults.elevation(0.dp)
                ) {
                    TargetLocationIcon(color = Color(0xFF1B4FD8), modifier = Modifier.size(20.dp))
                }

                // Report Hazard float icon
                FloatingActionButton(
                    onClick = { /* Handle report navigation */ },
                    containerColor = Color.White,
                    contentColor = Color(0xFFDC2626),
                    shape = CircleShape,
                    modifier = Modifier.size(46.dp),
                    elevation = FloatingActionButtonDefaults.elevation(0.dp)
                ) {
                    Icon(imageVector = Icons.Default.Warning, contentDescription = "Report", tint = Color(0xFF1B4FD8), modifier = Modifier.size(20.dp))
                }
            }

            // Bottom controls & Sheets Stack
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.BottomCenter)
                    .padding(bottom = 12.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                // Floating Drive Mode Button
                Button(
                    onClick = {
                        if (routePoints.isNotEmpty()) {
                            isNavigationMode = true
                        } else {
                            Toast.makeText(context, "No route available. Click a marker and choose Navigate Here first.", Toast.LENGTH_SHORT).show()
                        }
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8)),
                    shape = RoundedCornerShape(24.dp),
                    contentPadding = PaddingValues(horizontal = 20.dp, vertical = 10.dp)
                ) {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(6.dp)
                    ) {
                        Text("🚘", fontSize = 14.sp)
                        Text("Drive Mode", color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    }
                }

                // Clicked Hazard detail sheet card
                selectedHazardForSheet?.let { hazard ->
                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(horizontal = 16.dp),
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        shape = RoundedCornerShape(16.dp),
                        border = BorderStroke(1.dp, Color(0xFFE5E7EB))
                    ) {
                        Column(modifier = Modifier.padding(14.dp)) {
                            // Slide bar
                            Box(
                                modifier = Modifier
                                    .align(Alignment.CenterHorizontally)
                                    .width(36.dp)
                                    .height(4.dp)
                                    .background(Color(0xFFE5E7EB), shape = RoundedCornerShape(2.dp))
                            )
                            Spacer(modifier = Modifier.height(10.dp))

                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(12.dp)
                            ) {
                                // Thumbnail
                                Box(
                                    modifier = Modifier
                                        .size(68.dp)
                                        .clip(RoundedCornerShape(6.dp))
                                        .background(Color(0xFFF3F4F6)),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Text("📸", fontSize = 24.sp)
                                }

                                // Details
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(
                                        text = hazard.title,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 15.sp,
                                        color = Color(0xFF111827)
                                    )
                                    Spacer(modifier = Modifier.height(2.dp))
                                    Text(
                                        text = hazard.locationName,
                                        fontSize = 12.sp,
                                        color = Color(0xFF6B7280)
                                    )
                                    Spacer(modifier = Modifier.height(4.dp))

                                    Row(
                                        horizontalArrangement = Arrangement.spacedBy(6.dp),
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        // Distance
                                        val distanceMeters = if (userLatLng != null) {
                                            BackendClient.distanceInMeters(
                                                hazard.latitude, hazard.longitude,
                                                userLatLng!!.latitude, userLatLng!!.longitude
                                            )
                                        } else {
                                            BackendClient.distanceInMeters(
                                                hazard.latitude, hazard.longitude,
                                                centerLatLng.latitude, centerLatLng.longitude
                                            )
                                        }
                                        val distanceText = if (distanceMeters >= 1000) {
                                            String.format("📍 %.1f km away", distanceMeters / 1000.0)
                                        } else {
                                            String.format("📍 %d m away", distanceMeters.toInt())
                                        }
                                        Text(distanceText, fontSize = 10.sp, color = Color(0xFF6B7280))
                                        
                                        // Severity badge
                                        Box(
                                            modifier = Modifier
                                                .background(
                                                    color = when (hazard.severity) {
                                                        Severity.HIGH -> Color(0xFFFEE2E2)
                                                        Severity.MEDIUM -> Color(0xFFFEF3C7)
                                                        Severity.LOW -> Color(0xFFD1FAE5)
                                                    },
                                                    shape = RoundedCornerShape(4.dp)
                                                )
                                                .padding(horizontal = 6.dp, vertical = 2.dp)
                                        ) {
                                            Text(
                                                text = when (hazard.severity) {
                                                    Severity.HIGH -> "High"
                                                    Severity.MEDIUM -> "Medium"
                                                    Severity.LOW -> "Low"
                                                },
                                                color = when (hazard.severity) {
                                                    Severity.HIGH -> Color(0xFFDC2626)
                                                    Severity.MEDIUM -> Color(0xFFD97706)
                                                    Severity.LOW -> Color(0xFF16A34A)
                                                },
                                                fontSize = 9.sp,
                                                fontWeight = FontWeight.Bold
                                            )
                                        }

                                        // Time
                                        Text("🕒 ${hazard.reportTime}", fontSize = 10.sp, color = Color(0xFF6B7280))
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                text = hazard.description,
                                fontSize = 12.sp,
                                color = Color(0xFF374151)
                            )
                            Spacer(modifier = Modifier.height(10.dp))

                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(8.dp)
                            ) {
                                OutlinedButton(
                                    onClick = { onNavigateToDetail(hazard.id) },
                                    modifier = Modifier.weight(1f),
                                    shape = RoundedCornerShape(6.dp)
                                ) {
                                    Text("View Details", fontSize = 12.sp, fontWeight = FontWeight.Bold)
                                }
                                Button(
                                    onClick = {
                                        destinationLatLng = LatLng(hazard.latitude, hazard.longitude)
                                        destinationName = hazard.locationName
                                        selectedHazardForSheet = null
                                        isRouteActive = true
                                        isNavigationMode = true
                                    },
                                    modifier = Modifier.weight(1f),
                                    shape = RoundedCornerShape(6.dp),
                                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8))
                                ) {
                                    Text("Navigate Here", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color.White)
                                }
                            }
                        }
                    }
                }

                // Summary Stats Card
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(12.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB))
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 12.dp),
                        horizontalArrangement = Arrangement.SpaceAround
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = totalCount.toString(), fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Color(0xFF111827))
                            Text(text = "Total", fontSize = 11.sp, color = Color(0xFF6B7280))
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = highCount.toString(), fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Color(0xFFDC2626))
                            Text(text = "High", fontSize = 11.sp, color = Color(0xFFDC2626))
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = mediumCount.toString(), fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Color(0xFFD97706))
                            Text(text = "Medium", fontSize = 11.sp, color = Color(0xFFD97706))
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = lowCount.toString(), fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Color(0xFF16A34A))
                            Text(text = "Low", fontSize = 11.sp, color = Color(0xFF16A34A))
                        }
                    }
                }
            }
        }

        // ===========================================
        // MODE 2: Active Navigation Drive Dashboard
        // ===========================================
        if (isNavigationMode) {
            val currentSpeed = remember(tripSeconds) { 40 + (tripSeconds % 6) }
            val tripTimeText = remember(tripSeconds) {
                val h = tripSeconds / 3600
                val m = (tripSeconds % 3600) / 60
                val s = tripSeconds % 60
                String.format("%02d:%02d:%02d", h, m, s)
            }

            val navRoute = remember(routesList, selectedRouteIndex) {
                routesList.getOrNull(selectedRouteIndex)
            }
            val remainingPoints = remember(navRoute, currentSimulationIndex) {
                if (navRoute != null) (navRoute.points.size - currentSimulationIndex).coerceAtLeast(1) else 1
            }
            val totalPoints = remember(navRoute) {
                if (navRoute != null) navRoute.points.size.coerceAtLeast(1) else 1
            }
            val ratio = remainingPoints.toFloat() / totalPoints.toFloat()

            val remainingDistanceMeters = remember(navRoute, ratio) {
                if (navRoute != null) (navRoute.distanceMeters * ratio).toInt() else 0
            }
            val remainingDurationSeconds = remember(navRoute, ratio) {
                if (navRoute != null) (navRoute.durationSeconds * ratio).toInt() else 0
            }

            val distanceText = remember(remainingDistanceMeters) {
                if (remainingDistanceMeters >= 1000) {
                    String.format("%.1f km", remainingDistanceMeters / 1000.0)
                } else {
                    "$remainingDistanceMeters m"
                }
            }
            val etaText = remember(remainingDurationSeconds) {
                val min = remainingDurationSeconds / 60
                if (min >= 60) {
                    String.format("%02d:%02d hr", min / 60, min % 60)
                } else {
                    "$min min"
                }
            }
            val hazardsOnRouteCount = remember(filteredHazards, routePoints) {
                filteredHazards.count { hazard ->
                    routePoints.any { pt ->
                        BackendClient.distanceInMeters(hazard.latitude, hazard.longitude, pt.latitude, pt.longitude) <= 100.0
                    }
                }
            }

            // Top Floating Dashboard Panel
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.TopCenter)
                    .padding(16.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0xFF1E293B)),
                shape = RoundedCornerShape(12.dp)
            ) {
                Column {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(14.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        // Col 1: Speed
                        Column(horizontalAlignment = Alignment.Start) {
                            Text("DRIVE MODE", fontSize = 9.sp, color = Color(0xFFF1F5F9), fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(2.dp))
                            Row(verticalAlignment = Alignment.Bottom) {
                                Text(
                                    text = "$currentSpeed",
                                    fontSize = 26.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = Color.White
                                )
                                Spacer(modifier = Modifier.width(2.dp))
                                Text("km/h", fontSize = 10.sp, color = Color(0xFF94A3B8))
                            }
                        }

                        // Col 2: Alert Status
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            if (approachingHazard != null) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Text("⚠️", fontSize = 12.sp)
                                    Spacer(modifier = Modifier.width(4.dp))
                                    Text(
                                        text = "${approachingHazardDistance.toInt()}m ahead",
                                        fontSize = 13.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = Color(0xFFF59E0B)
                                    )
                                }
                                Text(
                                    text = "${approachingHazard!!.title} - ${approachingHazard!!.rawSeverity}",
                                    fontSize = 10.sp,
                                    color = Color(0xFFCBD5E1)
                                )
                            } else {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Text("🛡️", fontSize = 12.sp)
                                    Spacer(modifier = Modifier.width(4.dp))
                                    Text("All Clear", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color(0xFF10B981))
                                }
                                Text("No hazards detected on route", fontSize = 10.sp, color = Color(0xFF94A3B8))
                            }
                        }

                        // Col 3: Trip Time
                        Column(horizontalAlignment = Alignment.End) {
                            Text(
                                text = tripTimeText,
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color.White
                            )
                            Text("Trip Time", fontSize = 9.sp, color = Color(0xFF94A3B8))
                        }
                    }

                    // safety gradient progress bar separating panel from map
                    Canvas(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(4.dp)
                    ) {
                        val w = size.width
                        val h = size.height
                        // Draw horizontal safety color bar (Green -> Yellow -> Red)
                        drawRect(
                            brush = androidx.compose.ui.graphics.Brush.horizontalGradient(
                                colors = listOf(Color(0xFF10B981), Color(0xFFF59E0B), Color(0xFFEF4444))
                            ),
                            topLeft = Offset(0f, 0f),
                            size = Size(w, h)
                        )
                    }
                }
            }

            // Hazard Ahead Red Alert Banner (if hazard is detected)
            approachingHazard?.let { hazard ->
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .align(Alignment.TopCenter)
                        .padding(top = 110.dp, start = 16.dp, end = 16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFFDC2626)),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Row(
                        modifier = Modifier.padding(10.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text("⚠️", fontSize = 16.sp)
                            Spacer(modifier = Modifier.width(10.dp))
                            Column {
                                Text("HAZARD AHEAD", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color.White)
                                Text("${hazard.title} — ${approachingHazardDistance.toInt()}m ahead on route", fontSize = 11.sp, color = Color.White)
                            }
                        }
                        Text("${approachingHazardDistance.toInt()}m", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color.White)
                    }
                }
            }

            // Floating controls (volume and closest alert overlay)
            Column(
                modifier = Modifier
                    .align(Alignment.BottomEnd)
                    .padding(end = 16.dp, bottom = 280.dp),
                verticalArrangement = Arrangement.spacedBy(10.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Speaker Mute button
                FloatingActionButton(
                    onClick = {
                        isVoiceAlertsEnabled = !isVoiceAlertsEnabled
                        val msg = if (isVoiceAlertsEnabled) "Voice alerts enabled" else "Voice alerts disabled"
                        Toast.makeText(context, msg, Toast.LENGTH_SHORT).show()
                    },
                    containerColor = Color(0xFF1E293B),
                    contentColor = Color.White,
                    shape = CircleShape,
                    modifier = Modifier.size(46.dp),
                    elevation = FloatingActionButtonDefaults.elevation(0.dp)
                ) {
                    VolumeMuteIcon(color = Color.White, isMuted = !isVoiceAlertsEnabled)
                }
            }

            // Closest hazard HUD floating card
            approachingHazard?.let { hazard ->
                Box(
                    modifier = Modifier
                        .align(Alignment.BottomCenter)
                        .padding(bottom = 280.dp)
                ) {
                    Card(
                        colors = CardDefaults.cardColors(containerColor = Color(0xFF1E293B)),
                        shape = RoundedCornerShape(20.dp),
                        border = BorderStroke(1.dp, Color(0xFFDC2626))
                    ) {
                        Row(
                            modifier = Modifier.padding(horizontal = 14.dp, vertical = 8.dp),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            Box(
                                modifier = Modifier
                                    .size(24.dp)
                                    .background(Color(0xFFDC2626), shape = CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Text("⚠️", fontSize = 11.sp)
                            }
                            Column {
                                Text(hazard.title.uppercase(), fontSize = 11.sp, fontWeight = FontWeight.Bold, color = Color.White)
                                Text("${approachingHazardDistance.toInt()} METERS", fontSize = 10.sp, fontWeight = FontWeight.Bold, color = Color(0xFFEF4444))
                            }
                        }
                    }
                }
            }

            // Bottom dark dashboard container
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.BottomCenter),
                colors = CardDefaults.cardColors(containerColor = Color(0xFF0F172A)),
                shape = RoundedCornerShape(topStart = 16.dp, topEnd = 16.dp)
            ) {
                Column(
                    modifier = Modifier.padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(14.dp)
                ) {
                    // Row 1: Remaining distance, ETA, hazards count
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column {
                            Text("Distance", fontSize = 10.sp, color = Color(0xFF94A3B8))
                            Text(distanceText, fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color.White)
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text("ETA adj", fontSize = 10.sp, color = Color(0xFF94A3B8))
                            Text(etaText, fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color.White)
                        }
                        Column(horizontalAlignment = Alignment.End) {
                            Text("On Route", fontSize = 10.sp, color = Color(0xFF94A3B8))
                            Text("$hazardsOnRouteCount hazards", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color.White)
                        }
                    }

                    HorizontalDivider(color = Color(0xFF334155), thickness = 0.8.dp)

                    // Row 2: Hazard Alerts Config Toggle
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Box(
                                modifier = Modifier
                                    .size(36.dp)
                                    .background(Color(0xFF1E293B), shape = CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                MapBellIcon(color = Color(0xFFF59E0B), modifier = Modifier.size(18.dp))
                            }
                            Spacer(modifier = Modifier.width(10.dp))
                            Column {
                                Text("Hazard Alerts", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color.White)
                                Text("Alert when hazard within 500m", fontSize = 10.sp, color = Color(0xFF94A3B8))
                            }
                        }
                        
                        Switch(
                            checked = isAlertsEnabled,
                            onCheckedChange = { isAlertsEnabled = it },
                            colors = SwitchDefaults.colors(
                                checkedThumbColor = Color.White,
                                checkedTrackColor = Color(0xFFF59E0B)
                            )
                        )
                    }

                    // Row 3: Action toggles strip
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        // Voice Alerts Toggle Card
                        Card(
                            modifier = Modifier
                                .weight(1f)
                                .clickable {
                                    isVoiceAlertsEnabled = !isVoiceAlertsEnabled
                                },
                            colors = CardDefaults.cardColors(
                                containerColor = if (isVoiceAlertsEnabled) Color(0xFF1E293B) else Color(0xFF0B1329)
                            ),
                            shape = RoundedCornerShape(8.dp),
                            border = BorderStroke(1.dp, if (isVoiceAlertsEnabled) Color(0xFFF59E0B) else Color(0xFF1E293B))
                        ) {
                            Column(
                                modifier = Modifier.padding(8.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                MapSpeakerIcon(
                                    color = if (isVoiceAlertsEnabled) Color(0xFFF59E0B) else Color(0xFF64748B),
                                    modifier = Modifier.size(18.dp)
                                )
                                Spacer(modifier = Modifier.height(4.dp))
                                Text("Voice Alerts", fontSize = 9.sp, color = Color(0xFF94A3B8))
                                Text(
                                    text = if (isVoiceAlertsEnabled) "ON" else "OFF",
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = if (isVoiceAlertsEnabled) Color(0xFFF59E0B) else Color(0xFF64748B)
                                )
                            }
                        }

                        // Vibration Toggle Card
                        Card(
                            modifier = Modifier
                                .weight(1f)
                                .clickable {
                                    isVibrationEnabled = !isVibrationEnabled
                                },
                            colors = CardDefaults.cardColors(
                                containerColor = if (isVibrationEnabled) Color(0xFF1E293B) else Color(0xFF0B1329)
                            ),
                            shape = RoundedCornerShape(8.dp),
                            border = BorderStroke(1.dp, if (isVibrationEnabled) Color(0xFFF59E0B) else Color(0xFF1E293B))
                        ) {
                            Column(
                                modifier = Modifier.padding(8.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                VibrationIcon(
                                    color = if (isVibrationEnabled) Color(0xFFF59E0B) else Color(0xFF64748B),
                                    modifier = Modifier.size(18.dp)
                                )
                                Spacer(modifier = Modifier.height(4.dp))
                                Text("Vibration", fontSize = 9.sp, color = Color(0xFF94A3B8))
                                Text(
                                    text = if (isVibrationEnabled) "ON" else "OFF",
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = if (isVibrationEnabled) Color(0xFFF59E0B) else Color(0xFF64748B)
                                )
                            }
                        }

                        // Route Avoid Toggle Card
                        Card(
                            modifier = Modifier
                                .weight(1f)
                                .clickable {
                                    isRouteAvoidEnabled = !isRouteAvoidEnabled
                                },
                            colors = CardDefaults.cardColors(
                                containerColor = if (isRouteAvoidEnabled) Color(0xFF1E293B) else Color(0xFF0B1329)
                            ),
                            shape = RoundedCornerShape(8.dp),
                            border = BorderStroke(1.dp, if (isRouteAvoidEnabled) Color(0xFFF59E0B) else Color(0xFF1E293B))
                        ) {
                            Column(
                                modifier = Modifier.padding(8.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                RouteAvoidIcon(
                                    color = if (isRouteAvoidEnabled) Color(0xFFF59E0B) else Color(0xFF64748B),
                                    modifier = Modifier.size(18.dp)
                                )
                                Spacer(modifier = Modifier.height(4.dp))
                                Text("Route Avoid", fontSize = 9.sp, color = Color(0xFF94A3B8))
                                Text(
                                    text = if (isRouteAvoidEnabled) "ON" else "OFF",
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = if (isRouteAvoidEnabled) Color(0xFFF59E0B) else Color(0xFF64748B)
                                )
                            }
                        }
                    }

                    Spacer(modifier = Modifier.height(2.dp))

                    // End Trip Red Button
                    Button(
                        onClick = {
                            isNavigationMode = false
                            NavigationState.isRideModeActive = false
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFDC2626)),
                        shape = RoundedCornerShape(8.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(48.dp)
                    ) {
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            // Stop square icon representation
                            Box(
                                modifier = Modifier
                                    .size(14.dp)
                                    .border(2.dp, Color.White, RoundedCornerShape(2.dp))
                            )
                            Text("End Trip", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color.White)
                        }
                    }
                }
            }
        }
    }
}

// Interactive Location Picker Bottom Sheet (Full Height, Google Places Search, Draggable Mini Map, Pin Drop)
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LocationSelectionBottomSheet(
    title: String,
    initialLatLng: LatLng,
    initialAddress: String,
    userLatLng: LatLng?,
    onConfirm: (LatLng, String) -> Unit,
    onDismiss: () -> Unit
) {
    val context = LocalContext.current
    val coroutineScope = rememberCoroutineScope()
    val sheetState = rememberModalBottomSheetState(skipPartiallyExpanded = true)

    var searchQuery by remember { mutableStateOf("") }
    var selectedLatLng by remember { mutableStateOf(initialLatLng) }
    var selectedAddress by remember { mutableStateOf(initialAddress) }
    var suggestions by remember { mutableStateOf<List<PlaceSuggestion>>(emptyList()) }
    
    var isPinDropped by remember { mutableStateOf(false) }
    var isGeocoding by remember { mutableStateOf(false) }

    // Autocomplete Places API query
    LaunchedEffect(searchQuery) {
        if (searchQuery.isNotBlank() && searchQuery.length >= 3) {
            delay(400) // debounce
            val results = BackendClient.fetchPlaceSuggestions(searchQuery)
            suggestions = results
        } else {
            suggestions = emptyList()
        }
    }

    val cameraPositionState = rememberCameraPositionState {
        position = CameraPosition.fromLatLngZoom(selectedLatLng, 15f)
    }

    // Centering Map on selection
    LaunchedEffect(selectedLatLng) {
        cameraPositionState.animate(
            com.google.android.gms.maps.CameraUpdateFactory.newLatLngZoom(selectedLatLng, 15.5f)
        )
    }

    val popularPlaces = remember {
        listOf(
            PresetLocation("Vivekananda Nagar, Kota", LatLng(25.174, 75.836)),
            PresetLocation("Nayapura, Kota", LatLng(25.200, 75.855)),
            PresetLocation("Kunhadi, Kota", LatLng(25.215, 75.840))
        )
    }

    val savedPlaces = remember {
        listOf(
            PresetLocation("Home (Talwandi)", LatLng(25.182, 75.828)),
            PresetLocation("Work (Indraprastha)", LatLng(25.166, 75.858))
        )
    }

    ModalBottomSheet(
        onDismissRequest = onDismiss,
        sheetState = sheetState,
        containerColor = Color.White,
        dragHandle = { BottomSheetDefaults.DragHandle(color = Color(0xFFCBD5E1)) }
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.White)
        ) {
            // Header Row
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 8.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = title,
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp,
                    color = Color(0xFF0F172A)
                )
                Box(
                    modifier = Modifier
                        .size(32.dp)
                        .background(Color(0xFFF1F5F9), shape = CircleShape)
                        .clickable { onDismiss() },
                    contentAlignment = Alignment.Center
                ) {
                    Text("✕", fontSize = 14.sp, color = Color(0xFF475569), fontWeight = FontWeight.Bold)
                }
            }

            // Draggable Mini Map Preview Box
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(200.dp)
                    .background(Color(0xFFF1F5F9))
            ) {
                GoogleMap(
                    modifier = Modifier.fillMaxSize(),
                    cameraPositionState = cameraPositionState,
                    uiSettings = MapUiSettings(
                        zoomControlsEnabled = false,
                        myLocationButtonEnabled = false,
                        compassEnabled = false
                    ),
                    onMapLongClick = { latLng ->
                        selectedLatLng = latLng
                        isPinDropped = true
                        isGeocoding = true
                        coroutineScope.launch {
                            selectedAddress = BackendClient.reverseGeocode(latLng)
                            isGeocoding = false
                        }
                    },
                    onMapClick = { latLng ->
                        selectedLatLng = latLng
                        isPinDropped = true
                        isGeocoding = true
                        coroutineScope.launch {
                            selectedAddress = BackendClient.reverseGeocode(latLng)
                            isGeocoding = false
                        }
                    }
                ) {
                    Marker(
                        state = MarkerState(position = selectedLatLng),
                        title = if (isPinDropped) "Dropped Pin" else "Selected Location",
                        icon = BitmapDescriptorFactory.defaultMarker(
                            if (isPinDropped) BitmapDescriptorFactory.HUE_RED else BitmapDescriptorFactory.HUE_GREEN
                        )
                    )
                }

                // Helper overlay label
                Box(
                    modifier = Modifier
                        .align(Alignment.TopCenter)
                        .padding(8.dp)
                        .background(Color(0x99000000), shape = RoundedCornerShape(12.dp))
                        .padding(horizontal = 12.dp, vertical = 4.dp)
                ) {
                    Text(
                        text = "Tap or long press map to drop a pin",
                        color = Color.White,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Medium
                    )
                }
            }

            // Search input Bar
            OutlinedTextField(
                value = searchQuery,
                onValueChange = { searchQuery = it },
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                placeholder = { Text("Search for a place, address or landmark", fontSize = 13.sp) },
                leadingIcon = {
                    Icon(
                        imageVector = Icons.Default.Search,
                        contentDescription = "Search icon",
                        tint = Color(0xFF64748B),
                        modifier = Modifier.size(20.dp)
                    )
                },
                trailingIcon = {
                    if (searchQuery.isNotEmpty()) {
                        IconButton(onClick = { searchQuery = "" }) {
                            Text("✕", color = Color(0xFF64748B), fontWeight = FontWeight.Bold, fontSize = 14.sp)
                        }
                    }
                },
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = Color(0xFF16A34A),
                    unfocusedBorderColor = Color(0xFFCBD5E1),
                    focusedContainerColor = Color(0xFFF8FAFC),
                    unfocusedContainerColor = Color(0xFFF8FAFC)
                ),
                shape = RoundedCornerShape(12.dp),
                singleLine = true
            )

            // Auto-complete suggestion results OR Predefined list shortcuts
            Box(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxWidth()
            ) {
                if (searchQuery.isNotEmpty()) {
                    if (suggestions.isEmpty()) {
                        Box(
                            modifier = Modifier.fillMaxSize(),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                text = if (searchQuery.length >= 3) "Searching..." else "Type at least 3 characters",
                                color = Color(0xFF64748B),
                                fontSize = 13.sp
                            )
                        }
                    } else {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(horizontal = 16.dp)
                        ) {
                            suggestions.forEach { suggestion ->
                                Row(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable {
                                            isGeocoding = true
                                            coroutineScope.launch {
                                                val details = BackendClient.fetchPlaceDetails(suggestion.placeId)
                                                if (details != null) {
                                                    selectedLatLng = details.first
                                                    selectedAddress = details.second
                                                    searchQuery = "" // Clear search box
                                                }
                                                isGeocoding = false
                                            }
                                        }
                                        .padding(vertical = 12.dp, horizontal = 4.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text("📍", fontSize = 14.sp)
                                    Spacer(modifier = Modifier.width(10.dp))
                                    Text(
                                        text = suggestion.description,
                                        fontSize = 13.sp,
                                        color = Color(0xFF0F172A),
                                        fontWeight = FontWeight.Medium,
                                        maxLines = 2,
                                        overflow = TextOverflow.Ellipsis
                                    )
                                }
                                HorizontalDivider(color = Color(0xFFF1F5F9))
                            }
                        }
                    }
                } else {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(horizontal = 16.dp),
                        verticalArrangement = Arrangement.spacedBy(16.dp)
                    ) {
                        // Quick Action: Use Current GPS Location
                        Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable {
                                        val gpsLatLng = userLatLng ?: LatLng(25.182, 75.828)
                                        selectedLatLng = gpsLatLng
                                        isGeocoding = true
                                        coroutineScope.launch {
                                            selectedAddress = BackendClient.reverseGeocode(gpsLatLng)
                                            isGeocoding = false
                                        }
                                    }
                                    .border(1.dp, Color(0xFFE2E8F0), RoundedCornerShape(12.dp))
                                    .padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(12.dp)
                            ) {
                                Box(
                                    modifier = Modifier
                                        .size(32.dp)
                                        .background(Color(0xFFDCFCE7), shape = CircleShape),
                                    contentAlignment = Alignment.Center
                                ) {
                                    TargetLocationIcon(color = Color(0xFF16A34A), modifier = Modifier.size(16.dp))
                                }
                                Column {
                                    Text("Use Current Location", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color(0xFF16A34A))
                                    Text("Center on your GPS coordinates", fontSize = 11.sp, color = Color(0xFF64748B))
                                }
                            }

                            // Dynamic tip alert
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .background(Color(0xFFF8FAFC), shape = RoundedCornerShape(12.dp))
                                    .padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(8.dp)
                            ) {
                                Text("📍", fontSize = 14.sp)
                                Text(
                                    text = "Or drag the map and tap to drop a custom location pin.",
                                    color = Color(0xFF475569),
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Medium
                                )
                            }
                        }

                        // Saved Places Row List
                        Column(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                            Text("Saved Places", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFF64748B))
                            savedPlaces.forEach { place ->
                                SavedOrPopularPlaceRow(
                                    name = place.name,
                                    icon = "🏠",
                                    onClick = {
                                        selectedLatLng = place.latLng
                                        selectedAddress = place.name
                                    }
                                )
                            }
                        }

                        // Popular Landmarks Row List
                        Column(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                            Text("Popular Places", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFF64748B))
                            popularPlaces.forEach { place ->
                                SavedOrPopularPlaceRow(
                                    name = place.name,
                                    icon = "🔥",
                                    onClick = {
                                        selectedLatLng = place.latLng
                                        selectedAddress = place.name
                                    }
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                    }
                }
            }

            // Confirmation Address footer Card
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(topStart = 16.dp, topEnd = 16.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0))
            ) {
                Column(
                    modifier = Modifier.padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    Column {
                        Text(
                            text = if (isPinDropped) "Dropped Pin Location" else "Selected Address",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF64748B)
                        )
                        Text(
                            text = if (isGeocoding) "Resolving address..." else selectedAddress,
                            fontSize = 13.sp,
                            color = Color(0xFF0F172A),
                            fontWeight = FontWeight.Bold,
                            maxLines = 2,
                            overflow = TextOverflow.Ellipsis
                        )
                    }

                    Button(
                        onClick = { onConfirm(selectedLatLng, selectedAddress) },
                        modifier = Modifier.fillMaxWidth(),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF16A34A)),
                        shape = RoundedCornerShape(12.dp),
                        contentPadding = PaddingValues(vertical = 12.dp),
                        enabled = !isGeocoding
                    ) {
                        val action = if (title.contains("Origin", ignoreCase = true)) "Confirm Origin" else "Confirm Destination"
                        Text(action, color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                    }
                }
            }
        }
    }
}

@Composable
fun SavedOrPopularPlaceRow(
    name: String,
    icon: String,
    onClick: () -> Unit
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() }
            .padding(vertical = 8.dp, horizontal = 4.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(10.dp)
    ) {
        Box(
            modifier = Modifier
                .size(28.dp)
                .background(Color(0xFFF1F5F9), shape = CircleShape),
            contentAlignment = Alignment.Center
        ) {
            Text(icon, fontSize = 12.sp)
        }
        Text(
            text = name,
            fontSize = 13.sp,
            color = Color(0xFF0F172A),
            fontWeight = FontWeight.Medium
        )
    }
    HorizontalDivider(color = Color(0xFFF1F5F9))
}

// Material 3 styling chip wrapper
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun M3FilterChip(
    text: String,
    selected: Boolean,
    onClick: () -> Unit
) {
    FilterChip(
        selected = selected,
        onClick = onClick,
        label = { Text(text, fontSize = 12.sp, fontWeight = FontWeight.Bold) },
        trailingIcon = {
            Text("▼", color = if (selected) Color(0xFF16A34A) else Color(0xFF94A3B8), fontSize = 8.sp)
        },
        shape = CircleShape,
        colors = FilterChipDefaults.filterChipColors(
            containerColor = Color.White,
            labelColor = Color(0xFF475569),
            selectedContainerColor = Color(0xFFDCFCE7),
            selectedLabelColor = Color(0xFF16A34A)
        ),
        border = FilterChipDefaults.filterChipBorder(
            enabled = true,
            selected = selected,
            borderColor = Color(0xFFE2E8F0),
            selectedBorderColor = Color(0xFF16A34A),
            borderWidth = 1.dp,
            selectedBorderWidth = 1.dp
        )
    )
}

@Composable
fun MapFloatingControl(
    onClick: () -> Unit,
    content: @Composable () -> Unit
) {
    Box(
        modifier = Modifier
            .size(36.dp)
            .background(Color.White, shape = RoundedCornerShape(8.dp))
            .border(1.dp, Color(0xFFCBD5E1), RoundedCornerShape(8.dp))
            .clickable { onClick() },
        contentAlignment = Alignment.Center
    ) {
        content()
    }
}

@Composable
fun HazardBottomSheetContent(
    hazard: HazardReport,
    onNavigate: () -> Unit,
    onViewDetails: () -> Unit
) {
    val severityColor = when (hazard.severity) {
        Severity.HIGH -> Color(0xFFB91C1C)
        Severity.MEDIUM -> Color(0xFFB45309)
        Severity.LOW -> Color(0xFF15803D)
    }
    val severityBg = when (hazard.severity) {
        Severity.HIGH -> Color(0xFFFEE2E2)
        Severity.MEDIUM -> Color(0xFFFEF3C7)
        Severity.LOW -> Color(0xFFDCFCE7)
    }

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = hazard.title,
                fontWeight = FontWeight.Bold,
                fontSize = 18.sp,
                color = Color(0xFF0F172A)
            )
            Box(
                modifier = Modifier
                    .background(severityBg, shape = RoundedCornerShape(8.dp))
                    .padding(horizontal = 8.dp, vertical = 4.dp)
            ) {
                Text(
                    text = if (hazard.rawSeverity.contains("Critical", ignoreCase = true)) "Critical Risk" else "${hazard.severity.name.lowercase().replaceFirstChar { it.uppercase() }} Risk",
                    color = severityColor,
                    fontWeight = FontWeight.Bold,
                    fontSize = 10.sp
                )
            }
        }

        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(4.dp)
        ) {
            Text("📍", fontSize = 12.sp)
            Text(
                text = hazard.locationName,
                fontSize = 12.sp,
                color = Color(0xFF64748B)
            )
        }

        Text(
            text = hazard.description,
            fontSize = 13.sp,
            color = Color(0xFF334155)
        )

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Button(
                onClick = onNavigate,
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF16A34A)),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier.weight(1f)
            ) {
                Text("Navigate Here", color = Color.White, fontWeight = FontWeight.Bold)
            }
            
            OutlinedButton(
                onClick = onViewDetails,
                border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier.weight(1f),
                colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF475569))
            ) {
                Text("View Details", fontWeight = FontWeight.Bold)
            }
        }
    }
}

// ----------------------------------------------------
// Custom Canvas Vector Icons
// ----------------------------------------------------

@Composable
private fun MapFilterIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF16A34A)) {
    Canvas(modifier = modifier.size(12.dp)) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        val path = Path().apply {
            moveTo(0f, 0f)
            lineTo(w, 0f)
            lineTo(w * 0.6f, h * 0.5f)
            lineTo(w * 0.6f, h * 0.9f)
            lineTo(w * 0.4f, h * 0.7f)
            lineTo(w * 0.4f, h * 0.5f)
            close()
        }
        drawPath(
            path = path,
            color = color,
            style = Stroke(
                width = strokeWidth,
                join = StrokeJoin.Round,
                cap = StrokeCap.Round
            )
        )
    }
}

@Composable
private fun SwapIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF475569)) {
    Canvas(modifier = modifier.size(14.dp)) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        drawLine(color, Offset(w * 0.3f, h * 0.15f), Offset(w * 0.3f, h * 0.85f), strokeWidth)
        val pathUp = Path().apply {
            moveTo(w * 0.15f, h * 0.3f)
            lineTo(w * 0.3f, h * 0.15f)
            lineTo(w * 0.45f, h * 0.3f)
        }
        drawPath(pathUp, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round, join = StrokeJoin.Round))

        drawLine(color, Offset(w * 0.7f, h * 0.15f), Offset(w * 0.7f, h * 0.85f), strokeWidth)
        val pathDown = Path().apply {
            moveTo(w * 0.55f, h * 0.7f)
            lineTo(w * 0.7f, h * 0.85f)
            lineTo(w * 0.85f, h * 0.7f)
        }
        drawPath(pathDown, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round, join = StrokeJoin.Round))
    }
}

@Composable
private fun PlayIcon(modifier: Modifier = Modifier, color: Color = Color.White) {
    Canvas(modifier = modifier.size(12.dp)) {
        val w = size.width
        val h = size.height
        
        val path = Path().apply {
            moveTo(w * 0.2f, h * 0.1f)
            lineTo(w * 0.85f, h * 0.5f)
            lineTo(w * 0.2f, h * 0.9f)
            close()
        }
        drawPath(path, color)
    }
}

@Composable
private fun LayersIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF475569)) {
    Canvas(modifier = modifier.size(20.dp)) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        val path1 = Path().apply {
            moveTo(w * 0.5f, h * 0.15f)
            lineTo(w * 0.85f, h * 0.35f)
            lineTo(w * 0.5f, h * 0.55f)
            lineTo(w * 0.15f, h * 0.35f)
            close()
        }
        drawPath(path1, color, style = Stroke(width = strokeWidth))
        
        val path2 = Path().apply {
            moveTo(w * 0.15f, h * 0.5f)
            lineTo(w * 0.5f, h * 0.7f)
            lineTo(w * 0.85f, h * 0.5f)
        }
        drawPath(path2, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round, join = StrokeJoin.Round))
        
        val path3 = Path().apply {
            moveTo(w * 0.15f, h * 0.65f)
            lineTo(w * 0.5f, h * 0.85f)
            lineTo(w * 0.85f, h * 0.65f)
        }
        drawPath(path3, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round, join = StrokeJoin.Round))
    }
}

@Composable
private fun TargetLocationIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF475569)) {
    Canvas(modifier = modifier.size(20.dp)) {
        val strokeWidth = 1.8.dp.toPx()
        val w = size.width
        val h = size.height
        val cx = w / 2f
        val cy = h / 2f
        
        drawCircle(color, radius = cx * 0.6f, style = Stroke(width = strokeWidth))
        drawCircle(color, radius = cx * 0.2f)
        
        drawLine(color, Offset(cx, 0f), Offset(cx, cy * 0.4f), strokeWidth)
        drawLine(color, Offset(cx, h), Offset(cx, h - cy * 0.4f), strokeWidth)
        drawLine(color, Offset(0f, cy), Offset(cx * 0.4f, cy), strokeWidth)
        drawLine(color, Offset(w, cy), Offset(w - cx * 0.4f, cy), strokeWidth)
    }
}

@Composable
private fun CompassIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF475569)) {
    Canvas(modifier = modifier.size(20.dp)) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        val cx = w / 2f
        val cy = h / 2f
        
        drawCircle(color, radius = cx * 0.8f, style = Stroke(width = strokeWidth))
        
        val needlePath = Path().apply {
            moveTo(cx, h * 0.2f)
            lineTo(cx + w * 0.15f, cy)
            lineTo(cx, h * 0.8f)
            lineTo(cx - w * 0.15f, cy)
            close()
        }
        drawPath(needlePath, color, style = Stroke(width = strokeWidth))
        
        val topNeedle = Path().apply {
            moveTo(cx, h * 0.2f)
            lineTo(cx + w * 0.15f, cy)
            lineTo(cx, cy)
            close()
        }
        drawPath(topNeedle, Color(0xFFEF4444))
    }
}

@Composable
private fun NavigationTurnRightIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF16A34A)) {
    Canvas(modifier = modifier.size(28.dp)) {
        val strokeWidth = 3.dp.toPx()
        val w = size.width
        val h = size.height
        
        val path = Path().apply {
            moveTo(w * 0.25f, h * 0.85f)
            lineTo(w * 0.25f, h * 0.4f)
            quadraticBezierTo(w * 0.25f, h * 0.25f, w * 0.4f, h * 0.25f)
            lineTo(w * 0.75f, h * 0.25f)
        }
        drawPath(path, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round, join = StrokeJoin.Round))
        
        val arrowHead = Path().apply {
            moveTo(w * 0.6f, h * 0.12f)
            lineTo(w * 0.78f, h * 0.25f)
            lineTo(w * 0.6f, h * 0.38f)
        }
        drawPath(arrowHead, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round, join = StrokeJoin.Round))
    }
}

@Composable
private fun ShareIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF16A34A)) {
    Canvas(modifier = modifier.size(16.dp)) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        drawCircle(color, radius = 2.dp.toPx(), center = Offset(w * 0.75f, h * 0.2f))
        drawCircle(color, radius = 2.dp.toPx(), center = Offset(w * 0.25f, h * 0.5f))
        drawCircle(color, radius = 2.dp.toPx(), center = Offset(w * 0.75f, h * 0.8f))
        
        drawLine(color, Offset(w * 0.35f, h * 0.45f), Offset(w * 0.65f, h * 0.25f), strokeWidth)
        drawLine(color, Offset(w * 0.35f, h * 0.55f), Offset(w * 0.65f, h * 0.75f), strokeWidth)
    }
}

@Composable
private fun VolumeMuteIcon(modifier: Modifier = Modifier, color: Color, isMuted: Boolean) {
    Canvas(modifier = modifier.size(16.dp)) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        // Speaker base
        val path = Path().apply {
            moveTo(w * 0.15f, h * 0.35f)
            lineTo(w * 0.35f, h * 0.35f)
            lineTo(w * 0.6f, h * 0.15f)
            lineTo(w * 0.6f, h * 0.85f)
            lineTo(w * 0.35f, h * 0.65f)
            lineTo(w * 0.15f, h * 0.65f)
            close()
        }
        drawPath(path, color, style = Stroke(width = strokeWidth, join = StrokeJoin.Round))
        
        if (!isMuted) {
            // Wave
            val wave = Path().apply {
                moveTo(w * 0.75f, h * 0.35f)
                quadraticBezierTo(w * 0.85f, h * 0.5f, w * 0.75f, h * 0.65f)
            }
            drawPath(wave, color, style = Stroke(width = strokeWidth, cap = StrokeCap.Round))
        } else {
            // Diagonal line through speaker
            drawLine(Color(0xFFEF4444), Offset(w * 0.1f, h * 0.1f), Offset(w * 0.9f, h * 0.9f), strokeWidth)
        }
    }
}

@Composable
fun VibrationIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width
        val h = size.height
        val strokeWidth = 2.dp.toPx()
        drawRoundRect(
            color = color,
            topLeft = Offset(w * 0.35f, h * 0.15f),
            size = Size(w * 0.3f, h * 0.7f),
            cornerRadius = CornerRadius(2.dp.toPx(), 2.dp.toPx()),
            style = Stroke(width = strokeWidth)
        )
        drawArc(
            color = color,
            startAngle = 120f,
            sweepAngle = 120f,
            useCenter = false,
            topLeft = Offset(w * 0.1f, h * 0.25f),
            size = Size(w * 0.2f, h * 0.5f),
            style = Stroke(width = strokeWidth, cap = StrokeCap.Round)
        )
        drawArc(
            color = color,
            startAngle = -60f,
            sweepAngle = 120f,
            useCenter = false,
            topLeft = Offset(w * 0.7f, h * 0.25f),
            size = Size(w * 0.2f, h * 0.5f),
            style = Stroke(width = strokeWidth, cap = StrokeCap.Round)
        )
    }
}

@Composable
fun RouteAvoidIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width
        val h = size.height
        val strokeWidth = 2.dp.toPx()
        val path = Path().apply {
            moveTo(w * 0.2f, h * 0.8f)
            quadraticBezierTo(w * 0.5f, h * 0.8f, w * 0.5f, h * 0.5f)
            quadraticBezierTo(w * 0.5f, h * 0.2f, w * 0.8f, h * 0.2f)
        }
        drawPath(path = path, color = color, style = Stroke(width = strokeWidth))
        drawLine(
            color = Color(0xFFEF4444),
            start = Offset(w * 0.4f, h * 0.4f),
            end = Offset(w * 0.6f, h * 0.6f),
            strokeWidth = strokeWidth * 1.5f
        )
        drawLine(
            color = Color(0xFFEF4444),
            start = Offset(w * 0.6f, h * 0.4f),
            end = Offset(w * 0.4f, h * 0.6f),
            strokeWidth = strokeWidth * 1.5f
        )
    }
}

@Composable
fun MapBellIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width
        val h = size.height
        val strokeWidth = 1.8.dp.toPx()
        val path = Path().apply {
            moveTo(w * 0.5f, h * 0.15f)
            quadraticBezierTo(w * 0.7f, h * 0.25f, w * 0.7f, h * 0.6f)
            lineTo(w * 0.8f, h * 0.75f)
            lineTo(w * 0.2f, h * 0.75f)
            lineTo(w * 0.3f, h * 0.6f)
            quadraticBezierTo(w * 0.3f, h * 0.25f, w * 0.5f, h * 0.15f)
            close()
        }
        drawPath(path = path, color = color, style = Stroke(width = strokeWidth))
        drawArc(
            color = color,
            startAngle = 0f,
            sweepAngle = 180f,
            useCenter = false,
            topLeft = Offset(w * 0.42f, h * 0.75f),
            size = Size(w * 0.16f, h * 0.12f)
        )
    }
}

@Composable
fun MapSpeakerIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width
        val h = size.height
        val path = Path().apply {
            moveTo(w * 0.2f, h * 0.35f)
            lineTo(w * 0.45f, h * 0.35f)
            lineTo(w * 0.75f, h * 0.15f)
            lineTo(w * 0.75f, h * 0.85f)
            lineTo(w * 0.45f, h * 0.65f)
            lineTo(w * 0.2f, h * 0.65f)
            close()
        }
        drawPath(path = path, color = color)
    }
}
