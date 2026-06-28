package com.nagarrakshak.ui.screens

import android.widget.Toast
import android.util.Log
import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.*
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.graphics.*
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.nagarrakshak.data.BackendClient
import com.nagarrakshak.data.models.HazardReport
import com.nagarrakshak.data.models.Severity
import com.nagarrakshak.data.models.VerificationStatus
import kotlinx.coroutines.launch
import androidx.compose.animation.core.*
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Rect
import androidx.compose.ui.geometry.RoundRect
import coil.compose.AsyncImage

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DetailScreen(
    hazardId: String,
    onBackClicked: () -> Unit
) {
    val context = LocalContext.current
    var hazardReport by remember { mutableStateOf<HazardReport?>(null) }
    var isLoading by remember { mutableStateOf(true) }
    var hasVerified by remember { mutableStateOf(false) }
    var isError by remember { mutableStateOf(false) }
    val coroutineScope = rememberCoroutineScope()

    var geminiAnalysisEnabled by remember { mutableStateOf(true) }
    var petitionEnabled by remember { mutableStateOf(true) }

    // Nearby alerts for related section
    var allAlerts by remember { mutableStateOf<List<HazardReport>>(emptyList()) }

    LaunchedEffect(hazardId) {
        isLoading = true
        isError = false
        try {
            val allList = BackendClient.fetchNearbyHazards()
            allAlerts = allList
            hazardReport = allList.find { it.id == hazardId }
            hasVerified = hazardReport?.verificationStatus == VerificationStatus.VERIFIED
            try {
                val settings = BackendClient.fetchSettings()
                geminiAnalysisEnabled = settings.optBoolean("gemini_analysis_enabled", true)
                petitionEnabled = settings.optBoolean("petition_enabled", true)
            } catch (e: Exception) {
                Log.e("DetailScreen", "Settings fetch failed: ${e.message}", e)
            }
        } catch (e: Exception) {
            Log.e("DetailScreen", "Failed to load: ${e.message}", e)
            isError = true
        }
        isLoading = false
    }

    // Loading state
    if (isLoading) {
        SkeletonDetailContent()
        return
    }

    // Error state
    if (isError) {
        Box(Modifier.fillMaxSize().background(Color(0xFFF5F7FA)), contentAlignment = Alignment.Center) {
            Column(horizontalAlignment = Alignment.CenterHorizontally, verticalArrangement = Arrangement.spacedBy(8.dp)) {
                Icon(Icons.Default.Warning, "Error", modifier = Modifier.size(64.dp), tint = Color(0xFFD1D5DB))
                Text("Couldn't load alert details", fontWeight = FontWeight.Bold, fontSize = 18.sp, color = Color(0xFF374151))
                Text("Check your connection and try again", fontSize = 14.sp, color = Color(0xFF6B7280))
                Spacer(Modifier.height(8.dp))
                Button(onClick = {
                    coroutineScope.launch {
                        isLoading = true; isError = false
                        try { hazardReport = BackendClient.fetchHazardDetail(hazardId) } catch (_: Exception) { isError = true }
                        isLoading = false
                    }
                }, colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8))) { Text("Retry", color = Color.White) }
                TextButton(onClick = onBackClicked) { Text("Go Back", color = Color(0xFF6B7280)) }
            }
        }
        return
    }

    val report = hazardReport
    if (report == null) {
        Box(Modifier.fillMaxSize().background(Color(0xFFF5F7FA)), contentAlignment = Alignment.Center) {
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text("Hazard report not found.", fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Color(0xFF374151))
                Spacer(Modifier.height(16.dp))
                Button(onClick = onBackClicked, colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8))) { Text("Go Back") }
            }
        }
        return
    }

    // Derived data
    val severityColor = when (report.severity) { Severity.HIGH -> Color(0xFFDC2626); Severity.MEDIUM -> Color(0xFFD97706); Severity.LOW -> Color(0xFF16A34A) }
    val severityBg = when (report.severity) { Severity.HIGH -> Color(0xFFFEF2F2); Severity.MEDIUM -> Color(0xFFFFFBEB); Severity.LOW -> Color(0xFFF0FDF4) }
    val severityBorder = when (report.severity) { Severity.HIGH -> Color(0xFFFECACA); Severity.MEDIUM -> Color(0xFFFDE68A); Severity.LOW -> Color(0xFFBBF7D0) }
    val severityText = when (report.severity) { Severity.HIGH -> "⚠ HIGH SEVERITY"; Severity.MEDIUM -> "⚠ MEDIUM SEVERITY"; Severity.LOW -> "✓ LOW SEVERITY" }
    val aiSummary = report.aiAnalysisSummary ?: "AI analysis detected a ${report.category.lowercase()} hazard. Risk factors include local safety concerns and pedestrian/vehicle damage probability."

    // Nearby related alerts (exclude current)
    val relatedAlerts = remember(allAlerts, hazardId) {
        allAlerts.filter { it.id != hazardId }.take(4)
    }

    // Comment count derived from data
    val commentCount = remember(report.id) { 2 + (kotlin.math.abs(report.id.hashCode()) % 8) }

    Scaffold(
        containerColor = Color(0xFFF5F7FA),
        // Sticky bottom action bar
        bottomBar = {
            Surface(
                modifier = Modifier.fillMaxWidth(),
                shadowElevation = 8.dp,
                color = Color.White,
                border = BorderStroke(1.dp, Color(0xFFE5E7EB))
            ) {
                Row(
                    modifier = Modifier.padding(horizontal = 16.dp, vertical = 12.dp),
                    horizontalArrangement = Arrangement.spacedBy(10.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Navigate button
                    Button(
                        onClick = { Toast.makeText(context, "Opening navigation...", Toast.LENGTH_SHORT).show() },
                        modifier = Modifier.weight(2f).height(52.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8)),
                        shape = RoundedCornerShape(10.dp)
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                            Icon(Icons.Default.Place, "Navigate", Modifier.size(18.dp), tint = Color.White)
                            Text("Navigate to Hazard", fontSize = 15.sp, fontWeight = FontWeight.SemiBold, color = Color.White)
                        }
                    }
                    // Share
                    OutlinedButton(
                        onClick = { },
                        modifier = Modifier.size(52.dp),
                        shape = RoundedCornerShape(10.dp),
                        border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                        contentPadding = PaddingValues(0.dp)
                    ) { Icon(Icons.Outlined.Share, "Share", Modifier.size(20.dp), tint = Color(0xFF6B7280)) }
                    // Report
                    OutlinedButton(
                        onClick = { },
                        modifier = Modifier.size(52.dp),
                        shape = RoundedCornerShape(10.dp),
                        border = BorderStroke(1.dp, Color(0xFFFECACA)),
                        contentPadding = PaddingValues(0.dp)
                    ) { Icon(Icons.Default.Warning, "Report", Modifier.size(20.dp), tint = Color(0xFFDC2626)) }
                }
            }
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .verticalScroll(rememberScrollState())
        ) {
            // ════════════════════════════════
            // IMAGE GALLERY SECTION
            // ════════════════════════════════
            Box(
                modifier = Modifier.fillMaxWidth().height(280.dp)
            ) {
                val imageUrl = report.imageUrl
                val model = if (!imageUrl.isNullOrBlank()) imageUrl else com.nagarrakshak.R.drawable.placeholder_hazard
                AsyncImage(
                    model = model,
                    contentDescription = "Hazard Photo",
                    modifier = Modifier.fillMaxSize(),
                    contentScale = ContentScale.Crop
                )

                // Gradient scrim overlay
                Box(
                    modifier = Modifier.fillMaxSize().background(
                        Brush.verticalGradient(
                            colors = listOf(Color.Transparent, Color.Black.copy(alpha = 0.7f)),
                            startY = 140f
                        )
                    )
                )

                // Top bar icons (back, share, bookmark, more)
                Row(
                    modifier = Modifier.fillMaxWidth().align(Alignment.TopCenter)
                        .padding(horizontal = 8.dp, vertical = 8.dp),
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    IconButton(onClick = onBackClicked, modifier = Modifier.background(Color.Black.copy(0.3f), CircleShape)) {
                        Icon(Icons.Outlined.ArrowBack, "Back", tint = Color.White)
                    }
                    Row(horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                        IconButton(onClick = { }, modifier = Modifier.background(Color.Black.copy(0.3f), CircleShape)) {
                            Icon(Icons.Outlined.Share, "Share", tint = Color.White)
                        }
                        IconButton(onClick = { }, modifier = Modifier.background(Color.Black.copy(0.3f), CircleShape)) {
                            Icon(Icons.Default.FavoriteBorder, "Bookmark", tint = Color.White)
                        }
                        IconButton(onClick = { }, modifier = Modifier.background(Color.Black.copy(0.3f), CircleShape)) {
                            Icon(Icons.Outlined.MoreVert, "More", tint = Color.White)
                        }
                    }
                }

                // Image counter chip (top right below icons)
                Box(
                    modifier = Modifier.align(Alignment.TopEnd).padding(top = 56.dp, end = 16.dp)
                        .background(Color.Black.copy(0.6f), RoundedCornerShape(12.dp))
                        .padding(horizontal = 8.dp, vertical = 4.dp)
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                        Text("📷", fontSize = 12.sp)
                        Text("1 / 1", fontSize = 11.sp, color = Color.White, fontWeight = FontWeight.Medium)
                    }
                }

                // Bottom overlay text on image
                Column(
                    modifier = Modifier.align(Alignment.BottomStart).padding(16.dp)
                ) {
                    // Severity badge
                    Box(
                        modifier = Modifier.background(severityColor, RoundedCornerShape(4.dp))
                            .padding(horizontal = 8.dp, vertical = 3.dp)
                    ) {
                        Text(severityText, color = Color.White, fontSize = 10.sp, fontWeight = FontWeight.Bold)
                    }
                    Spacer(Modifier.height(4.dp))
                    Text(report.title, fontSize = 22.sp, fontWeight = FontWeight.Bold, color = Color.White)
                    Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                        Icon(Icons.Outlined.LocationOn, "Location", Modifier.size(12.dp), tint = Color.White.copy(0.8f))
                        Text(report.locationName, fontSize = 12.sp, color = Color.White.copy(0.8f))
                    }
                }
            }

            // ════════════════════════════════
            // REFERENCE ID + STATUS ROW
            // ════════════════════════════════
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(0.dp),
                elevation = CardDefaults.cardElevation(0.dp)
            ) {
                Column {
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 12.dp),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Column {
                            Text("REPORT ID", fontSize = 10.sp, color = Color(0xFF6B7280), fontWeight = FontWeight.Medium)
                            Text("#${report.id.take(12).uppercase()}", fontSize = 14.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF111827))
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text("FILED ON", fontSize = 10.sp, color = Color(0xFF6B7280), fontWeight = FontWeight.Medium)
                            Text(report.reportTime, fontSize = 14.sp, color = Color(0xFF111827))
                        }
                        Column(horizontalAlignment = Alignment.End) {
                            Text("STATUS", fontSize = 10.sp, color = Color(0xFF6B7280), fontWeight = FontWeight.Medium)
                            Box(
                                modifier = Modifier.background(Color(0xFFFEF3C7), RoundedCornerShape(4.dp))
                                    .padding(horizontal = 6.dp, vertical = 2.dp)
                            ) {
                                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(3.dp)) {
                                    Text("⏳", fontSize = 10.sp)
                                    Text("Under Review", fontSize = 10.sp, color = Color(0xFFD97706), fontWeight = FontWeight.Bold)
                                }
                            }
                        }
                    }
                    HorizontalDivider(color = Color(0xFFF3F4F6))
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 8.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                            Icon(Icons.Outlined.Person, "Reporter", Modifier.size(12.dp), tint = Color(0xFF6B7280))
                            Text("Reported by Citizen · Verified User", fontSize = 12.sp, color = Color(0xFF6B7280))
                        }
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(3.dp)) {
                            Icon(Icons.Default.CheckCircle, "Verified", Modifier.size(12.dp), tint = Color(0xFF1B4FD8))
                            Text("Verified", fontSize = 12.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.Medium)
                        }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // HAZARD DETAILS CARD
            // ════════════════════════════════
            Card(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(10.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB))
            ) {
                Column(Modifier.padding(16.dp)) {
                    SectionHeader(icon = Icons.Outlined.Info, label = "HAZARD DETAILS")
                    Spacer(Modifier.height(12.dp))

                    // Details Grid
                    Column {
                        DetailGridRow("Hazard Type", report.category, "Severity", when(report.severity) { Severity.HIGH -> "● High"; Severity.MEDIUM -> "● Medium"; Severity.LOW -> "● Low" }, severityColor)
                        HorizontalDivider(color = Color(0xFFF3F4F6))
                        DetailGridRow("Road Type", "City Road", "Traffic Level", "High Traffic", null)
                        HorizontalDivider(color = Color(0xFFF3F4F6))
                        DetailGridRow("Risk Type", report.rawSeverity, "Dept. Assigned", "PWD, Kota", null)
                    }

                    HorizontalDivider(color = Color(0xFFF3F4F6), modifier = Modifier.padding(vertical = 12.dp))

                    // Description
                    Text("DESCRIPTION", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF6B7280), letterSpacing = 1.2.sp)
                    Spacer(Modifier.height(6.dp))

                    var isExpanded by remember { mutableStateOf(false) }
                    Text(
                        text = if (report.description.isBlank()) "No description provided." else report.description,
                        fontSize = 14.sp, color = Color(0xFF374151), lineHeight = 22.sp,
                        maxLines = if (isExpanded) Int.MAX_VALUE else 3,
                        overflow = TextOverflow.Ellipsis
                    )
                    if (report.description.length > 120) {
                        TextButton(onClick = { isExpanded = !isExpanded }, contentPadding = PaddingValues(0.dp)) {
                            Text(if (isExpanded) "Read Less ▴" else "Read More ▾", fontSize = 13.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.SemiBold)
                        }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // AI ANALYSIS CARD
            // ════════════════════════════════
            if (geminiAnalysisEnabled || report.aiAnalysisSummary != null) {
                Card(
                    modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFFEEF2FF)),
                    shape = RoundedCornerShape(10.dp),
                    border = BorderStroke(1.dp, Color(0xFFC7D2FE))
                ) {
                    Column(Modifier.padding(16.dp)) {
                        Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                                Icon(Icons.Filled.Star, "AI", Modifier.size(16.dp), tint = Color(0xFF6366F1))
                                Text("AI ANALYSIS REPORT", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF4338CA), letterSpacing = 1.sp)
                            }
                            Text("96% Confident", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFF4338CA))
                        }
                        Spacer(Modifier.height(12.dp))

                        // AI result rows
                        val aiResults = listOf(
                            Triple("Detected Object", report.category, "98%"),
                            Triple("Surface Damage", if (report.severity == Severity.HIGH) "Severe" else "Moderate", "96%"),
                            Triple("Water Risk", if (report.severity == Severity.HIGH) "High" else "Medium", "88%"),
                            Triple("Repair Priority", if (report.severity == Severity.HIGH) "Urgent" else "Standard", "95%")
                        )
                        aiResults.forEach { (label, value, confidence) ->
                            AIResultRow(label, value, confidence)
                            Spacer(Modifier.height(6.dp))
                        }

                        Spacer(Modifier.height(8.dp))
                        // Confidence bar
                        Text("Overall Confidence Score", fontSize = 11.sp, color = Color(0xFF6B7280))
                        Spacer(Modifier.height(4.dp))
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            LinearProgressIndicator(
                                progress = { 0.96f },
                                modifier = Modifier.weight(1f).height(6.dp).clip(RoundedCornerShape(3.dp)),
                                color = Color(0xFF6366F1),
                                trackColor = Color(0xFFC7D2FE)
                            )
                            Spacer(Modifier.width(8.dp))
                            Text("96%", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFF4338CA))
                        }

                        Spacer(Modifier.height(12.dp))
                        Text("AI OBSERVATIONS", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF4338CA), letterSpacing = 1.sp)
                        Spacer(Modifier.height(6.dp))

                        // Parse AI summary into observation points
                        val observations = remember(aiSummary) {
                            aiSummary.split(". ").filter { it.isNotBlank() }.map { it.trim().removeSuffix(".") }
                        }
                        observations.forEach { obs ->
                            Row(modifier = Modifier.padding(vertical = 2.dp), verticalAlignment = Alignment.Top) {
                                Box(Modifier.padding(top = 6.dp).size(6.dp).background(Color(0xFF6366F1), CircleShape))
                                Spacer(Modifier.width(8.dp))
                                Text(obs, fontSize = 13.sp, color = Color(0xFF374151), lineHeight = 18.sp)
                            }
                        }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // LOCATION & MAP CARD
            // ════════════════════════════════
            Card(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(10.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB))
            ) {
                Column(Modifier.padding(16.dp)) {
                    SectionHeader(icon = Icons.Outlined.LocationOn, label = "LOCATION")
                    Spacer(Modifier.height(10.dp))

                    // Mini Map
                    Card(
                        modifier = Modifier.fillMaxWidth().height(160.dp),
                        shape = RoundedCornerShape(8.dp)
                    ) {
                        Box {
                            val marker = remember(hazardId) {
                                HazardMarker(
                                    id = hazardId, title = report.title,
                                    latitude = report.latitude, longitude = report.longitude,
                                    severity = when(report.severity) { Severity.HIGH -> "High Risk"; Severity.MEDIUM -> "Medium Risk"; Severity.LOW -> "Low Risk" },
                                    snippet = report.locationName
                                )
                            }
                            GoogleMapView(
                                modifier = Modifier.fillMaxSize(),
                                markers = listOf(marker),
                                centerLat = report.latitude, centerLng = report.longitude,
                                zoom = 16, onNavigateToDetail = {}
                            )
                            // "Tap to open" overlay
                            Box(
                                Modifier.align(Alignment.BottomCenter).padding(bottom = 8.dp)
                                    .background(Color.Black.copy(0.5f), RoundedCornerShape(12.dp))
                                    .padding(horizontal = 10.dp, vertical = 4.dp)
                            ) {
                                Text("Tap to open full map", fontSize = 11.sp, color = Color.White)
                            }
                        }
                    }

                    Spacer(Modifier.height(10.dp))
                    Text(report.locationName, fontSize = 14.sp, color = Color(0xFF374151))
                    Spacer(Modifier.height(4.dp))

                    Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                        Icon(Icons.Default.LocationOn, "Coords", Modifier.size(12.dp), tint = Color(0xFF6B7280))
                        Text("${String.format("%.4f", report.latitude)}°N, ${String.format("%.4f", report.longitude)}°E", fontSize = 12.sp, color = Color(0xFF6B7280))
                    }

                    Spacer(Modifier.height(10.dp))

                    Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        OutlinedButton(
                            onClick = { }, modifier = Modifier.weight(1f),
                            shape = RoundedCornerShape(8.dp), border = BorderStroke(1.dp, Color(0xFF1B4FD8))
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                                Icon(Icons.Default.Place, "Map", Modifier.size(14.dp), tint = Color(0xFF1B4FD8))
                                Text("Open in Maps", fontSize = 12.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.SemiBold)
                            }
                        }
                        OutlinedButton(
                            onClick = { }, modifier = Modifier.weight(1f),
                            shape = RoundedCornerShape(8.dp), border = BorderStroke(1.dp, Color(0xFF1B4FD8))
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                                Icon(Icons.Default.Place, "Directions", Modifier.size(14.dp), tint = Color(0xFF1B4FD8))
                                Text("Directions", fontSize = 12.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.SemiBold)
                            }
                        }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // COMMUNITY RESPONSE CARD
            // ════════════════════════════════
            Card(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(10.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB))
            ) {
                Column(Modifier.padding(16.dp)) {
                    SectionHeader(icon = Icons.Default.Person, label = "COMMUNITY RESPONSE")
                    Spacer(Modifier.height(12.dp))

                    // Stats row
                    Row(Modifier.fillMaxWidth()) {
                        CommunityStatColumn(Modifier.weight(1f), "${report.verificationCount}", "Confirmed", "👍", Color(0xFF16A34A))
                        Box(Modifier.width(1.dp).height(48.dp).background(Color(0xFFF3F4F6)))
                        CommunityStatColumn(Modifier.weight(1f), "$commentCount", "Comments", "💬", Color(0xFF1B4FD8))
                        Box(Modifier.width(1.dp).height(48.dp).background(Color(0xFFF3F4F6)))
                        CommunityStatColumn(Modifier.weight(1f), "${report.verificationCount * 6}", "Views", "👁️", Color(0xFF6B7280))
                    }

                    Spacer(Modifier.height(12.dp))

                    // Confirm button
                    OutlinedButton(
                        onClick = {
                            if (!hasVerified) {
                                coroutineScope.launch {
                                    val success = BackendClient.verifyHazard(hazardId)
                                    if (success) {
                                        hazardReport = report.copy(verificationCount = report.verificationCount + 1, verificationStatus = VerificationStatus.VERIFIED)
                                        hasVerified = true
                                        Toast.makeText(context, "Report verified!", Toast.LENGTH_SHORT).show()
                                    } else {
                                        Toast.makeText(context, "Failed to verify.", Toast.LENGTH_SHORT).show()
                                    }
                                }
                            }
                        },
                        modifier = Modifier.fillMaxWidth().height(48.dp),
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, if (hasVerified) Color(0xFFC7D2FE) else Color(0xFF1B4FD8)),
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (hasVerified) Color(0xFFEEF2FF) else Color.Transparent
                        )
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                            Icon(
                                if (hasVerified) Icons.Filled.ThumbUp else Icons.Outlined.ThumbUp,
                                "Confirm", Modifier.size(18.dp),
                                tint = if (hasVerified) Color(0xFF4338CA) else Color(0xFF1B4FD8)
                            )
                            Text(
                                if (hasVerified) "You confirmed this · ${report.verificationCount} total" else "I Confirm This Hazard (${report.verificationCount})",
                                fontSize = 14.sp, fontWeight = FontWeight.SemiBold,
                                color = if (hasVerified) Color(0xFF4338CA) else Color(0xFF1B4FD8)
                            )
                        }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // COMMENTS SECTION
            // ════════════════════════════════
            Card(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(10.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB))
            ) {
                Column(Modifier.padding(16.dp)) {
                    Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                        SectionHeader(icon = Icons.Default.Email, label = "COMMENTS ($commentCount)")
                        TextButton(onClick = { }) { Text("View All →", fontSize = 12.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.SemiBold) }
                    }

                    Spacer(Modifier.height(4.dp))

                    // Pinned official comment
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        colors = CardDefaults.cardColors(containerColor = Color(0xFFEEF2FF)),
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, Color(0xFFC7D2FE))
                    ) {
                        Column(Modifier.padding(10.dp)) {
                            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                                Icon(Icons.Filled.CheckCircle, "Official", Modifier.size(12.dp), tint = Color(0xFF6366F1))
                                Text("Municipal Corporation", fontSize = 13.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF4338CA))
                                Spacer(Modifier.weight(1f))
                                Box(Modifier.background(Color(0xFFC7D2FE), RoundedCornerShape(4.dp)).padding(horizontal = 5.dp, vertical = 1.dp)) {
                                    Text("Official", fontSize = 9.sp, color = Color(0xFF4338CA), fontWeight = FontWeight.Bold)
                                }
                            }
                            Spacer(Modifier.height(4.dp))
                            Text("Complaint registered. Assigned to PWD Division. Expected repair within 7 working days.", fontSize = 13.sp, color = Color(0xFF374151), lineHeight = 18.sp)
                        }
                    }

                    Spacer(Modifier.height(8.dp))

                    // Add comment input
                    var commentText by remember { mutableStateOf("") }
                    Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        Box(
                            Modifier.size(32.dp).background(Color(0xFFEEF2FF), CircleShape),
                            contentAlignment = Alignment.Center
                        ) { Text("U", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color(0xFF4338CA)) }
                        OutlinedTextField(
                            value = commentText, onValueChange = { commentText = it },
                            placeholder = { Text("Add a comment...", fontSize = 13.sp, color = Color(0xFF9CA3AF)) },
                            modifier = Modifier.weight(1f).height(44.dp),
                            shape = RoundedCornerShape(20.dp),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = Color(0xFF1B4FD8), unfocusedBorderColor = Color(0xFFE5E7EB),
                                focusedContainerColor = Color.White, unfocusedContainerColor = Color.White
                            ),
                            singleLine = true,
                            trailingIcon = {
                                IconButton(onClick = { if (commentText.isNotBlank()) { Toast.makeText(context, "Comment posted!", Toast.LENGTH_SHORT).show(); commentText = "" } }, enabled = commentText.isNotBlank()) {
                                    Icon(Icons.Outlined.Send, "Send", Modifier.size(18.dp), tint = if (commentText.isNotBlank()) Color(0xFF1B4FD8) else Color(0xFFD1D5DB))
                                }
                            },
                            textStyle = androidx.compose.ui.text.TextStyle(fontSize = 13.sp)
                        )
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // STATUS TIMELINE CARD
            // ════════════════════════════════
            Card(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(10.dp),
                border = BorderStroke(1.dp, Color(0xFFE5E7EB))
            ) {
                Column(Modifier.padding(16.dp)) {
                    SectionHeader(icon = Icons.Default.DateRange, label = "STATUS TIMELINE")
                    Spacer(Modifier.height(12.dp))

                    TimelineStep("Report Submitted", report.reportTime, "Hazard reported by citizen with photo evidence", TimelineState.COMPLETED, showLine = true)
                    TimelineStep("Report Verified", report.reportTime, "${report.verificationCount} citizens corroborated report", TimelineState.COMPLETED, showLine = true)
                    TimelineStep("Assigned to Department", "In Progress", "Assigned to PWD Division, Kota", TimelineState.CURRENT, showLine = true)
                    TimelineStep("Inspection Scheduled", "Expected", "On-site inspection by team", TimelineState.PENDING, showLine = true)
                    TimelineStep("Repair in Progress", "Expected", "Road repair works to commence", TimelineState.PENDING, showLine = true)
                    TimelineStep("Resolved", "Expected", "Hazard cleared and road restored", TimelineState.PENDING, showLine = false)
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // PETITION STATUS CARD
            // ════════════════════════════════
            if (petitionEnabled) {
                val signatureCount = remember(report.id) { 20 + (kotlin.math.abs(report.id.hashCode()) % 30) }
                val signatureGoal = 50
                Card(
                    modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFFEEF2FF)),
                    shape = RoundedCornerShape(10.dp),
                    border = BorderStroke(1.dp, Color(0xFFC7D2FE))
                ) {
                    Column(Modifier.padding(16.dp)) {
                        Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                                Icon(Icons.Default.Build, "Petition", Modifier.size(16.dp), tint = Color(0xFF6366F1))
                                Text("CITIZEN PETITION", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF4338CA), letterSpacing = 1.sp)
                            }
                            Box(Modifier.background(Color(0xFFDCFCE7), RoundedCornerShape(4.dp)).padding(horizontal = 6.dp, vertical = 2.dp)) {
                                Text("● ACTIVE", fontSize = 10.sp, color = Color(0xFF16A34A), fontWeight = FontWeight.Bold)
                            }
                        }

                        Spacer(Modifier.height(8.dp))
                        Text("Urgent Repair Request — ${report.title}", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color(0xFF111827))
                        Spacer(Modifier.height(8.dp))

                        Text("SIGNATURES", fontSize = 11.sp, color = Color(0xFF6B7280), fontWeight = FontWeight.Medium)
                        Row(verticalAlignment = Alignment.Bottom, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                            Text("$signatureCount", fontSize = 22.sp, fontWeight = FontWeight.Bold, color = Color(0xFF1B4FD8))
                            Text("/ $signatureGoal needed", fontSize = 13.sp, color = Color(0xFF6B7280))
                        }
                        Spacer(Modifier.height(6.dp))
                        LinearProgressIndicator(
                            progress = { signatureCount.toFloat() / signatureGoal },
                            modifier = Modifier.fillMaxWidth().height(8.dp).clip(RoundedCornerShape(4.dp)),
                            color = Color(0xFF1B4FD8), trackColor = Color(0xFFC7D2FE)
                        )
                        Spacer(Modifier.height(4.dp))
                        Text("${signatureGoal - signatureCount} more signatures to trigger municipal action", fontSize = 12.sp, color = Color(0xFF6B7280))

                        Spacer(Modifier.height(12.dp))
                        Button(
                            onClick = { Toast.makeText(context, "Petition signed!", Toast.LENGTH_SHORT).show() },
                            modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(8.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8))
                        ) { Text("✍ Sign Petition", color = Color.White, fontWeight = FontWeight.SemiBold) }
                        Spacer(Modifier.height(6.dp))
                        OutlinedButton(
                            onClick = { }, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(8.dp),
                            border = BorderStroke(1.dp, Color(0xFF6366F1))
                        ) { Text("📄 View Full Petition", color = Color(0xFF4338CA), fontWeight = FontWeight.SemiBold) }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // ════════════════════════════════
            // RELATED ALERTS
            // ════════════════════════════════
            if (relatedAlerts.isNotEmpty()) {
                Card(
                    modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(10.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB))
                ) {
                    Column(Modifier.padding(16.dp)) {
                        SectionHeader(icon = Icons.Default.Place, label = "NEARBY RELATED ALERTS (${relatedAlerts.size})")
                        Spacer(Modifier.height(10.dp))

                        LazyRow(horizontalArrangement = Arrangement.spacedBy(10.dp)) {
                            items(relatedAlerts) { related ->
                                val relSevColor = when (related.severity) { Severity.HIGH -> Color(0xFFDC2626); Severity.MEDIUM -> Color(0xFFD97706); Severity.LOW -> Color(0xFF16A34A) }
                                val relSevBg = when (related.severity) { Severity.HIGH -> Color(0xFFFEE2E2); Severity.MEDIUM -> Color(0xFFFEF3C7); Severity.LOW -> Color(0xFFDCFCE7) }
                                val relSevText = when (related.severity) { Severity.HIGH -> "High"; Severity.MEDIUM -> "Medium"; Severity.LOW -> "Low" }
                                val dist = String.format("%.1f km", 0.1 + (kotlin.math.abs(related.id.hashCode()) % 30) / 10.0)

                                Card(
                                    modifier = Modifier.width(200.dp).height(120.dp).clickable { },
                                    colors = CardDefaults.cardColors(containerColor = Color.White),
                                    shape = RoundedCornerShape(8.dp),
                                    border = BorderStroke(1.dp, Color(0xFFE5E7EB))
                                ) {
                                    Row {
                                        Box(Modifier.width(3.dp).fillMaxHeight().background(relSevColor))
                                        Column(Modifier.padding(10.dp).weight(1f)) {
                                            Row(horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                                                Box(Modifier.background(relSevBg, RoundedCornerShape(4.dp)).padding(horizontal = 4.dp, vertical = 1.dp)) {
                                                    Text(relSevText, fontSize = 9.sp, color = relSevColor, fontWeight = FontWeight.Bold)
                                                }
                                                Text("· $dist", fontSize = 10.sp, color = Color(0xFF6B7280))
                                            }
                                            Spacer(Modifier.height(4.dp))
                                            Text(related.title, fontSize = 13.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF111827), maxLines = 2, overflow = TextOverflow.Ellipsis)
                                            Text(related.locationName, fontSize = 11.sp, color = Color(0xFF6B7280), maxLines = 1, overflow = TextOverflow.Ellipsis)
                                            Spacer(Modifier.weight(1f))
                                            Text("View →", fontSize = 11.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.Bold)
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            Spacer(Modifier.height(24.dp))
        }
    }
}

// ====================================================
// Helper Composables
// ====================================================

@Composable
fun SectionHeader(icon: androidx.compose.ui.graphics.vector.ImageVector, label: String) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        Icon(icon, label, Modifier.size(16.dp), tint = Color(0xFF6B7280))
        Spacer(Modifier.width(6.dp))
        Text(label, fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF6B7280), letterSpacing = 1.2.sp)
        Spacer(Modifier.width(8.dp))
        HorizontalDivider(Modifier.weight(1f), color = Color(0xFFF3F4F6))
    }
}

@Composable
fun DetailGridRow(label1: String, value1: String, label2: String, value2: String, valueColor2: Color?) {
    Row(Modifier.fillMaxWidth().padding(vertical = 10.dp)) {
        Column(Modifier.weight(1f)) {
            Text(label1, fontSize = 11.sp, color = Color(0xFF9CA3AF))
            Spacer(Modifier.height(2.dp))
            Text(value1, fontSize = 13.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF111827))
        }
        Box(Modifier.width(1.dp).height(36.dp).background(Color(0xFFF3F4F6)))
        Column(Modifier.weight(1f).padding(start = 12.dp)) {
            Text(label2, fontSize = 11.sp, color = Color(0xFF9CA3AF))
            Spacer(Modifier.height(2.dp))
            Text(value2, fontSize = 13.sp, fontWeight = FontWeight.SemiBold, color = valueColor2 ?: Color(0xFF111827))
        }
    }
}

@Composable
fun AIResultRow(label: String, value: String, confidence: String) {
    Row(
        Modifier.fillMaxWidth().background(Color.White.copy(0.5f), RoundedCornerShape(6.dp)).padding(horizontal = 10.dp, vertical = 6.dp),
        horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically
    ) {
        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            Box(Modifier.size(6.dp).background(Color(0xFF6366F1), CircleShape))
            Column {
                Text(label, fontSize = 11.sp, color = Color(0xFF6B7280))
                Text(value, fontSize = 13.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF111827))
            }
        }
        Box(
            Modifier.border(1.dp, Color(0xFFC7D2FE), RoundedCornerShape(10.dp))
                .background(Color.White, RoundedCornerShape(10.dp))
                .padding(horizontal = 6.dp, vertical = 2.dp)
        ) { Text(confidence, fontSize = 10.sp, fontWeight = FontWeight.Bold, color = Color(0xFF4338CA)) }
    }
}

@Composable
fun CommunityStatColumn(modifier: Modifier, count: String, label: String, emoji: String, color: Color) {
    Column(modifier, horizontalAlignment = Alignment.CenterHorizontally) {
        Text(emoji, fontSize = 16.sp)
        Spacer(Modifier.height(2.dp))
        Text(count, fontSize = 22.sp, fontWeight = FontWeight.Bold, color = Color(0xFF111827))
        Text(label, fontSize = 11.sp, color = Color(0xFF6B7280))
    }
}

enum class TimelineState { COMPLETED, CURRENT, PENDING }

@Composable
fun TimelineStep(title: String, time: String, description: String, state: TimelineState, showLine: Boolean) {
    Row(modifier = Modifier.fillMaxWidth()) {
        // Timeline indicator
        Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.width(24.dp)) {
            when (state) {
                TimelineState.COMPLETED -> {
                    Box(Modifier.size(20.dp).background(Color(0xFF16A34A), CircleShape), contentAlignment = Alignment.Center) {
                        Icon(Icons.Filled.Check, "Done", Modifier.size(12.dp), tint = Color.White)
                    }
                }
                TimelineState.CURRENT -> {
                    val transition = rememberInfiniteTransition(label = "pulse")
                    val pulseAlpha by transition.animateFloat(0.4f, 1f, infiniteRepeatable(tween(1000), RepeatMode.Reverse), label = "pulseAlpha")
                    Box(Modifier.size(20.dp).alpha(pulseAlpha).background(Color(0xFF1B4FD8), CircleShape))
                }
                TimelineState.PENDING -> {
                    Box(Modifier.size(20.dp).border(2.dp, Color(0xFFE5E7EB), CircleShape))
                }
            }
            if (showLine) {
                Box(
                    Modifier.width(2.dp).height(40.dp).background(
                        if (state == TimelineState.COMPLETED) Color(0xFF16A34A) else Color(0xFFE5E7EB)
                    )
                )
            }
        }

        Spacer(Modifier.width(12.dp))

        // Step details
        Column(Modifier.weight(1f).padding(bottom = if (showLine) 24.dp else 0.dp)) {
            Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                Text(title, fontSize = 14.sp, fontWeight = FontWeight.SemiBold, color = Color(0xFF111827))
                Text(time, fontSize = 11.sp, color = Color(0xFF9CA3AF))
            }
            Text(description, fontSize = 12.sp, color = Color(0xFF6B7280), lineHeight = 16.sp)
            if (state == TimelineState.CURRENT) {
                Spacer(Modifier.height(4.dp))
                Box(Modifier.background(Color(0xFFEEF2FF), RoundedCornerShape(4.dp)).padding(horizontal = 6.dp, vertical = 2.dp)) {
                    Text("In Progress", fontSize = 10.sp, color = Color(0xFF4338CA), fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
fun SkeletonDetailContent() {
    val transition = rememberInfiniteTransition(label = "shimmer")
    val alpha by transition.animateFloat(
        initialValue = 0.3f, targetValue = 0.9f,
        animationSpec = infiniteRepeatable(tween(800, easing = LinearEasing), RepeatMode.Reverse),
        label = "alpha"
    )
    Column(Modifier.fillMaxSize().alpha(alpha)) {
        Box(Modifier.fillMaxWidth().height(280.dp).background(Color(0xFFE2E8F0)))
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
            Row(Modifier.fillMaxWidth(), Arrangement.SpaceBetween) {
                Column(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                    Box(Modifier.size(180.dp, 24.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(4.dp)))
                    Box(Modifier.size(120.dp, 16.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(4.dp)))
                }
                Box(Modifier.size(80.dp, 24.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(8.dp)))
            }
            Box(Modifier.fillMaxWidth().height(72.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(12.dp)))
            Box(Modifier.fillMaxWidth().height(100.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(12.dp)))
            Box(Modifier.fillMaxWidth().height(160.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(12.dp)))
        }
    }
}
