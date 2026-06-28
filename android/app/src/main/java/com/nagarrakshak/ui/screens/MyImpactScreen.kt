package com.nagarrakshak.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Star
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.nagarrakshak.data.BackendClient
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MyImpactScreen(
    onBackClicked: () -> Unit
) {
    var reputationScore by remember { mutableIntStateOf(0) }
    var badgeLevel by remember { mutableStateOf("Contributor") }
    var reportedCount by remember { mutableIntStateOf(0) }
    var verifiedCount by remember { mutableIntStateOf(0) }
    var resolvedCount by remember { mutableIntStateOf(0) }
    var isLoading by remember { mutableStateOf(true) }

    LaunchedEffect(Unit) {
        isLoading = true
        val statsObj = BackendClient.fetchProfileStats()
        if (statsObj != null) {
            reputationScore = statsObj.optInt("reputation_score", 0)
            badgeLevel = statsObj.optString("badge_level", "Contributor")
            val s = statsObj.optJSONObject("stats")
            if (s != null) {
                reportedCount = s.optInt("hazards_reported", 0)
                verifiedCount = s.optInt("hazards_verified", 0)
                resolvedCount = s.optInt("hazards_resolved", 0)
            }
        }
        isLoading = false
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("My Impact", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White) },
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
            LazyColumn(
                modifier = Modifier.fillMaxSize().padding(paddingValues),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                // Reputation score block
                item {
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        colors = CardDefaults.cardColors(containerColor = Color(0xFF1E2A3A)),
                        shape = RoundedCornerShape(16.dp)
                    ) {
                        Column(
                            modifier = Modifier.fillMaxWidth().padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text("REPUTATION POINTS", fontSize = 11.sp, color = Color.White.copy(0.7f), fontWeight = FontWeight.Bold, letterSpacing = 1.2.sp)
                            Spacer(Modifier.height(8.dp))
                            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                Icon(Icons.Default.Star, "Points", tint = Color(0xFFF59E0B), modifier = Modifier.size(36.dp))
                                Text(reputationScore.toString(), fontSize = 42.sp, fontWeight = FontWeight.Black, color = Color.White)
                            }
                            Spacer(Modifier.height(8.dp))
                            Box(
                                modifier = Modifier.background(Color(0xFF15803D), RoundedCornerShape(12.dp))
                                    .padding(horizontal = 12.dp, vertical = 4.dp)
                            ) {
                                Text(badgeLevel, fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color.White)
                            }
                        }
                    }
                }

                // Stats grid
                item {
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        shape = RoundedCornerShape(16.dp),
                        border = BorderStroke(1.dp, Color(0xFFE2E8F0))
                    ) {
                        Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
                            Text("CONTRIBUTIONS SUMMARY", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFF64748B), letterSpacing = 1.sp)
                            
                            ImpactStatRow("Hazards Filed", reportedCount, Color(0xFF1B4FD8))
                            HorizontalDivider(color = Color(0xFFF1F5F9))
                            ImpactStatRow("Reports Verified", verifiedCount, Color(0xFF15803D))
                            HorizontalDivider(color = Color(0xFFF1F5F9))
                            ImpactStatRow("Hazards Resolved", resolvedCount, Color(0xFF7E22CE))
                        }
                    }
                }

                // Gamification Info
                item {
                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        colors = CardDefaults.cardColors(containerColor = Color(0xFFFFEDD5)),
                        shape = RoundedCornerShape(16.dp),
                        border = BorderStroke(1.dp, Color(0xFFFFD8A8))
                    ) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Text("How to earn points?", fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color(0xFFC2410C))
                            Spacer(Modifier.height(6.dp))
                            Text("• Report a new verified hazard: +100 points", fontSize = 13.sp, color = Color(0xFF7C2D12))
                            Text("• Verify a report correctly: +20 points", fontSize = 13.sp, color = Color(0xFF7C2D12))
                            Text("• Successfully resolve/clear hazard: +150 points", fontSize = 13.sp, color = Color(0xFF7C2D12))
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun ImpactStatRow(label: String, count: Int, color: Color) {
    Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
        Text(label, fontWeight = FontWeight.SemiBold, fontSize = 14.sp, color = Color(0xFF1E293B))
        Box(
            modifier = Modifier.background(color.copy(0.1f), CircleShape).padding(horizontal = 12.dp, vertical = 4.dp),
            contentAlignment = Alignment.Center
        ) {
            Text(count.toString(), fontWeight = FontWeight.Bold, fontSize = 14.sp, color = color)
        }
    }
}
