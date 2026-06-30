package com.nagarrakshak.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.MoreVert
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.StrokeCap
import androidx.compose.ui.graphics.StrokeJoin
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Rect
import androidx.compose.ui.geometry.RoundRect
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.platform.LocalContext
import com.nagarrakshak.data.BackendClient
import com.nagarrakshak.data.models.HazardReport
import com.nagarrakshak.data.models.Severity
import com.nagarrakshak.data.models.VerificationStatus

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AlertsScreen(
    onNavigateToDetail: (String) -> Unit,
    onNavigateToNotifications: () -> Unit,
    onNavigateToMap: (Double, Double, String) -> Unit
) {
    val context = LocalContext.current
    var alertsList by remember { mutableStateOf<List<HazardReport>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var selectedTab by remember { mutableStateOf("All") }
    var selectedChips by remember { mutableStateOf<Set<String>>(emptySet()) }
    var searchQuery by remember { mutableStateOf("") }

    var currentCityName by remember { mutableStateOf("Kota") }
    var currentAreaName by remember { mutableStateOf("Talwandi") }
    var userPincode by remember { mutableStateOf("324005") }

    LaunchedEffect(Unit) {
        isLoading = true
        fetchHomeLocation(context) { city, area, pincode ->
            currentCityName = city
            currentAreaName = area
            userPincode = pincode
        }
        alertsList = BackendClient.fetchNearbyHazards()
        isLoading = false
    }

    val highCount = remember(alertsList) { alertsList.count { it.severity == Severity.HIGH } }
    val mediumCount = remember(alertsList) { alertsList.count { it.severity == Severity.MEDIUM } }
    val lowCount = remember(alertsList) { alertsList.count { it.severity == Severity.LOW } }
    val totalCount = alertsList.size

    val sortedAlerts = remember(alertsList, userPincode, currentCityName, currentAreaName) {
        alertsList.sortedWith(compareByDescending<HazardReport> { alert ->
            val loc = alert.locationName.lowercase()
            val pin = userPincode.lowercase()
            val city = currentCityName.lowercase()
            val area = currentAreaName.lowercase()
            when {
                loc.contains(pin) || (area.length > 2 && loc.contains(area)) -> 2
                city.length > 2 && loc.contains(city) -> 1
                else -> 0
            }
        })
    }

    val filteredList = remember(sortedAlerts, selectedTab, selectedChips, searchQuery, userPincode, currentAreaName, currentCityName) {
        var list = sortedAlerts

        when (selectedTab) {
            "High" -> list = list.filter { it.severity == Severity.HIGH }
            "Medium" -> list = list.filter { it.severity == Severity.MEDIUM }
            "Low" -> list = list.filter { it.severity == Severity.LOW }
            "Nearby" -> list = list.filter { alert ->
                val loc = alert.locationName.lowercase()
                loc.contains(userPincode.lowercase()) ||
                (currentAreaName.length > 2 && loc.contains(currentAreaName.lowercase())) ||
                (currentCityName.length > 2 && loc.contains(currentCityName.lowercase()))
            }
        }

        if (selectedChips.isNotEmpty()) {
            list = list.filter { alert ->
                selectedChips.any { chip ->
                    alert.category.contains(chip, ignoreCase = true) ||
                    alert.title.contains(chip, ignoreCase = true)
                }
            }
        }

        if (searchQuery.isNotBlank()) {
            list = list.filter { alert ->
                alert.title.contains(searchQuery, ignoreCase = true) ||
                alert.locationName.contains(searchQuery, ignoreCase = true) ||
                alert.description.contains(searchQuery, ignoreCase = true) ||
                alert.category.contains(searchQuery, ignoreCase = true)
            }
        }

        list
    }

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF5F7FA)),
        contentPadding = PaddingValues(bottom = 24.dp)
    ) {
        // 1. Top Header Bar
        item {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 14.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Alerts",
                    fontSize = 26.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF0F172A),
                    modifier = Modifier.weight(1f)
                )
                Row(
                    horizontalArrangement = Arrangement.spacedBy(2.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    IconButton(onClick = { }) {
                        Icon(Icons.Default.Search, "Search", tint = Color(0xFF374151), modifier = Modifier.size(24.dp))
                    }
                    IconButton(onClick = { }) {
                        AlertsFilterIcon(color = Color(0xFF374151))
                    }
                    Box {
                        IconButton(onClick = onNavigateToNotifications) {
                            Icon(Icons.Default.Notifications, "Notifications", tint = Color(0xFF374151), modifier = Modifier.size(24.dp))
                        }
                        if (totalCount > 0) {
                            Box(
                                modifier = Modifier
                                    .size(8.dp)
                                    .background(Color(0xFFEF4444), CircleShape)
                                    .align(Alignment.TopEnd)
                                    .offset(x = (-4).dp, y = 4.dp)
                            )
                        }
                    }
                }
            }
        }

        // 2. Dark Navy Summary Cards
        item {
            Row(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                SeveritySummaryCard(Modifier.weight(1f), highCount, "HIGH", Color(0xFFEF4444), "warning")
                SeveritySummaryCard(Modifier.weight(1f), mediumCount, "MEDIUM", Color(0xFFF59E0B), "bell")
                SeveritySummaryCard(Modifier.weight(1f), lowCount, "LOW", Color(0xFF3B82F6), "info")
            }
        }

        item {
            Text(
                text = "$totalCount active alerts in Kota",
                fontSize = 12.sp, color = Color(0xFF94A3B8),
                modifier = Modifier.fillMaxWidth().padding(top = 8.dp, bottom = 12.dp),
                textAlign = androidx.compose.ui.text.style.TextAlign.Center
            )
        }

        // 3. Search Bar
        item {
            OutlinedTextField(
                value = searchQuery, onValueChange = { searchQuery = it },
                placeholder = { Text("Search alerts, areas, hazard types...", fontSize = 14.sp, color = Color(0xFF9CA3AF)) },
                leadingIcon = { Icon(Icons.Default.Search, "Search", tint = Color(0xFF9CA3AF), modifier = Modifier.size(20.dp)) },
                trailingIcon = { MicIcon(color = Color(0xFF6B7280)) },
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = Color(0xFFE5E7EB), unfocusedBorderColor = Color(0xFFE5E7EB),
                    focusedContainerColor = Color.White, unfocusedContainerColor = Color.White
                ),
                singleLine = true
            )
        }

        // 4. Severity Tab Row
        item {
            Row(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 10.dp)
                    .horizontalScroll(rememberScrollState()),
                horizontalArrangement = Arrangement.spacedBy(6.dp)
            ) {
                SeverityTab("All", totalCount, selectedTab == "All", null) { selectedTab = "All" }
                SeverityTab("High", highCount, selectedTab == "High", Color(0xFFEF4444)) { selectedTab = "High" }
                SeverityTab("Medium", mediumCount, selectedTab == "Medium", Color(0xFFF59E0B)) { selectedTab = "Medium" }
                SeverityTab("Low", lowCount, selectedTab == "Low", Color(0xFF10B981)) { selectedTab = "Low" }
                SeverityTab("Nearby", null, selectedTab == "Nearby", null) { selectedTab = "Nearby" }
                SeverityTab("Official", null, selectedTab == "Official", null) { selectedTab = "Official" }
            }
        }

        // 5. Category Chip Filters
        item {
            val chipLabels = listOf("Today" to "📅", "Within 5km" to "📍", "Pothole" to "🕳️", "Waterlogging" to "🌊", "Broken Light" to "💡", "Road Damage" to "🚧")
            Row(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp).horizontalScroll(rememberScrollState()),
                horizontalArrangement = Arrangement.spacedBy(6.dp)
            ) {
                chipLabels.forEach { (label, icon) ->
                    val isChipSelected = selectedChips.contains(label)
                    Box(
                        modifier = Modifier
                            .background(if (isChipSelected) Color(0xFF1E293B) else Color.White, RoundedCornerShape(20.dp))
                            .border(1.dp, if (isChipSelected) Color(0xFF1E293B) else Color(0xFFE5E7EB), RoundedCornerShape(20.dp))
                            .clickable { selectedChips = if (isChipSelected) selectedChips - label else selectedChips + label }
                            .padding(horizontal = 12.dp, vertical = 7.dp)
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                            Text(icon, fontSize = 12.sp)
                            Text(label, fontSize = 12.sp, color = if (isChipSelected) Color.White else Color(0xFF374151), fontWeight = FontWeight.Medium)
                        }
                    }
                }
                Box(
                    modifier = Modifier.background(Color.White, RoundedCornerShape(20.dp))
                        .border(1.dp, Color(0xFF1B4FD8), RoundedCornerShape(20.dp))
                        .clickable { }.padding(horizontal = 12.dp, vertical = 7.dp)
                ) {
                    Text("+ More Filters", fontSize = 12.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.Bold)
                }
            }
        }

        // 6. Section Header
        item {
            Row(
                modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 14.dp),
                horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically
            ) {
                Text("NEW ALERTS", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFF6B7280), letterSpacing = 1.sp)
                Text("Mark all read", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color(0xFF1B4FD8), modifier = Modifier.clickable { })
            }
        }

        // 7. Alert Cards
        if (isLoading) {
            items(3) { Box(Modifier.padding(horizontal = 16.dp)) { SkeletonAlertCard() } }
        } else if (filteredList.isEmpty()) {
            item {
                Box(Modifier.fillMaxWidth().padding(48.dp), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text("🔔", fontSize = 40.sp)
                        Spacer(Modifier.height(12.dp))
                        Text("No alerts found", fontWeight = FontWeight.Bold, fontSize = 16.sp, color = Color(0xFF374151))
                        Spacer(Modifier.height(4.dp))
                        Text("Try adjusting your filters", fontSize = 13.sp, color = Color(0xFF9CA3AF))
                    }
                }
            }
        } else {
            items(count = filteredList.size, key = { index -> filteredList[index].id }) { index ->
                val alert = filteredList[index]
                Box(Modifier.padding(horizontal = 16.dp, vertical = 4.dp)) {
                    AlertCardRedesigned(
                        alert = alert,
                        onClick = { onNavigateToDetail(alert.id) },
                        onNavigateClick = { onNavigateToMap(alert.latitude, alert.longitude, alert.category) }
                    )
                }
            }
        }

        // 8. EARLIER footer
        item {
            Box(Modifier.fillMaxWidth().padding(vertical = 16.dp), contentAlignment = Alignment.Center) {
                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    HorizontalDivider(Modifier.width(60.dp), 1.dp, Color(0xFFE5E7EB))
                    Text("EARLIER", fontSize = 11.sp, fontWeight = FontWeight.Bold, color = Color(0xFF9CA3AF), letterSpacing = 1.sp)
                    HorizontalDivider(Modifier.width(60.dp), 1.dp, Color(0xFFE5E7EB))
                }
            }
        }
    }
}

// ====================================================
// Severity Summary Card (Tinted/Light Pastel)
// ====================================================
@Composable
fun SeveritySummaryCard(modifier: Modifier, count: Int, label: String, dotColor: Color, iconType: String) {
    val (backgroundColor, textColor) = when (label.uppercase()) {
        "HIGH" -> Pair(Color(0xFFFEF2F2), Color(0xFF991B1B))
        "MEDIUM" -> Pair(Color(0xFFFFFBEB), Color(0xFF92400E))
        else -> Pair(Color(0xFFEFF6FF), Color(0xFF1E40AF))
    }

    Card(
        modifier = modifier.height(95.dp),
        colors = CardDefaults.cardColors(containerColor = backgroundColor),
        shape = RoundedCornerShape(14.dp),
        border = BorderStroke(1.dp, textColor.copy(alpha = 0.12f))
    ) {
        Box(Modifier.fillMaxSize().padding(14.dp)) {
            Text(
                text = "$count",
                fontSize = 30.sp,
                fontWeight = FontWeight.Black,
                color = textColor,
                modifier = Modifier.align(Alignment.TopStart)
            )
            Box(modifier = Modifier.align(Alignment.TopEnd)) {
                when (iconType) {
                    "warning" -> AlertTriangleIcon(textColor.copy(alpha = 0.18f), Modifier.size(30.dp))
                    "bell" -> AlertBellDrawIcon(textColor.copy(alpha = 0.18f), Modifier.size(30.dp))
                    "info" -> AlertInfoIcon(textColor.copy(alpha = 0.18f), Modifier.size(30.dp))
                }
            }
            Row(
                modifier = Modifier.align(Alignment.BottomStart),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(6.dp)
            ) {
                Box(Modifier.size(6.dp).background(dotColor, CircleShape))
                Text(
                    text = label,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                    color = textColor.copy(alpha = 0.8f),
                    letterSpacing = 1.sp
                )
            }
        }
    }
}

// ====================================================
// Severity Tab
// ====================================================
@Composable
fun SeverityTab(text: String, count: Int?, isSelected: Boolean, color: Color?, onClick: () -> Unit) {
    Box(
        modifier = Modifier
            .background(if (isSelected) Color(0xFF1E293B) else Color.Transparent, RoundedCornerShape(10.dp))
            .clickable { onClick() }
            .padding(horizontal = 15.dp, vertical = 8.dp)
    ) {
        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(6.dp)) {
            Text(
                text = text,
                fontSize = 13.sp,
                fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
                color = if (isSelected) Color.White else Color(0xFF6B7280)
            )
            if (count != null && count > 0) {
                Box(
                    modifier = Modifier
                        .size(18.dp)
                        .background(color ?: if (isSelected) Color(0xFF475569) else Color(0xFFE5E7EB), CircleShape),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "$count",
                        fontSize = 9.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color.White,
                        textAlign = androidx.compose.ui.text.style.TextAlign.Center,
                        lineHeight = 9.sp,
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

// ====================================================
// Redesigned Alert Card
// ====================================================
@Composable
fun AlertCardRedesigned(alert: HazardReport, onClick: () -> Unit, onNavigateClick: () -> Unit = {}) {
    val severityColor = when (alert.severity) { Severity.HIGH -> Color(0xFFDC2626); Severity.MEDIUM -> Color(0xFFD97706); Severity.LOW -> Color(0xFF16A34A) }
    val severityBg = when (alert.severity) { Severity.HIGH -> Color(0xFFFEE2E2); Severity.MEDIUM -> Color(0xFFFEF3C7); Severity.LOW -> Color(0xFFDCFCE7) }
    val severityLabel = when (alert.severity) { Severity.HIGH -> "High Risk"; Severity.MEDIUM -> "Medium Risk"; Severity.LOW -> "Low Risk" }
    val severityBadgeText = when (alert.severity) { Severity.HIGH -> "HIGH"; Severity.MEDIUM -> "MED"; Severity.LOW -> "LOW" }

    Card(
        modifier = Modifier.fillMaxWidth().clickable { onClick() },
        colors = CardDefaults.cardColors(containerColor = Color.White),
        shape = RoundedCornerShape(16.dp),
        border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Column {
            Row(Modifier.fillMaxWidth().padding(16.dp)) {
                // Image thumbnail with severity badge overlay
                Box(Modifier.size(width = 96.dp, height = 96.dp).clip(RoundedCornerShape(12.dp)).background(Color(0xFFF1F5F9))) {
                    val firstUrl = alert.imageUrl?.split(",")?.firstOrNull()?.trim()
                    val model = if (!firstUrl.isNullOrBlank()) firstUrl else com.nagarrakshak.R.drawable.placeholder_hazard
                    coil.compose.AsyncImage(model = model, contentDescription = "Alert Photo", modifier = Modifier.fillMaxSize(), contentScale = androidx.compose.ui.layout.ContentScale.Crop)
                    Box(Modifier.align(Alignment.BottomStart).padding(6.dp).background(severityColor, RoundedCornerShape(4.dp)).padding(horizontal = 6.dp, vertical = 2.dp)) {
                        Text(text = severityBadgeText, color = Color.White, fontSize = 9.sp, fontWeight = FontWeight.Bold)
                    }
                }

                Spacer(Modifier.width(16.dp))

                Column(Modifier.weight(1f)) {
                    // Title + time + menu
                    Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.Top) {
                        Column(Modifier.weight(1f)) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Box(Modifier.size(8.dp).background(severityColor, CircleShape))
                                Spacer(Modifier.width(6.dp))
                                Text(alert.title, fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color(0xFF0F172A), maxLines = 1, overflow = TextOverflow.Ellipsis)
                            }
                            Spacer(modifier = Modifier.height(2.dp))
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text("📍", fontSize = 10.sp)
                                Spacer(Modifier.width(3.dp))
                                Text(alert.locationName, fontSize = 11.sp, color = Color(0xFF64748B), maxLines = 1, overflow = TextOverflow.Ellipsis)
                            }
                        }
                        
                        Row(modifier = Modifier.padding(start = 4.dp), verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(2.dp)) {
                            Text(alert.reportTime, fontSize = 10.sp, color = Color(0xFF94A3B8))
                            Icon(Icons.Default.MoreVert, "Menu", tint = Color(0xFF94A3B8), modifier = Modifier.size(16.dp))
                        }
                    }

                    Spacer(Modifier.height(6.dp))
                    Text(if (alert.description.isBlank()) "No description provided." else alert.description, fontSize = 12.sp, color = Color(0xFF475569), maxLines = 2, overflow = TextOverflow.Ellipsis, lineHeight = 16.sp)

                    val urls = alert.imageUrl?.split(",")?.map { it.trim() }?.filter { it.isNotBlank() } ?: emptyList()
                    if (urls.size > 1) {
                        Spacer(Modifier.height(8.dp))
                        Text("COMMUNITY EVIDENCE (${urls.size} PHOTOS)", fontSize = 9.sp, fontWeight = FontWeight.Bold, color = Color(0xFF64748B), letterSpacing = 0.5.sp)
                        Spacer(Modifier.height(4.dp))
                        Row(
                            modifier = Modifier.horizontalScroll(rememberScrollState()),
                            horizontalArrangement = Arrangement.spacedBy(6.dp)
                        ) {
                            urls.forEachIndexed { idx, url ->
                                Card(
                                    modifier = Modifier.size(40.dp),
                                    shape = RoundedCornerShape(6.dp),
                                    border = BorderStroke(1.dp, Color(0xFFE2E8F0))
                                ) {
                                    coil.compose.AsyncImage(
                                        model = url,
                                        contentDescription = "Evidence $idx",
                                        modifier = Modifier.fillMaxSize(),
                                        contentScale = androidx.compose.ui.layout.ContentScale.Crop
                                    )
                                }
                            }
                        }
                    }

                    Spacer(Modifier.height(10.dp))

                    // Category chips
                    Row(Modifier.horizontalScroll(rememberScrollState()), horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                        AlertChip(severityLabel, "⚠️", severityBg, severityColor)
                        AlertChip(alert.category, bgColor = Color(0xFFF1F5F9), textColor = Color(0xFF475569))
                        if (urls.size > 1) {
                            AlertChip("Merged (${urls.size} Evidences)", bgColor = Color(0xFFDCFCE7), textColor = Color(0xFF16A34A))
                        }
                        AlertChip("📍 ${String.format("%.1f", 0.1 + (kotlin.math.abs(alert.id.hashCode()) % 50) / 10.0)} km", bgColor = Color(0xFFF1F5F9), textColor = Color(0xFF475569))
                        AlertChip("Citizen Report", bgColor = Color(0xFFF1F5F9), textColor = Color(0xFF475569))
                    }
                }
            }

            // Bottom action row
            HorizontalDivider(color = Color(0xFFF1F5F9), thickness = 1.dp)
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 10.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Info badges
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), verticalAlignment = Alignment.CenterVertically) {
                    Box(Modifier.background(Color(0xFFF8FAFC), RoundedCornerShape(8.dp)).padding(horizontal = 8.dp, vertical = 4.dp)) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text("👍", fontSize = 11.sp)
                            Spacer(Modifier.width(4.dp))
                            Text("${alert.verificationCount}", fontSize = 11.sp, fontWeight = FontWeight.Bold, color = Color(0xFF475569))
                        }
                    }
                    Box(Modifier.background(Color(0xFFF8FAFC), RoundedCornerShape(8.dp)).padding(horizontal = 8.dp, vertical = 4.dp)) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text("💬", fontSize = 11.sp)
                            Spacer(Modifier.width(4.dp))
                            Text("${2 + (kotlin.math.abs(alert.id.hashCode()) % 8)}", fontSize = 11.sp, fontWeight = FontWeight.Bold, color = Color(0xFF475569))
                        }
                    }
                }
                
                // Interactive buttons
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(8.dp))
                            .background(Color(0xFFEFF6FF))
                            .clickable { onNavigateClick() }
                            .padding(horizontal = 10.dp, vertical = 6.dp)
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                            Text("🧭", fontSize = 11.sp)
                            Text("Navigate", fontSize = 11.sp, color = Color(0xFF1B4FD8), fontWeight = FontWeight.Bold)
                        }
                    }
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(8.dp))
                            .background(Color(0xFF1B4FD8))
                            .clickable { onClick() }
                            .padding(horizontal = 10.dp, vertical = 6.dp)
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
                            Text("Details", fontSize = 11.sp, color = Color.White, fontWeight = FontWeight.Bold)
                            Text("→", fontSize = 11.sp, color = Color.White)
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun AlertChip(text: String, icon: String? = null, bgColor: Color = Color(0xFFF1F5F9), textColor: Color = Color(0xFF475569)) {
    Box(Modifier.background(bgColor, RoundedCornerShape(12.dp)).padding(horizontal = 7.dp, vertical = 3.dp)) {
        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(2.dp)) {
            if (icon != null) Text(icon, fontSize = 9.sp)
            Text(text, fontSize = 10.sp, color = textColor, fontWeight = FontWeight.Medium)
        }
    }
}

// ====================================================
// Custom Icons
// ====================================================
@Composable
fun AlertsFilterIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF374151)) {
    Canvas(modifier = modifier.size(20.dp)) {
        val sw = 2f.dp.toPx(); val w = size.width; val h = size.height
        drawLine(color, Offset(w * 0.1f, h * 0.25f), Offset(w * 0.9f, h * 0.25f), sw, StrokeCap.Round)
        drawLine(color, Offset(w * 0.25f, h * 0.5f), Offset(w * 0.75f, h * 0.5f), sw, StrokeCap.Round)
        drawLine(color, Offset(w * 0.38f, h * 0.75f), Offset(w * 0.62f, h * 0.75f), sw, StrokeCap.Round)
    }
}

@Composable
fun AlertTriangleIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width; val h = size.height; val sw = 1.8f.dp.toPx()
        val path = Path().apply { moveTo(w * 0.5f, h * 0.15f); lineTo(w * 0.9f, h * 0.85f); lineTo(w * 0.1f, h * 0.85f); close() }
        drawPath(path, color, style = Stroke(sw, join = StrokeJoin.Round))
        drawLine(color, Offset(w * 0.5f, h * 0.4f), Offset(w * 0.5f, h * 0.6f), sw, StrokeCap.Round)
        drawCircle(color, 1.5f.dp.toPx(), Offset(w * 0.5f, h * 0.72f))
    }
}

@Composable
fun AlertBellDrawIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width; val h = size.height; val sw = 1.8f.dp.toPx()
        val path = Path().apply {
            moveTo(w * 0.5f, h * 0.1f); quadraticBezierTo(w * 0.75f, h * 0.2f, w * 0.75f, h * 0.55f)
            lineTo(w * 0.85f, h * 0.7f); lineTo(w * 0.15f, h * 0.7f); lineTo(w * 0.25f, h * 0.55f)
            quadraticBezierTo(w * 0.25f, h * 0.2f, w * 0.5f, h * 0.1f); close()
        }
        drawPath(path, color, style = Stroke(sw))
        drawArc(color, 0f, 180f, false, Offset(w * 0.38f, h * 0.72f), androidx.compose.ui.geometry.Size(w * 0.24f, h * 0.16f))
    }
}

@Composable
fun AlertInfoIcon(color: Color, modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        val w = size.width; val h = size.height; val sw = 1.8f.dp.toPx()
        drawCircle(color, (w / 2f) - sw, style = Stroke(sw))
        drawCircle(color, 1.5f.dp.toPx(), Offset(w * 0.5f, h * 0.32f))
        drawLine(color, Offset(w * 0.5f, h * 0.44f), Offset(w * 0.5f, h * 0.7f), sw, StrokeCap.Round)
    }
}

// Existing icons kept for other screens
@Composable
fun ShieldIcon(modifier: Modifier = Modifier, color: Color = Color.White) {
    Canvas(modifier = modifier.size(20.dp)) {
        val w = size.width; val h = size.height
        val path = Path().apply {
            moveTo(w * 0.5f, h * 0.1f); quadraticBezierTo(w * 0.8f, h * 0.1f, w * 0.85f, h * 0.15f)
            lineTo(w * 0.85f, h * 0.5f); quadraticBezierTo(w * 0.85f, h * 0.8f, w * 0.5f, h * 0.95f)
            quadraticBezierTo(w * 0.15f, h * 0.8f, w * 0.15f, h * 0.5f); lineTo(w * 0.15f, h * 0.15f)
            quadraticBezierTo(w * 0.2f, h * 0.1f, w * 0.5f, h * 0.1f); close()
        }
        drawPath(path, color)
    }
}

@Composable
fun FilterIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF16A34A)) {
    Canvas(modifier = modifier.size(12.dp)) {
        val sw = 1.5.dp.toPx(); val w = size.width; val h = size.height
        val path = Path().apply { moveTo(0f, 0f); lineTo(w, 0f); lineTo(w * 0.6f, h * 0.5f); lineTo(w * 0.6f, h * 0.9f); lineTo(w * 0.4f, h * 0.7f); lineTo(w * 0.4f, h * 0.5f); close() }
        drawPath(path, color, style = Stroke(sw, join = StrokeJoin.Round, cap = StrokeCap.Round))
    }
}

@Composable
fun CommentIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF64748B)) {
    Canvas(modifier = modifier.size(14.dp)) {
        val sw = 1.2.dp.toPx(); val w = size.width; val h = size.height
        val rect = RoundRect(1.dp.toPx(), 1.dp.toPx(), w - 1.dp.toPx(), h - 4.dp.toPx(), CornerRadius(2.dp.toPx()))
        val path = Path().apply { addRoundRect(rect); moveTo(3.dp.toPx(), h - 4.dp.toPx()); lineTo(1.dp.toPx(), h - 1.dp.toPx()); lineTo(6.dp.toPx(), h - 4.dp.toPx()) }
        drawPath(path, color, style = Stroke(sw))
    }
}

@Composable
fun VerificationIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF64748B)) {
    Canvas(modifier = modifier.size(14.dp)) {
        val sw = 1.2.dp.toPx(); val w = size.width; val h = size.height
        drawCircle(color, (w / 2f) - sw, style = Stroke(sw))
        val p = Path().apply { moveTo(w * 0.3f, h * 0.5f); lineTo(w * 0.45f, h * 0.65f); lineTo(w * 0.7f, h * 0.35f) }
        drawPath(p, color, style = Stroke(sw, cap = StrokeCap.Round, join = StrokeJoin.Round))
    }
}

@Composable
fun BookmarkIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF64748B)) {
    Canvas(modifier = modifier.size(14.dp)) {
        val sw = 1.2.dp.toPx(); val w = size.width; val h = size.height
        val path = Path().apply { moveTo(2.dp.toPx(), 1.dp.toPx()); lineTo(w - 2.dp.toPx(), 1.dp.toPx()); lineTo(w - 2.dp.toPx(), h - 1.dp.toPx()); lineTo(w / 2f, h - 5.dp.toPx()); lineTo(2.dp.toPx(), h - 1.dp.toPx()); close() }
        drawPath(path, color, style = Stroke(sw, join = StrokeJoin.Round))
    }
}

@Composable
fun MicIcon(modifier: Modifier = Modifier, color: Color = Color(0xFF0F172A)) {
    Canvas(modifier = modifier.size(16.dp)) {
        val sw = 1.5.dp.toPx(); val w = size.width; val h = size.height
        val bodyRect = Rect(w * 0.35f, h * 0.15f, w * 0.65f, h * 0.65f)
        drawPath(Path().apply { addRoundRect(RoundRect(bodyRect, CornerRadius(w * 0.15f))) }, color, style = Stroke(sw))
        val cp = Path().apply { moveTo(w * 0.25f, h * 0.45f); lineTo(w * 0.25f, h * 0.65f); quadraticBezierTo(w * 0.25f, h * 0.8f, w * 0.5f, h * 0.8f); quadraticBezierTo(w * 0.75f, h * 0.8f, w * 0.75f, h * 0.65f); lineTo(w * 0.75f, h * 0.45f) }
        drawPath(cp, color, style = Stroke(sw, cap = StrokeCap.Round))
        drawLine(color, Offset(w * 0.5f, h * 0.8f), Offset(w * 0.5f, h * 0.95f), sw)
        drawLine(color, Offset(w * 0.35f, h * 0.95f), Offset(w * 0.65f, h * 0.95f), sw, StrokeCap.Round)
    }
}

@Composable
fun ScanIconDraw() {
    Canvas(modifier = Modifier.size(16.dp)) {
        val s = 1.5.dp.toPx(); val l = 4.dp.toPx(); val c = Color(0xFF475569)
        drawLine(c, Offset(0f, 0f), Offset(l, 0f), s); drawLine(c, Offset(0f, 0f), Offset(0f, l), s)
        drawLine(c, Offset(size.width, 0f), Offset(size.width - l, 0f), s); drawLine(c, Offset(size.width, 0f), Offset(size.width, l), s)
        drawLine(c, Offset(0f, size.height), Offset(l, size.height), s); drawLine(c, Offset(0f, size.height), Offset(0f, size.height - l), s)
        drawLine(c, Offset(size.width, size.height), Offset(size.width - l, size.height), s); drawLine(c, Offset(size.width, size.height), Offset(size.width, size.height - l), s)
        drawRect(c, Offset(size.width * 0.35f, size.height * 0.35f), size * 0.3f)
    }
}

@Composable
fun AlertPageCard(alert: HazardReport, onClick: () -> Unit) { AlertCardRedesigned(alert, onClick) }

@Composable
fun FilterPill(text: String, color: Color? = null, isSelected: Boolean, onClick: () -> Unit) {
    val ac = color ?: Color(0xFF16A34A)
    Box(Modifier.background(if (isSelected) ac else Color.White, CircleShape).border(1.dp, if (isSelected) ac else Color(0xFFE2E8F0), CircleShape).clickable { onClick() }.padding(horizontal = 12.dp, vertical = 6.dp), contentAlignment = Alignment.Center) {
        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(4.dp)) {
            if (color != null) Box(Modifier.size(6.dp).background(if (isSelected) Color.White else color, CircleShape))
            Text(text, color = if (isSelected) Color.White else (color ?: Color(0xFF475569)), fontWeight = FontWeight.Bold, fontSize = 12.sp)
        }
    }
}
