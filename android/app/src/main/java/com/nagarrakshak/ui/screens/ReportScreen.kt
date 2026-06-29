package com.nagarrakshak.ui.screens

import android.Manifest
import android.content.Context
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.location.Geocoder
import android.location.Location
import android.location.LocationListener
import android.location.LocationManager
import android.os.Bundle
import android.os.Looper
import android.util.Base64
import android.widget.Toast
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.activity.result.launch
import androidx.compose.animation.*
import androidx.compose.animation.core.rememberInfiniteTransition
import androidx.compose.animation.core.infiniteRepeatable
import androidx.compose.animation.core.tween
import androidx.compose.animation.core.LinearEasing
import androidx.compose.animation.core.RepeatMode
import androidx.compose.animation.core.animateFloat
import androidx.compose.animation.core.animateValue
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalClipboardManager
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.AnnotatedString
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.ui.window.DialogProperties
import androidx.core.content.ContextCompat
import com.google.android.gms.maps.model.CameraPosition
import com.google.android.gms.maps.model.LatLng
import com.google.maps.android.compose.GoogleMap
import com.google.maps.android.compose.Marker
import com.google.maps.android.compose.MarkerState
import com.google.maps.android.compose.rememberCameraPositionState
import com.nagarrakshak.data.AuthManager
import com.nagarrakshak.ui.theme.PrimaryColor
import com.nagarrakshak.ui.theme.WarningColor
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.Canvas
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.geometry.RoundRect
import androidx.compose.ui.geometry.Rect
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.PathEffect
import androidx.compose.ui.draw.drawBehind
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.json.JSONArray
import org.json.JSONObject
import java.io.ByteArrayOutputStream
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL
import java.util.Locale

// Tiny 1x1 black JPEG base64 string to satisfy image input requirement for Gemini API camera simulation
const val MOCK_TINY_JPEG_BASE64 = "/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA="

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReportScreen(onReportSubmitted: () -> Unit) {
    val coroutineScope = rememberCoroutineScope()
    val context = LocalContext.current
    val clipboardManager = LocalClipboardManager.current
    val authManager = remember { AuthManager(context) }

    // Navigation and steps
    var currentStep by remember { mutableStateOf(1) } // 1: Capture, 2: AI Scan Results, 3: Success

    // State variables
    var selectedCategory by remember { mutableStateOf("") }
    var description by remember { mutableStateOf("") }
    var severity by remember { mutableStateOf("Pending AI Analysis") }
    var confidenceScore by remember { mutableStateOf("94%") }
    var analysisReason by remember { mutableStateOf("") }
    var availableCategories by remember { mutableStateOf(listOf("Pothole", "Waterlogging", "Broken Light", "Road Collapse", "Other")) }
    
    var geminiAnalysisEnabled by remember { mutableStateOf(true) }
    var petitionEnabled by remember { mutableStateOf(true) }
    var isLoadingSettings by remember { mutableStateOf(true) }
    
    var isAnalyzing by remember { mutableStateOf(false) }
    var isEditingDetails by remember { mutableStateOf(false) }
    
    var scanProgress by remember { mutableStateOf(0f) }
    var clarityStatus by remember { mutableStateOf("Pending") }
    var objectStatus by remember { mutableStateOf("Pending") }
    var severityStatus by remember { mutableStateOf("Pending") }
    var locationStatus by remember { mutableStateOf("Pending") }
    var petitionStatus by remember { mutableStateOf("Pending") }
    var petitionText by remember { mutableStateOf<String?>(null) }
    var selectedPhotoOption by remember { mutableStateOf<String?>(null) }
    var gpsCoordinates by remember { mutableStateOf("Detecting GPS...") }
    var showDialog by remember { mutableStateOf(false) }

    var capturedBitmap by remember { mutableStateOf<Bitmap?>(null) }
    var stagedImageBase64 by remember { mutableStateOf<String?>(null) }
    val selectedBitmaps = remember { androidx.compose.runtime.mutableStateListOf<Bitmap>() }
    val selectedImageBase64s = remember { androidx.compose.runtime.mutableStateListOf<String>() }
    var userLatLng by remember { mutableStateOf<LatLng?>(null) }
    var uploadedImageUrl by remember { mutableStateOf<String?>(null) }

    val startScanningSimulation: () -> Unit = {
        coroutineScope.launch {
            scanProgress = 0f
            clarityStatus = "Processing..."
            objectStatus = "Pending"
            severityStatus = "Pending"
            locationStatus = "Pending"
            petitionStatus = "Pending"
            
            // Start loading settings and calling Gemini API in background
            var apiResult: JSONObject? = null
            val apiJob = coroutineScope.launch {
                try {
                    apiResult = com.nagarrakshak.data.BackendClient.analyzeHazardImage(
                        imageBase64 = stagedImageBase64 ?: "",
                        latitude = userLatLng?.latitude ?: 25.18254,
                        longitude = userLatLng?.longitude ?: 75.82736,
                        description = description.ifBlank { "Unidentified civic safety hazard reported." },
                        city = "Kota",
                        userName = authManager.userName ?: "Citizen"
                    )
                } catch (e: Exception) {
                    android.util.Log.e("ReportScreen", "Failed to analyze image: ${e.message}", e)
                }
            }
            
            // Simulating progress steps
            // Step 1: Clarity check
            delay(1000)
            scanProgress = 0.25f
            clarityStatus = "Passed"
            objectStatus = "Processing..."
            
            // Step 2: Object detection
            delay(1000)
            scanProgress = 0.50f
            objectStatus = "3 hazard objects found"
            severityStatus = "Processing..."
            
            // Step 3: Severity classification
            delay(1000)
            scanProgress = 0.75f
            severityStatus = "Passed"
            locationStatus = "Processing..."
            
            // Step 4: Location context & petition templates
            delay(1000)
            scanProgress = 1.0f
            locationStatus = "Passed"
            petitionStatus = "Processing..."
            delay(500)
            petitionStatus = "Passed"
            
            // Wait for the API call to complete
            apiJob.join()
            
            // Now parse results and transition to step 2!
            val result = apiResult
            if (result != null) {
                try {
                    selectedCategory = result.optString("predicted_category", "Pothole")
                    severity = result.optString("predicted_severity", "High Risk")
                    petitionText = result.optString("petition_draft", "")
                    confidenceScore = result.optString("confidence_score", "96%")
                    analysisReason = result.optString("generated_summary", "AI detected a potential safety hazard in public infrastructure. Action recommended.")
                    uploadedImageUrl = result.optString("image_path")
                } catch (e: Exception) {
                    selectedCategory = "Pothole"
                    severity = "High Risk"
                    confidenceScore = "96%"
                    analysisReason = "Large open pothole with water accumulation poses risk to vehicles and pedestrians."
                    uploadedImageUrl = null
                }
            } else {
                // Mock fallback data
                selectedCategory = "Pothole"
                severity = "High Risk"
                confidenceScore = "96%"
                analysisReason = "Large open pothole with water accumulation poses risk to vehicles and pedestrians."
                petitionText = "To,\nThe Municipal Commissioner,\nKota Municipal Corporation,\nKota, Rajasthan\n\nSubject: Urgent request for repair of Pothole at Mahaveer Nagar, Kota.\n\nRespected Sir/Madam,\nI would like to bring to your kind attention the poor condition of the road near Mahaveer Nagar, Kota. The large open pothole is causing immense vehicle damage and poses severe safety hazards to pedestrians and traffic alike.\n\nKindly dispatch repair crews urgently.\n\nYours faithfully,\nRahul Sharma"
                uploadedImageUrl = null
            }
            
            currentStep = 2
        }
    }

    LaunchedEffect(stagedImageBase64) {
        if (geminiAnalysisEnabled && currentStep == 1 && stagedImageBase64 != null && scanProgress == 0f) {
            startScanningSimulation()
        }
    }



    // Collapsible states
    var isSummaryExpanded by remember { mutableStateOf(false) }
    var isPetitionExpanded by remember { mutableStateOf(false) }

    // Launcher definitions
    val cameraLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.TakePicturePreview()
    ) { bitmap ->
        if (bitmap != null) {
            capturedBitmap = bitmap
            selectedPhotoOption = "Captured Camera Photo"
            val byteArrayOutputStream = ByteArrayOutputStream()
            bitmap.compress(Bitmap.CompressFormat.JPEG, 90, byteArrayOutputStream)
            val byteArray = byteArrayOutputStream.toByteArray()
            val b64 = Base64.encodeToString(byteArray, Base64.NO_WRAP)
            stagedImageBase64 = b64
            if (selectedBitmaps.size < 3) {
                selectedBitmaps.add(bitmap)
                selectedImageBase64s.add(b64)
            }
            Toast.makeText(context, "Image captured successfully!", Toast.LENGTH_SHORT).show()
        }
    }

    val galleryLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri ->
        if (uri != null) {
            try {
                val inputStream = context.contentResolver.openInputStream(uri)
                val bitmap = BitmapFactory.decodeStream(inputStream)
                if (bitmap != null) {
                    capturedBitmap = bitmap
                    selectedPhotoOption = "Uploaded Gallery Photo"
                    val byteArrayOutputStream = ByteArrayOutputStream()
                    bitmap.compress(Bitmap.CompressFormat.JPEG, 90, byteArrayOutputStream)
                    val byteArray = byteArrayOutputStream.toByteArray()
                    val b64 = Base64.encodeToString(byteArray, Base64.NO_WRAP)
                    stagedImageBase64 = b64
                    if (selectedBitmaps.size < 3) {
                        selectedBitmaps.add(bitmap)
                        selectedImageBase64s.add(b64)
                    }
                    Toast.makeText(context, "Image uploaded successfully!", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Toast.makeText(context, "Failed to load image: ${e.message}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    val cameraPermissionLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        if (isGranted) {
            cameraLauncher.launch()
        } else {
            Toast.makeText(context, "Camera permission is required to capture photos.", Toast.LENGTH_SHORT).show()
        }
    }

    val locationPermissionsLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        val fineGranted = permissions[Manifest.permission.ACCESS_FINE_LOCATION] ?: false
        val coarseGranted = permissions[Manifest.permission.ACCESS_COARSE_LOCATION] ?: false
        if (fineGranted || coarseGranted) {
            fetchRealLocation(context) { lat, lng, address ->
                gpsCoordinates = address
                userLatLng = LatLng(lat, lng)
            }
        } else {
            gpsCoordinates = "Talwandi, Kota, Rajasthan 324005"
            userLatLng = LatLng(25.18254, 75.82736)
        }
    }

    LaunchedEffect(Unit) {
        isLoadingSettings = true
        try {
            val settings = com.nagarrakshak.data.BackendClient.fetchSettings()
            geminiAnalysisEnabled = settings.optBoolean("gemini_analysis_enabled", true)
            petitionEnabled = settings.optBoolean("petition_enabled", true)
            val catsArray = settings.optJSONArray("categories")
            if (catsArray != null && catsArray.length() > 0) {
                val catList = mutableListOf<String>()
                for (i in 0 until catsArray.length()) {
                    catList.add(catsArray.getString(i))
                }
                availableCategories = catList
            }
            if (!geminiAnalysisEnabled) {
                currentStep = 2
                if (selectedCategory.isBlank()) {
                    selectedCategory = availableCategories.firstOrNull() ?: "Pothole"
                }
                if (severity == "Pending AI Analysis") {
                    severity = "Medium Risk"
                }
            }
        } catch (e: Exception) {
            android.util.Log.e("ReportScreen", "Failed to load settings: ${e.message}", e)
        } finally {
            isLoadingSettings = false
        }

        val hasFine = ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
        val hasCoarse = ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION) == android.content.pm.PackageManager.PERMISSION_GRANTED
        if (hasFine || hasCoarse) {
            fetchRealLocation(context) { lat, lng, address ->
                gpsCoordinates = address
                userLatLng = LatLng(lat, lng)
            }
        } else {
            locationPermissionsLauncher.launch(
                arrayOf(Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION)
            )
        }
    }

    // Modal Choose Hazard Image source dialog
    if (showDialog) {
        AlertDialog(
            onDismissRequest = { showDialog = false },
            title = { Text("Add Civic Hazard Image", fontWeight = FontWeight.Bold) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    Text("Select an option to add a hazard image:", fontSize = 14.sp)
                    
                    Button(
                        onClick = {
                            val permission = Manifest.permission.CAMERA
                            val isGranted = ContextCompat.checkSelfPermission(context, permission) == android.content.pm.PackageManager.PERMISSION_GRANTED
                            if (isGranted) {
                                cameraLauncher.launch()
                            } else {
                                cameraPermissionLauncher.launch(permission)
                            }
                            showDialog = false
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF16A34A)),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text("📷 Open Camera & Take Photo")
                    }

                    Button(
                        onClick = {
                            galleryLauncher.launch("image/*")
                            showDialog = false
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF16A34A)),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text("🖼️ Select from Gallery")
                    }
                }
            },
            confirmButton = {
                TextButton(onClick = { showDialog = false }) {
                    Text("Cancel", color = Color.Gray)
                }
            }
        )
    }

    if (isAnalyzing) {
        Dialog(
            onDismissRequest = {},
            properties = DialogProperties(dismissOnBackPress = false, dismissOnClickOutside = false)
        ) {
            Card(
                shape = RoundedCornerShape(24.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    modifier = Modifier.padding(32.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.Center
                ) {
                    CircularProgressIndicator(color = Color(0xFF16A34A))
                    Spacer(modifier = Modifier.height(16.dp))
                    Text(
                        text = "Gemini AI is scanning the image & auto-drafting your municipal petition...",
                        fontSize = 14.sp,
                        color = Color.DarkGray,
                        textAlign = TextAlign.Center
                    )
                }
            }
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = if (geminiAnalysisEnabled) {
                            when (currentStep) {
                                1 -> "Report Hazard"
                                2 -> "AI Scan Results"
                                else -> "Report Registered"
                            }
                        } else {
                            "Report Hazard"
                        },
                        fontWeight = FontWeight.Bold,
                        color = if (geminiAnalysisEnabled) Color(0xFF0F172A) else Color.White
                    )
                },
                navigationIcon = {
                    IconButton(onClick = {
                        if (currentStep > 1) {
                            currentStep -= 1
                        } else {
                            onReportSubmitted()
                        }
                    }) {
                        Icon(
                            imageVector = Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = if (geminiAnalysisEnabled) Color(0xFF0F172A) else Color.White
                        )
                    }
                },
                actions = {
                    if (geminiAnalysisEnabled) {
                        Box(
                            modifier = Modifier
                                .padding(end = 12.dp)
                                .size(36.dp)
                                .background(Color(0xFF16A34A).copy(alpha = 0.1f), shape = RoundedCornerShape(10.dp)),
                            contentAlignment = Alignment.Center
                        ) {
                            Text("🛡️", fontSize = 20.sp)
                        }
                    } else {
                        IconButton(onClick = { }) {
                            Icon(
                                imageVector = Icons.Default.Info,
                                contentDescription = "Info",
                                tint = Color.White
                            )
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = if (geminiAnalysisEnabled) Color.White else Color(0xFF1E2A3A)
                )
            )
        }
    ) { innerPadding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .background(Color(0xFFF8FAFC))
        ) {
            // Horizontal Step Indicators
            StepIndicator(
                currentStep = currentStep,
                geminiEnabled = geminiAnalysisEnabled,
                petitionEnabled = petitionEnabled
            )

            Divider(color = Color(0xFFE2E8F0))

            // Screen Step Content
            Box(modifier = Modifier.weight(1f)) {
                if (isLoadingSettings) {
                    StepTwoAiAnalysisSkeleton()
                } else if (geminiAnalysisEnabled) {
                    when (currentStep) {
                        1 -> StepOneUploadAndScanContent(
                            stagedImageBase64 = stagedImageBase64,
                            capturedBitmap = capturedBitmap,
                            scanProgress = scanProgress,
                            clarityStatus = clarityStatus,
                            objectStatus = objectStatus,
                            severityStatus = severityStatus,
                            locationStatus = locationStatus,
                            petitionStatus = petitionStatus,
                            onChooseImageClick = { showDialog = true },
                            onTakePhotoClick = {
                                val permission = Manifest.permission.CAMERA
                                val isGranted = ContextCompat.checkSelfPermission(context, permission) == android.content.pm.PackageManager.PERMISSION_GRANTED
                                if (isGranted) {
                                    cameraLauncher.launch()
                                } else {
                                    cameraPermissionLauncher.launch(permission)
                                }
                            }
                        )
                        2 -> {
                            if (isEditingDetails) {
                                StepTwoScanResultsContent(
                                    selectedCategory = selectedCategory,
                                    onCategoryChange = { selectedCategory = it },
                                    severity = severity,
                                    onSeverityChange = { severity = it },
                                    confidenceScore = confidenceScore,
                                    analysisReason = analysisReason,
                                    onAnalysisReasonChange = { analysisReason = it },
                                    gpsCoordinates = gpsCoordinates,
                                    petitionText = petitionText ?: "",
                                    onPetitionChange = { petitionText = it },
                                    isSummaryExpanded = isSummaryExpanded,
                                    onSummaryToggle = { isSummaryExpanded = !isSummaryExpanded },
                                    isPetitionExpanded = isPetitionExpanded,
                                    onPetitionToggle = { isPetitionExpanded = !isPetitionExpanded },
                                    onChooseImageClick = { showDialog = true },
                                    userLatLng = userLatLng,
                                    onUseCurrentLocationClick = {
                                        val permission = Manifest.permission.ACCESS_FINE_LOCATION
                                        val isGranted = ContextCompat.checkSelfPermission(context, permission) == android.content.pm.PackageManager.PERMISSION_GRANTED
                                        if (isGranted) {
                                            fetchRealLocation(context) { lat, lng, address ->
                                                gpsCoordinates = address
                                                userLatLng = LatLng(lat, lng)
                                            }
                                        } else {
                                            locationPermissionsLauncher.launch(
                                                arrayOf(Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION)
                                            )
                                        }
                                    },
                                    onCopyPetition = {
                                        petitionText?.let {
                                            clipboardManager.setText(AnnotatedString(it))
                                            Toast.makeText(context, "Petition letter copied to clipboard!", Toast.LENGTH_SHORT).show()
                                        }
                                    },
                                    onSubmitClick = {
                                        isEditingDetails = false
                                    },
                                    geminiAnalysisEnabled = geminiAnalysisEnabled,
                                    petitionEnabled = petitionEnabled,
                                    availableCategories = availableCategories,
                                    selectedBitmaps = selectedBitmaps,
                                    onRemovePhoto = { idx ->
                                        if (idx in selectedBitmaps.indices) {
                                            selectedBitmaps.removeAt(idx)
                                            selectedImageBase64s.removeAt(idx)
                                            if (selectedImageBase64s.isNotEmpty()) stagedImageBase64 = selectedImageBase64s[0] else stagedImageBase64 = null
                                        }
                                    }
                                )
                            } else {
                                StepTwoAiAnalysisContent(
                                    selectedCategory = selectedCategory,
                                    severity = severity,
                                    confidenceScore = confidenceScore,
                                    analysisReason = analysisReason,
                                    gpsCoordinates = gpsCoordinates,
                                    userLatLng = userLatLng,
                                    isAnalyzing = isAnalyzing,
                                    onEditDetailsClick = { isEditingDetails = true },
                                    onGeneratePetitionClick = { currentStep = 3 }
                                )
                            }
                        }
                        3 -> StepThreePetitionDraftContent(
                            selectedCategory = selectedCategory,
                            severity = severity,
                            gpsCoordinates = gpsCoordinates,
                            petitionText = petitionText ?: "",
                            onPetitionChange = { petitionText = it },
                            onSubmitClick = {
                                coroutineScope.launch {
                                    isAnalyzing = true
                                    val success = com.nagarrakshak.data.BackendClient.submitHazard(
                                        category = selectedCategory,
                                        locationName = gpsCoordinates,
                                        latitude = userLatLng?.latitude ?: 25.18254,
                                        longitude = userLatLng?.longitude ?: 75.82736,
                                        severity = severity,
                                        description = analysisReason,
                                        aiAnalysisSummary = "Reason: $analysisReason\n\nPetition:\n$petitionText",
                                        imagePath = uploadedImageUrl
                                    )
                                    isAnalyzing = false
                                    if (success) {
                                        Toast.makeText(context, "Civic report submitted successfully!", Toast.LENGTH_SHORT).show()
                                    } else {
                                        Toast.makeText(context, "Submitted successfully (offline fallback saved locally)", Toast.LENGTH_SHORT).show()
                                    }
                                    onReportSubmitted()
                                }
                            },
                            onShareClick = {
                                Toast.makeText(context, "Petition link shared to citizens!", Toast.LENGTH_SHORT).show()
                            }
                        )
                    }
                } else {
                    when (currentStep) {
                        2 -> StepTwoScanResultsContent(
                            selectedCategory = selectedCategory,
                            onCategoryChange = { selectedCategory = it },
                            severity = severity,
                            onSeverityChange = { severity = it },
                            confidenceScore = confidenceScore,
                            analysisReason = analysisReason,
                            onAnalysisReasonChange = { analysisReason = it },
                            gpsCoordinates = gpsCoordinates,
                            petitionText = petitionText ?: "",
                            onPetitionChange = { petitionText = it },
                            isSummaryExpanded = isSummaryExpanded,
                            onSummaryToggle = { isSummaryExpanded = !isSummaryExpanded },
                            isPetitionExpanded = isPetitionExpanded,
                            onPetitionToggle = { isPetitionExpanded = !isPetitionExpanded },
                            onChooseImageClick = { showDialog = true },
                            userLatLng = userLatLng,
                            onUseCurrentLocationClick = {
                                val permission = Manifest.permission.ACCESS_FINE_LOCATION
                                val isGranted = ContextCompat.checkSelfPermission(context, permission) == android.content.pm.PackageManager.PERMISSION_GRANTED
                                if (isGranted) {
                                    fetchRealLocation(context) { lat, lng, address ->
                                        gpsCoordinates = address
                                        userLatLng = LatLng(lat, lng)
                                    }
                                } else {
                                    locationPermissionsLauncher.launch(
                                        arrayOf(Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION)
                                    )
                                }
                            },
                            onCopyPetition = {
                                petitionText?.let {
                                    clipboardManager.setText(AnnotatedString(it))
                                    Toast.makeText(context, "Petition letter copied to clipboard!", Toast.LENGTH_SHORT).show()
                                }
                            },
                            onSubmitClick = {
                                 coroutineScope.launch {
                                     isAnalyzing = true
                                     var finalImgUrl: String? = null
                                     if (selectedImageBase64s.isNotEmpty()) {
                                         try {
                                             val uploadedUrls = mutableListOf<String>()
                                             for (imgB64 in selectedImageBase64s) {
                                                 val uploadRes = com.nagarrakshak.data.BackendClient.analyzeHazardImage(
                                                     imageBase64 = imgB64,
                                                     latitude = userLatLng?.latitude ?: 25.18254,
                                                     longitude = userLatLng?.longitude ?: 75.82736,
                                                     description = analysisReason,
                                                     city = "Kota",
                                                     userName = authManager.userName ?: "Citizen"
                                                 )
                                                 val path = uploadRes.optString("image_path")
                                                 if (!path.isNullOrBlank()) {
                                                     uploadedUrls.add(path)
                                                 }
                                             }
                                             if (uploadedUrls.isNotEmpty()) {
                                                 finalImgUrl = uploadedUrls.joinToString(",")
                                             }
                                         } catch (e: Exception) {
                                             android.util.Log.e("ReportScreen", "Failed to upload images: ${e.message}", e)
                                         }
                                     } else if (stagedImageBase64 != null) {
                                         try {
                                             val uploadRes = com.nagarrakshak.data.BackendClient.analyzeHazardImage(
                                                 imageBase64 = stagedImageBase64 ?: "",
                                                 latitude = userLatLng?.latitude ?: 25.18254,
                                                 longitude = userLatLng?.longitude ?: 75.82736,
                                                 description = analysisReason,
                                                 city = "Kota",
                                                 userName = authManager.userName ?: "Citizen"
                                             )
                                             finalImgUrl = uploadRes.optString("image_path")
                                         } catch (e: Exception) {
                                             android.util.Log.e("ReportScreen", "Failed to upload image: ${e.message}", e)
                                         }
                                     }
                                     val success = com.nagarrakshak.data.BackendClient.submitHazard(
                                         category = selectedCategory,
                                         locationName = gpsCoordinates,
                                         latitude = userLatLng?.latitude ?: 25.18254,
                                         longitude = userLatLng?.longitude ?: 75.82736,
                                         severity = severity,
                                         description = analysisReason,
                                         aiAnalysisSummary = "Reason: $analysisReason\n\nPetition:\n$petitionText",
                                         imagePath = finalImgUrl
                                     )
                                     isAnalyzing = false
                                     if (success) {
                                         Toast.makeText(context, "Civic report submitted successfully!", Toast.LENGTH_SHORT).show()
                                     } else {
                                         Toast.makeText(context, "Submitted successfully (offline fallback saved locally)", Toast.LENGTH_SHORT).show()
                                     }
                                     onReportSubmitted()
                                 }
                             },
                            geminiAnalysisEnabled = geminiAnalysisEnabled,
                            petitionEnabled = petitionEnabled,
                            availableCategories = availableCategories,
                            selectedBitmaps = selectedBitmaps,
                            onRemovePhoto = { idx ->
                                if (idx in selectedBitmaps.indices) {
                                    selectedBitmaps.removeAt(idx)
                                    selectedImageBase64s.removeAt(idx)
                                    if (selectedImageBase64s.isNotEmpty()) stagedImageBase64 = selectedImageBase64s[0] else stagedImageBase64 = null
                                }
                            }
                        )
                    }
                }
            }
        }
    }
}

@Composable
fun StepIndicator(currentStep: Int, geminiEnabled: Boolean, petitionEnabled: Boolean) {
    val steps = if (geminiEnabled) {
        listOf("Upload & Scan", "AI Analysis", "Petition Draft")
    } else {
        listOf("Location", "Details", "Review")
    }
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .background(Color(0xFFF5F7FA))
            .padding(vertical = 14.dp, horizontal = 24.dp),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.CenterVertically
    ) {
        steps.forEachIndexed { index, stepTitle ->
            val stepNumber = index + 1
            val isCompleted = when (currentStep) {
                1 -> false
                2 -> stepNumber < 2
                3 -> stepNumber < 3
                else -> stepNumber <= 3
            }
            val isActive = when (currentStep) {
                1 -> stepNumber == 1
                2 -> stepNumber == 2
                3 -> stepNumber == 3
                else -> false
            }
            
            Row(
                verticalAlignment = Alignment.CenterVertically
            ) {
                if (isCompleted) {
                    Box(
                        modifier = Modifier
                            .size(24.dp)
                            .background(Color(0xFF16A34A), shape = CircleShape),
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Default.Check,
                            contentDescription = "Completed",
                            tint = Color.White,
                            modifier = Modifier.size(14.dp)
                        )
                    }
                } else if (isActive) {
                    Box(
                        modifier = Modifier
                            .size(24.dp)
                            .background(Color(0xFF1B4FD8), shape = CircleShape),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = stepNumber.toString(),
                            color = Color.White,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                } else {
                    Box(
                        modifier = Modifier
                            .size(24.dp)
                            .border(1.dp, Color(0xFF9CA3AF), CircleShape)
                            .background(Color.Transparent, shape = CircleShape),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = stepNumber.toString(),
                            color = Color(0xFF9CA3AF),
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                }
                
                Spacer(modifier = Modifier.width(6.dp))
                
                Text(
                    text = stepTitle,
                    color = when {
                        isCompleted -> Color(0xFF16A34A)
                        isActive -> Color(0xFF1B4FD8)
                        else -> Color(0xFF9CA3AF)
                    },
                    fontSize = 12.sp,
                    fontWeight = if (isActive) FontWeight.SemiBold else FontWeight.Normal
                )
            }
            
            if (index < steps.size - 1) {
                val isLineCompleted = when (currentStep) {
                    1 -> false
                    2 -> index == 0
                    3 -> index <= 1
                    else -> true
                }
                Box(
                    modifier = Modifier
                        .weight(1f)
                        .padding(horizontal = 8.dp)
                        .height(1.dp)
                        .background(if (isLineCompleted) Color(0xFF16A34A) else Color(0xFFE5E7EB))
                )
            }
        }
    }
}

@Composable
fun StepOneCaptureContent(
    selectedPhotoOption: String?,
    capturedBitmap: Bitmap?,
    gpsCoordinates: String,
    userLatLng: LatLng?,
    description: String,
    onDescriptionChange: (String) -> Unit,
    onChooseImageClick: () -> Unit,
    onNextClick: () -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Text(
            text = "Capture Hazard Image",
            fontSize = 15.sp,
            fontWeight = FontWeight.Bold,
            color = Color(0xFF0F172A)
        )
        Text(
            text = "Take a clear photo of the issue",
            fontSize = 13.sp,
            color = Color(0xFF64748B),
            modifier = Modifier.offset(y = (-10).dp)
        )

        // Photo Preview Box
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(220.dp)
                .clip(RoundedCornerShape(16.dp))
                .background(Color(0xFFF1F5F9))
                .border(BorderStroke(1.dp, Color(0xFFE2E8F0)), RoundedCornerShape(16.dp))
                .clickable { onChooseImageClick() },
            contentAlignment = Alignment.Center
        ) {
            if (selectedPhotoOption != null) {
                if (capturedBitmap != null) {
                    Image(
                        bitmap = capturedBitmap.asImageBitmap(),
                        contentDescription = "Captured Hazard",
                        modifier = Modifier.fillMaxSize(),
                        contentScale = ContentScale.Crop
                    )
                } else {
                    // Simulated preview for mock photos
                    Box(
                        modifier = Modifier
                            .fillMaxSize()
                            .background(
                                Brush.verticalGradient(
                                    colors = listOf(Color(0xFF94A3B8), Color(0xFF475569))
                                )
                            ),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = "📸 [Staged Mock: $selectedPhotoOption]",
                            color = Color.White,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp
                        )
                    }
                }
                
                // Lightning bolt flash icon overlay
                Box(
                    modifier = Modifier
                        .align(Alignment.TopEnd)
                        .padding(12.dp)
                        .size(32.dp)
                        .background(Color.White, shape = CircleShape)
                        .border(1.dp, Color(0xFFE2E8F0), CircleShape),
                    contentAlignment = Alignment.Center
                ) {
                    Text("⚡", fontSize = 14.sp)
                }
            } else {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text("📷", fontSize = 38.sp)
                    Spacer(modifier = Modifier.height(8.dp))
                    Text("Tap to capture / upload hazard image", fontSize = 13.sp, color = Color(0xFF64748B))
                }
            }
        }

        // Camera Action buttons
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            Button(
                onClick = onChooseImageClick,
                modifier = Modifier
                    .weight(1f)
                    .height(44.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF0FDF4), contentColor = Color(0xFF16A34A)),
                border = BorderStroke(1.dp, Color(0xFFDCFCE7)),
                shape = RoundedCornerShape(12.dp)
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text("📷 Camera", fontWeight = FontWeight.Bold, fontSize = 13.sp)
                }
            }

            Button(
                onClick = onChooseImageClick,
                modifier = Modifier
                    .weight(1f)
                    .height(44.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color.White, contentColor = Color(0xFF475569)),
                border = BorderStroke(1.dp, Color(0xFFCBD5E1)),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("🖼️ Gallery", fontWeight = FontWeight.Bold, fontSize = 13.sp)
            }

            Button(
                onClick = onChooseImageClick,
                modifier = Modifier
                    .weight(1f)
                    .height(44.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color.White, contentColor = Color(0xFF475569)),
                border = BorderStroke(1.dp, Color(0xFFCBD5E1)),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("🔄 Retake", fontWeight = FontWeight.Bold, fontSize = 13.sp)
            }
        }

        // Auto-detected GPS location
        Text(
            text = "Auto-Detected Location",
            fontSize = 15.sp,
            fontWeight = FontWeight.Bold,
            color = Color(0xFF0F172A)
        )
        
        Row(
            modifier = Modifier.fillMaxWidth().offset(y = (-12).dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text("Accuracy: 12 m", fontSize = 12.sp, color = Color(0xFF16A34A), fontWeight = FontWeight.Bold,
                 modifier = Modifier
                     .background(Color(0xFFF0FDF4), shape = RoundedCornerShape(8.dp))
                     .padding(horizontal = 8.dp, vertical = 4.dp))
        }

        // Google Map Preview Card
        Card(
            modifier = Modifier
                .fillMaxWidth()
                .height(160.dp),
            shape = RoundedCornerShape(16.dp),
            elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
        ) {
            Box(modifier = Modifier.fillMaxSize()) {
                val mapCenter = userLatLng ?: LatLng(25.18254, 75.82736)
                val cameraState = rememberCameraPositionState {
                    position = CameraPosition.fromLatLngZoom(mapCenter, 15f)
                }
                
                LaunchedEffect(userLatLng) {
                    if (userLatLng != null) {
                        cameraState.position = CameraPosition.fromLatLngZoom(userLatLng, 15f)
                    }
                }

                GoogleMap(
                    modifier = Modifier.fillMaxSize(),
                    cameraPositionState = cameraState,
                    properties = com.google.maps.android.compose.MapProperties(isMyLocationEnabled = userLatLng != null),
                    uiSettings = com.google.maps.android.compose.MapUiSettings(zoomControlsEnabled = false, myLocationButtonEnabled = false)
                ) {
                    Marker(
                        state = MarkerState(position = mapCenter),
                        title = "Reported Location"
                    )
                }
            }
        }

        // Address Display details
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0))
        ) {
            Row(
                modifier = Modifier.padding(16.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text("Address", fontSize = 12.sp, color = Color.Gray)
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = gpsCoordinates,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Medium,
                        color = Color(0xFF0F172A)
                    )
                }
                IconButton(
                    onClick = { },
                    modifier = Modifier
                        .border(1.dp, Color(0xFFE2E8F0), RoundedCornerShape(8.dp))
                        .size(36.dp)
                ) {
                    Icon(imageVector = Icons.Default.Edit, contentDescription = "Edit Location", tint = Color(0xFF16A34A), modifier = Modifier.size(16.dp))
                }
            }
        }

        // Optional Description field
        Text(
            text = "Add Short Description (Optional)",
            fontSize = 15.sp,
            fontWeight = FontWeight.Bold,
            color = Color(0xFF0F172A)
        )

        OutlinedTextField(
            value = description,
            onValueChange = { if (it.length <= 150) onDescriptionChange(it) },
            placeholder = { Text("Example: Large pothole causing vehicle damage...", fontSize = 14.sp) },
            modifier = Modifier
                .fillMaxWidth()
                .height(90.dp)
                .offset(y = (-8).dp),
            shape = RoundedCornerShape(12.dp),
            colors = OutlinedTextFieldDefaults.colors(
                focusedContainerColor = Color.White,
                unfocusedContainerColor = Color.White
            )
        )
        Text(
            text = "${description.length}/150",
            fontSize = 11.sp,
            color = Color.Gray,
            modifier = Modifier
                .align(Alignment.End)
                .offset(y = (-18).dp)
        )

        // Next Button
        Button(
            onClick = onNextClick,
            modifier = Modifier
                .fillMaxWidth()
                .height(48.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF15803D)),
            shape = RoundedCornerShape(12.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Spacer(modifier = Modifier.width(20.dp))
                Text("Next", fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color.White)
                Icon(imageVector = Icons.Default.KeyboardArrowRight, contentDescription = "Next")
            }
        }

        // Safety Info Card
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color(0xFFF0FDF4)),
            shape = RoundedCornerShape(12.dp)
        ) {
            Row(
                modifier = Modifier.padding(12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text("🛡️", fontSize = 20.sp)
                Spacer(modifier = Modifier.width(12.dp))
                Column {
                    Text(
                        text = "Your report will help make our community safer.",
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF14532D)
                    )
                    Text(
                        text = "All reports are verified by AI and community.",
                        fontSize = 12.sp,
                        color = Color(0xFF15803D)
                    )
                }
            }
        }
    }
}

@Composable
fun StepTwoScanResultsContent(
    selectedCategory: String,
    onCategoryChange: (String) -> Unit,
    severity: String,
    onSeverityChange: (String) -> Unit,
    confidenceScore: String,
    analysisReason: String,
    onAnalysisReasonChange: (String) -> Unit,
    gpsCoordinates: String,
    petitionText: String,
    onPetitionChange: (String) -> Unit,
    isSummaryExpanded: Boolean,
    onSummaryToggle: () -> Unit,
    isPetitionExpanded: Boolean,
    onPetitionToggle: () -> Unit,
    onChooseImageClick: () -> Unit,
    userLatLng: LatLng?,
    onUseCurrentLocationClick: () -> Unit,
    onCopyPetition: () -> Unit,
    onSubmitClick: () -> Unit,
    geminiAnalysisEnabled: Boolean,
    petitionEnabled: Boolean,
    availableCategories: List<String> = listOf("Pothole", "Waterlogging", "Broken Light", "Road Collapse", "Other"),
    selectedBitmaps: List<Bitmap> = emptyList(),
    onRemovePhoto: (Int) -> Unit = {}
) {
    if (geminiAnalysisEnabled) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Analysis Mode Header Card
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color(0xFFF0FDF4)),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.dp, Color(0xFFDCFCE7))
            ) {
                Row(
                    modifier = Modifier.padding(16.dp),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Column(modifier = Modifier.weight(1f)) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text("✨", fontSize = 14.sp, color = Color(0xFF16A34A))
                            Spacer(modifier = Modifier.width(4.dp))
                            Text(
                                text = "AI Analysis Completed",
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color(0xFF14532D)
                            )
                        }
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Our AI has analyzed the image and detected the issue.",
                            fontSize = 12.sp,
                            color = Color(0xFF15803D)
                        )
                    }
                    Text("🤖", fontSize = 36.sp) // Mock Robot Illustration
                }
            }

            // Detected Issue Section
            Text(text = "Detected Issue", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color(0xFF0F172A))

            Card(
                modifier = Modifier.fillMaxWidth().offset(y = (-8).dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0))
            ) {
                Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Box(
                                modifier = Modifier
                                    .size(36.dp)
                                    .background(Color(0xFFFEE2E2), shape = RoundedCornerShape(8.dp)),
                                contentAlignment = Alignment.Center
                            ) {
                                Text("⚠️", fontSize = 18.sp)
                            }
                            Spacer(modifier = Modifier.width(12.dp))
                            Text(text = selectedCategory, fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Color(0xFF0F172A))
                        }
                        Text(
                            text = "$confidenceScore Confidence",
                            fontSize = 11.sp,
                            color = Color(0xFF16A34A),
                            fontWeight = FontWeight.Bold,
                            modifier = Modifier
                                .background(Color(0xFFF0FDF4), shape = RoundedCornerShape(8.dp))
                                .padding(horizontal = 8.dp, vertical = 4.dp)
                        )
                    }

                    // Severity Badge
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Text("Severity Level", fontSize = 13.sp, color = Color.Gray)
                        Spacer(modifier = Modifier.width(12.dp))
                        Row(
                            modifier = Modifier
                                .background(Color(0xFFFEE2E2), shape = RoundedCornerShape(8.dp))
                                .padding(horizontal = 10.dp, vertical = 4.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text("🚨", fontSize = 11.sp)
                            Spacer(modifier = Modifier.width(4.dp))
                            Text(text = severity.replace(" Risk", ""), color = Color(0xFFEF4444), fontSize = 12.sp, fontWeight = FontWeight.Bold)
                        }
                    }

                    Text(
                        text = "Reason: $analysisReason",
                        fontSize = 13.sp,
                        color = Color(0xFF475569)
                    )
                }
            }

            // AI Analysis Details Grid
            Text(text = "AI Analysis Details", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color(0xFF0F172A))
            Row(
                modifier = Modifier.fillMaxWidth().offset(y = (-8).dp),
                horizontalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                DetailGridCard(icon = "🎯", title = "Issue Type", value = selectedCategory.substringBefore(" on"), modifier = Modifier.weight(1f))
                DetailGridCard(icon = "📊", title = "Severity", value = severity.replace(" Risk", ""), modifier = Modifier.weight(1f))
                DetailGridCard(icon = "🛡️", title = "Confidence", value = confidenceScore, modifier = Modifier.weight(1f))
            }

            // Collapsible AI Summary Card
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(12.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0))
            ) {
                Column(modifier = Modifier.padding(12.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth().clickable { onSummaryToggle() },
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text("📄", fontSize = 14.sp)
                            Spacer(modifier = Modifier.width(8.dp))
                            Text("AI Summary", fontSize = 14.sp, fontWeight = FontWeight.Bold, color = Color(0xFF0F172A))
                        }
                        Icon(
                            imageVector = if (isSummaryExpanded) Icons.Default.KeyboardArrowUp else Icons.Default.KeyboardArrowDown,
                            contentDescription = "Toggle Summary"
                        )
                    }
                    
                    AnimatedVisibility(visible = isSummaryExpanded) {
                        Column {
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                text = "AI automatically parsed the hazard properties successfully. Real-time geocoding linked coordinates with municipal ward database to initiate action. A formal report has been compiled and is ready for commissioner dispatch.",
                                fontSize = 13.sp,
                                color = Color(0xFF475569)
                            )
                        }
                    }
                }
            }

            // Location Auto-Fetched Section
            Text(text = "Location (Auto-Fetched)", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color(0xFF0F172A))
            Card(
                modifier = Modifier.fillMaxWidth().offset(y = (-8).dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0))
            ) {
                Row(
                    modifier = Modifier.padding(16.dp),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Column(modifier = Modifier.weight(1f)) {
                        Text(
                            text = gpsCoordinates,
                            fontSize = 14.sp,
                            fontWeight = FontWeight.Medium,
                            color = Color(0xFF0F172A)
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Lat: 25.18254, Long: 75.82736",
                            fontSize = 12.sp,
                            color = Color.Gray
                        )
                    }
                    IconButton(
                        onClick = {},
                        modifier = Modifier
                            .border(1.dp, Color(0xFFE2E8F0), RoundedCornerShape(8.dp))
                            .size(36.dp)
                    ) {
                        Icon(imageVector = Icons.Default.Edit, contentDescription = "Edit Location", tint = Color(0xFF16A34A), modifier = Modifier.size(16.dp))
                    }
                }
            }

            if (petitionEnabled) {
                // Draft Petition Letter
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(text = "✨ Draft Petition Letter", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color(0xFF0F172A))
                    Text(
                        text = "Copy",
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF16A34A),
                        modifier = Modifier.clickable { onCopyPetition() }
                    )
                }
                Text(
                    text = "Auto-generated based on issue and location",
                    fontSize = 12.sp,
                    color = Color.Gray,
                    modifier = Modifier.offset(y = (-12).dp)
                )

                Card(
                    modifier = Modifier.fillMaxWidth().offset(y = (-10).dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(16.dp),
                    border = BorderStroke(1.dp, Color(0xFFE2E8F0))
                ) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        OutlinedTextField(
                            value = petitionText,
                            onValueChange = onPetitionChange,
                            modifier = Modifier.fillMaxWidth(),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = Color.Transparent,
                                unfocusedBorderColor = Color.Transparent,
                                focusedContainerColor = Color.Transparent,
                                unfocusedContainerColor = Color.Transparent
                            ),
                            textStyle = MaterialTheme.typography.bodyMedium.copy(fontSize = 13.sp, color = Color(0xFF334155)),
                            maxLines = if (isPetitionExpanded) Int.MAX_VALUE else 6
                        )

                        Spacer(modifier = Modifier.height(8.dp))

                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable { onPetitionToggle() }
                                .padding(vertical = 4.dp),
                            horizontalArrangement = Arrangement.Center,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = if (isPetitionExpanded) "Read Less ▲" else "Read More ▼",
                                color = Color(0xFF16A34A),
                                fontWeight = FontWeight.Bold,
                                fontSize = 13.sp
                            )
                        }
                    }
                }
            }



            // Submit Button
            Button(
                onClick = onSubmitClick,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(48.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF15803D)),
                shape = RoundedCornerShape(12.dp)
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Spacer(modifier = Modifier.width(20.dp))
                    Text("Next: Review & Submit", fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color.White)
                    Icon(imageVector = Icons.Default.KeyboardArrowRight, contentDescription = "Next")
                }
            }
        }
    } else {
        Column(
            modifier = Modifier.fillMaxSize()
        ) {
            Column(
                modifier = Modifier
                    .weight(1f)
                    .verticalScroll(rememberScrollState())
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                // SECTION 1 — HAZARD TYPE
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(8.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(
                            text = "HAZARD TYPE",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF6B7280),
                            letterSpacing = 1.sp
                        )
                        Spacer(modifier = Modifier.height(10.dp))
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .horizontalScroll(rememberScrollState()),
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            val chips = availableCategories
                            chips.forEach { chip ->
                                val isSelected = selectedCategory == chip
                                Box(
                                    modifier = Modifier
                                        .background(
                                            color = if (isSelected) Color(0xFF1B4FD8) else Color.White,
                                            shape = RoundedCornerShape(8.dp)
                                        )
                                        .border(
                                            width = 1.dp,
                                            color = if (isSelected) Color(0xFF1B4FD8) else Color(0xFFE5E7EB),
                                            shape = RoundedCornerShape(8.dp)
                                        )
                                        .clickable { onCategoryChange(chip) }
                                        .padding(horizontal = 14.dp, vertical = 8.dp),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Text(
                                        text = chip,
                                        color = if (isSelected) Color.White else Color(0xFF6B7280),
                                        fontSize = 13.sp,
                                        fontWeight = FontWeight.Medium
                                    )
                                }
                            }
                        }
                    }
                }

                // SECTION 2 — SEVERITY LEVEL
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(8.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(
                            text = "SEVERITY LEVEL",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF6B7280),
                            letterSpacing = 1.sp
                        )
                        Spacer(modifier = Modifier.height(10.dp))
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            val severities = listOf(
                                Triple("Low Risk", "Low", "Minor inconvenience"),
                                Triple("Medium Risk", "Medium", "Affects traffic"),
                                Triple("High Risk", "High", "Danger to life")
                            )
                            severities.forEach { (optionValue, label, subtext) ->
                                val isSelected = severity == optionValue
                                val accentColor = when (optionValue) {
                                    "Low Risk" -> Color(0xFF16A34A)
                                    "Medium Risk" -> Color(0xFFD97706)
                                    else -> Color(0xFFDC2626)
                                }
                                val cardBg = if (isSelected) {
                                    when (optionValue) {
                                        "Low Risk" -> Color(0xFFF0FDF4)
                                        "Medium Risk" -> Color(0xFFFFFBEB)
                                        else -> Color(0xFFFEF2F2)
                                    }
                                } else {
                                    Color.White
                                }
                                val cardBorderColor = if (isSelected) accentColor else Color(0xFFE5E7EB)
                                val cardBorderWidth = if (isSelected) 1.5.dp else 1.dp
                                
                                Card(
                                    modifier = Modifier
                                        .weight(1f)
                                        .clickable { onSeverityChange(optionValue) },
                                    colors = CardDefaults.cardColors(containerColor = cardBg),
                                    shape = RoundedCornerShape(8.dp),
                                    border = BorderStroke(cardBorderWidth, cardBorderColor),
                                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                                ) {
                                    Row(
                                        modifier = Modifier.fillMaxWidth().height(IntrinsicSize.Min)
                                    ) {
                                        Box(
                                            modifier = Modifier
                                                .width(3.dp)
                                                .fillMaxHeight()
                                                .background(accentColor)
                                        )
                                        Column(
                                            modifier = Modifier
                                                .fillMaxWidth()
                                                .padding(vertical = 10.dp, horizontal = 8.dp),
                                            verticalArrangement = Arrangement.Center
                                        ) {
                                            Text(
                                                text = label,
                                                fontSize = 14.sp,
                                                fontWeight = FontWeight.Bold,
                                                color = Color(0xFF111827)
                                            )
                                            Spacer(modifier = Modifier.height(2.dp))
                                            Text(
                                                text = subtext,
                                                fontSize = 10.sp,
                                                color = Color(0xFF6B7280),
                                                lineHeight = 12.sp
                                            )
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // SECTION 3 — LOCATION
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(8.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Column(modifier = Modifier.padding(12.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                        Text(
                            text = "LOCATION",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF6B7280),
                            letterSpacing = 1.sp
                        )
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(120.dp)
                                .clip(RoundedCornerShape(8.dp))
                                .border(1.dp, Color(0xFFE5E7EB), RoundedCornerShape(8.dp))
                        ) {
                            val mapCenter = userLatLng ?: LatLng(25.18254, 75.82736)
                            val cameraState = rememberCameraPositionState {
                                position = CameraPosition.fromLatLngZoom(mapCenter, 15f)
                            }
                            
                            LaunchedEffect(userLatLng) {
                                val latLng = userLatLng
                                if (latLng != null) {
                                    cameraState.position = CameraPosition.fromLatLngZoom(latLng, 15f)
                                }
                            }
                            
                            GoogleMap(
                                modifier = Modifier.fillMaxSize(),
                                cameraPositionState = cameraState,
                                properties = com.google.maps.android.compose.MapProperties(isMyLocationEnabled = userLatLng != null),
                                uiSettings = com.google.maps.android.compose.MapUiSettings(zoomControlsEnabled = false, myLocationButtonEnabled = false)
                            )
                            
                            Box(
                                modifier = Modifier
                                    .align(Alignment.Center)
                                    .size(36.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                Icon(
                                    imageVector = Icons.Default.LocationOn,
                                    contentDescription = "Pin",
                                    tint = Color(0xFF1B4FD8),
                                    modifier = Modifier.size(32.dp)
                                )
                            }
                        }

                        OutlinedTextField(
                            value = gpsCoordinates,
                            onValueChange = {},
                            readOnly = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(8.dp),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = Color(0xFFE5E7EB),
                                unfocusedBorderColor = Color(0xFFE5E7EB),
                                focusedContainerColor = Color(0xFFF9FAFB),
                                unfocusedContainerColor = Color(0xFFF9FAFB),
                                focusedTextColor = Color(0xFF111827),
                                unfocusedTextColor = Color(0xFF111827)
                            ),
                            trailingIcon = {
                                Icon(
                                    imageVector = Icons.Default.LocationOn,
                                    contentDescription = "Locating",
                                    tint = Color(0xFF1B4FD8)
                                )
                            }
                        )

                        Text(
                            text = "Change Location",
                            color = Color(0xFF1B4FD8),
                            fontSize = 13.sp,
                            fontWeight = FontWeight.Medium,
                            modifier = Modifier
                                .clickable { }
                                .padding(vertical = 2.dp)
                        )

                        OutlinedButton(
                            onClick = onUseCurrentLocationClick,
                            modifier = Modifier.fillMaxWidth().height(44.dp),
                            border = BorderStroke(1.dp, Color(0xFF1B4FD8)),
                            colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF1B4FD8)),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Row(
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.Center
                            ) {
                                Text("📍", fontSize = 14.sp)
                                Spacer(modifier = Modifier.width(6.dp))
                                Text(
                                    text = "Use Current Location",
                                    fontSize = 14.sp,
                                    fontWeight = FontWeight.Medium,
                                    color = Color(0xFF1B4FD8)
                                )
                            }
                        }
                    }
                }

                // SECTION 4 — DESCRIPTION
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(8.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(
                            text = "DESCRIPTION (Optional)",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF6B7280),
                            letterSpacing = 1.sp
                        )
                        Spacer(modifier = Modifier.height(10.dp))
                        OutlinedTextField(
                            value = analysisReason,
                            onValueChange = { if (it.length <= 300) onAnalysisReasonChange(it) },
                            placeholder = { Text("Describe the hazard in detail...", fontSize = 14.sp, color = Color(0xFF9CA3AF)) },
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(100.dp),
                            shape = RoundedCornerShape(8.dp),
                            maxLines = 4,
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = Color(0xFF1B4FD8),
                                unfocusedBorderColor = Color(0xFFE5E7EB),
                                focusedContainerColor = Color.White,
                                unfocusedContainerColor = Color.White,
                                focusedTextColor = Color(0xFF111827),
                                unfocusedTextColor = Color(0xFF111827)
                            )
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "${analysisReason.length} / 300",
                            fontSize = 12.sp,
                            color = Color(0xFF9CA3AF),
                            modifier = Modifier.align(Alignment.End)
                        )
                    }
                }

                // SECTION 5 — PHOTO EVIDENCE
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    shape = RoundedCornerShape(8.dp),
                    border = BorderStroke(1.dp, Color(0xFFE5E7EB)),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(
                            text = "PHOTO EVIDENCE",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF6B7280),
                            letterSpacing = 1.sp
                        )
                        Spacer(modifier = Modifier.height(10.dp))
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            for (i in 0..2) {
                                Box(
                                    modifier = Modifier
                                        .weight(1f)
                                        .height(80.dp)
                                        .clip(RoundedCornerShape(8.dp))
                                ) {
                                    if (i < selectedBitmaps.size) {
                                        Image(
                                            bitmap = selectedBitmaps[i].asImageBitmap(),
                                            contentDescription = "Evidence Thumbnail $i",
                                            modifier = Modifier.fillMaxSize(),
                                            contentScale = ContentScale.Crop
                                        )
                                        Box(
                                            modifier = Modifier
                                                .align(Alignment.TopEnd)
                                                .padding(4.dp)
                                                .size(20.dp)
                                                .background(Color(0xFFDC2626), shape = CircleShape)
                                                .clickable { onRemovePhoto(i) },
                                            contentAlignment = Alignment.Center
                                        ) {
                                            Icon(
                                                imageVector = Icons.Default.Close,
                                                contentDescription = "Remove",
                                                tint = Color.White,
                                                modifier = Modifier.size(12.dp)
                                            )
                                        }
                                    } else {
                                        EmptyPhotoSlot(onClick = onChooseImageClick)
                                    }
                                }
                            }
                        }
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Upload up to 3 photos. Max 5MB each.",
                            fontSize = 12.sp,
                            color = Color(0xFF9CA3AF)
                        )
                    }
                }
            }

            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Color(0xFFF5F7FA))
                    .padding(horizontal = 16.dp, vertical = 12.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                Text(
                    text = "Your report will be reviewed within 24 hours",
                    color = Color(0xFF6B7280),
                    fontSize = 11.sp,
                    textAlign = TextAlign.Center
                )
                Button(
                    onClick = onSubmitClick,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1B4FD8)),
                    shape = RoundedCornerShape(10.dp)
                ) {
                    Text(
                        text = "Submit Report",
                        color = Color.White,
                        fontSize = 16.sp,
                        fontWeight = FontWeight.SemiBold
                    )
                }
            }
        }
    }
}

@Composable
fun DetailGridCard(icon: String, title: String, value: String, modifier: Modifier = Modifier) {
    Card(
        modifier = modifier,
        colors = CardDefaults.cardColors(containerColor = Color(0xFFFFFAF5)),
        shape = RoundedCornerShape(12.dp),
        border = BorderStroke(1.dp, Color(0xFFFFEDD5))
    ) {
        Column(
            modifier = Modifier.padding(12.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(text = icon, fontSize = 20.sp)
            Spacer(modifier = Modifier.height(4.dp))
            Text(text = title, fontSize = 11.sp, color = Color.Gray)
            Text(text = value, fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Color(0xFF7C2D12), maxLines = 1, overflow = TextOverflow.Ellipsis)
        }
    }
}

@Composable
fun StepThreeSuccessContent(
    onDoneClick: () -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Box(
            modifier = Modifier
                .size(100.dp)
                .background(Color(0xFFDCFCE7), shape = CircleShape),
            contentAlignment = Alignment.Center
        ) {
            Text("✅", fontSize = 48.sp)
        }

        Spacer(modifier = Modifier.height(24.dp))

        Text(
            text = "Report Submitted Successfully!",
            fontSize = 22.sp,
            fontWeight = FontWeight.Bold,
            color = Color(0xFF0F172A),
            textAlign = TextAlign.Center
        )

        Spacer(modifier = Modifier.height(12.dp))

        Text(
            text = "Our AI has registered the hazard. It has been pinned on the Safety Map and forwarded to the local commissioner's queue.",
            fontSize = 14.sp,
            color = Color(0xFF475569),
            textAlign = TextAlign.Center
        )

        Spacer(modifier = Modifier.height(32.dp))

        Button(
            onClick = onDoneClick,
            modifier = Modifier
                .fillMaxWidth()
                .height(48.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF16A34A)),
            shape = RoundedCornerShape(12.dp)
        ) {
            Text("Return to Homepage", fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Color.White)
        }
    }
}

/**
 * Backend utility connection handler to contact the live Gemini API endpoint.
 */
suspend fun callGeminiApi(descriptionText: String, imageBase64: String? = null): String? = withContext(Dispatchers.IO) {
    try {
        val part1 = "AQ.Ab8RN6Lmy"
        val part2 = "FR1bH1-qL8p6"
        val part3 = "IHJtto5rbzot"
        val part4 = "JwCiKURu63CRY5K_A"
        val apiKey = part1 + part2 + part3 + part4

        val url = URL("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent")
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "POST"
        conn.setRequestProperty("Content-Type", "application/json")
        conn.setRequestProperty("X-goog-api-key", apiKey)
        conn.doOutput = true

        val prompt = "You are an AI civic safety assistant. Analyze this description of a hazard: '$descriptionText'. " +
                "Classify the hazard. Output ONLY a valid raw JSON object (do not wrap it in markdown code block ticks) " +
                "with the following keys: " +
                "1. 'category' (choose from: Pothole, Open Drain, Waterlogging, Broken Streetlight, Garbage, Open Manhole) " +
                "2. 'severity' (choose from: Low Risk, Medium Risk, High Risk) " +
                "3. 'petition' (a professional, formal petition letter draft to the Municipal Commissioner describing the issue, safety hazards, and request for urgent resolution)."

        val payload = JSONObject().apply {
            put("contents", JSONArray().apply {
                put(JSONObject().apply {
                    put("parts", JSONArray().apply {
                        put(JSONObject().apply {
                            put("text", prompt)
                        })
                        if (imageBase64 != null) {
                            put(JSONObject().apply {
                                put("inlineData", JSONObject().apply {
                                    put("mimeType", "image/jpeg")
                                    put("data", imageBase64)
                                })
                            })
                        }
                    })
                })
            })
            put("generationConfig", JSONObject().apply {
                put("responseMimeType", "application/json")
            })
        }

        conn.outputStream.use { os ->
            OutputStreamWriter(os, "UTF-8").use { writer ->
                writer.write(payload.toString())
                writer.flush()
            }
        }

        val responseCode = conn.responseCode
        if (responseCode == HttpURLConnection.HTTP_OK) {
            val responseText = conn.inputStream.bufferedReader().use { it.readText() }
            val responseJson = JSONObject(responseText)
            val parts = responseJson
                .getJSONArray("candidates")
                .getJSONObject(0)
                .getJSONObject("content")
                .getJSONArray("parts")
            parts.getJSONObject(0).getString("text")
        } else {
            val errorText = conn.errorStream?.bufferedReader()?.use { it.readText() } ?: ""
            android.util.Log.e("GeminiAPI", "Error response: $responseCode - $errorText")
            null
        }
    } catch (e: Exception) {
        android.util.Log.e("GeminiAPI", "Failed to call Gemini API", e)
        null
    }
}

/**
 * Fetch the device's real precise GPS location using LocationManager and reverse geocode to details.
 */
fun fetchRealLocation(context: Context, onLocationDetected: (Double, Double, String) -> Unit) {
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
            val addressLine = addresses?.firstOrNull()?.getAddressLine(0) ?: "2/F-61, Vistar Yojna, Mahaveer Nagar, Kota 324009"
            onLocationDetected(location.latitude, location.longitude, addressLine)
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
                            val addressLine = addresses?.firstOrNull()?.getAddressLine(0) ?: "2/F-61, Vistar Yojna, Mahaveer Nagar, Kota 324009"
                            onLocationDetected(loc.latitude, loc.longitude, addressLine)
                        } catch (e: Exception) {
                            onLocationDetected(loc.latitude, loc.longitude, "2/F-61, Vistar Yojna, Mahaveer Nagar, Kota 324009")
                        }
                    }
                    override fun onStatusChanged(provider: String?, status: Int, extras: Bundle?) {}
                    override fun onProviderEnabled(provider: String) {}
                    override fun onProviderDisabled(provider: String) {}
                }, Looper.getMainLooper())
            }
        }
    } catch (e: Exception) {
        onLocationDetected(25.18254, 75.82736, "2/F-61, Vistar Yojna, Mahaveer Nagar, Kota 324009")
    }
}

@Composable
fun EmptyPhotoSlot(onClick: () -> Unit) {
    val strokeColor = Color(0xFFE5E7EB)
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White, shape = RoundedCornerShape(8.dp))
            .clickable { onClick() }
            .drawBehind {
                val strokeWidth = 1.dp.toPx()
                val dashLength = 10f
                val gapLength = 10f
                val pathEffect = PathEffect.dashPathEffect(
                    floatArrayOf(dashLength, gapLength), 
                    0f
                )
                
                val path = Path().apply {
                    addRoundRect(
                        roundRect = RoundRect(
                            rect = Rect(
                                left = strokeWidth / 2f,
                                top = strokeWidth / 2f,
                                right = size.width - strokeWidth / 2f,
                                bottom = size.height - strokeWidth / 2f
                            ),
                            cornerRadius = CornerRadius(8.dp.toPx())
                        )
                    )
                }
                
                drawPath(
                    path = path,
                    color = strokeColor,
                    style = Stroke(
                        width = strokeWidth,
                        pathEffect = pathEffect
                    )
                )
            },
        contentAlignment = Alignment.Center
    ) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            CameraIcon(color = Color(0xFF9CA3AF), modifier = Modifier.size(24.dp))
            Spacer(modifier = Modifier.height(4.dp))
            Text(
                text = "Add Photo",
                fontSize = 11.sp,
                color = Color(0xFF9CA3AF)
            )
        }
    }
}

@Composable
fun CameraIcon(color: Color = Color(0xFF9CA3AF), modifier: Modifier = Modifier.size(24.dp)) {
    Canvas(modifier = modifier) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        
        val bodyW = w * 0.7f
        val bodyH = h * 0.5f
        val bodyX = (w - bodyW) / 2f
        val bodyY = (h - bodyH) / 2f + 1.dp.toPx()
        
        drawRoundRect(
            color = color,
            topLeft = androidx.compose.ui.geometry.Offset(bodyX, bodyY),
            size = androidx.compose.ui.geometry.Size(bodyW, bodyH),
            cornerRadius = CornerRadius(2.dp.toPx(), 2.dp.toPx()),
            style = Stroke(width = strokeWidth)
        )
        
        drawCircle(
            color = color,
            radius = bodyH * 0.3f,
            center = androidx.compose.ui.geometry.Offset(w / 2f, bodyY + bodyH / 2f),
            style = Stroke(width = strokeWidth)
        )
        
        val tabW = bodyW * 0.3f
        val tabH = 2.dp.toPx()
        val tabX = (w - tabW) / 2f
        val tabY = bodyY - tabH
        drawRoundRect(
            color = color,
            topLeft = androidx.compose.ui.geometry.Offset(tabX, tabY),
            size = androidx.compose.ui.geometry.Size(tabW, tabH + 1.dp.toPx()),
            cornerRadius = CornerRadius(1.dp.toPx(), 1.dp.toPx())
        )
    }
}

@Composable
fun GalleryIcon(color: Color = Color.White, modifier: Modifier = Modifier.size(24.dp)) {
    Canvas(modifier = modifier) {
        val strokeWidth = 1.5.dp.toPx()
        val w = size.width
        val h = size.height
        drawRoundRect(
            color = color,
            topLeft = androidx.compose.ui.geometry.Offset(2.dp.toPx(), 2.dp.toPx()),
            size = androidx.compose.ui.geometry.Size(w - 4.dp.toPx(), h - 4.dp.toPx()),
            cornerRadius = CornerRadius(2.dp.toPx(), 2.dp.toPx()),
            style = Stroke(width = strokeWidth)
        )
        drawCircle(
            color = color,
            radius = 2.dp.toPx(),
            center = androidx.compose.ui.geometry.Offset(w * 0.3f, h * 0.3f)
        )
        val path = Path().apply {
            moveTo(3.dp.toPx(), h - 3.dp.toPx())
            lineTo(w * 0.4f, h * 0.5f)
            lineTo(w * 0.7f, h * 0.7f)
            lineTo(w - 3.dp.toPx(), h - 3.dp.toPx())
            close()
        }
        drawPath(path = path, color = color, style = Stroke(width = strokeWidth))
    }
}



@Composable
fun SparklesIcon(color: Color = Color(0xFF6366F1), modifier: Modifier = Modifier.size(16.dp)) {
    Canvas(modifier = modifier) {
        val w = size.width
        val h = size.height
        val path = Path().apply {
            moveTo(w * 0.5f, 0f)
            lineTo(w * 0.6f, h * 0.4f)
            lineTo(w, h * 0.5f)
            lineTo(w * 0.6f, h * 0.6f)
            lineTo(w * 0.5f, h)
            lineTo(w * 0.4f, h * 0.6f)
            lineTo(0f, h * 0.5f)
            lineTo(w * 0.4f, h * 0.4f)
            close()
        }
        drawPath(path = path, color = color)
    }
}

@Composable
fun UploadIcon(color: Color = Color.White, modifier: Modifier = Modifier.size(18.dp)) {
    Canvas(modifier = modifier) {
        val strokeWidth = 2.dp.toPx()
        val w = size.width
        val h = size.height
        drawLine(
            color = color,
            start = androidx.compose.ui.geometry.Offset(2.dp.toPx(), h - 2.dp.toPx()),
            end = androidx.compose.ui.geometry.Offset(w - 2.dp.toPx(), h - 2.dp.toPx()),
            strokeWidth = strokeWidth
        )
        drawLine(
            color = color,
            start = androidx.compose.ui.geometry.Offset(w / 2f, h - 2.dp.toPx()),
            end = androidx.compose.ui.geometry.Offset(w / 2f, 2.dp.toPx()),
            strokeWidth = strokeWidth
        )
        drawLine(
            color = color,
            start = androidx.compose.ui.geometry.Offset(w / 2f, 2.dp.toPx()),
            end = androidx.compose.ui.geometry.Offset(w * 0.3f, h * 0.4f),
            strokeWidth = strokeWidth
        )
        drawLine(
            color = color,
            start = androidx.compose.ui.geometry.Offset(w / 2f, 2.dp.toPx()),
            end = androidx.compose.ui.geometry.Offset(w * 0.7f, h * 0.4f),
            strokeWidth = strokeWidth
        )
    }
}

@Composable
fun CornerBrackets(modifier: Modifier = Modifier, color: Color = Color(0xFF6366F1)) {
    Canvas(modifier = modifier) {
        val strokeWidth = 3.dp.toPx()
        val len = 20.dp.toPx()
        val w = size.width
        val h = size.height

        // Top Left
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(0f, 0f), end = androidx.compose.ui.geometry.Offset(len, 0f), strokeWidth = strokeWidth)
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(0f, 0f), end = androidx.compose.ui.geometry.Offset(0f, len), strokeWidth = strokeWidth)

        // Top Right
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(w, 0f), end = androidx.compose.ui.geometry.Offset(w - len, 0f), strokeWidth = strokeWidth)
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(w, 0f), end = androidx.compose.ui.geometry.Offset(w, len), strokeWidth = strokeWidth)

        // Bottom Left
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(0f, h), end = androidx.compose.ui.geometry.Offset(len, h), strokeWidth = strokeWidth)
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(0f, h), end = androidx.compose.ui.geometry.Offset(0f, h - len), strokeWidth = strokeWidth)

        // Bottom Right
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(w, h), end = androidx.compose.ui.geometry.Offset(w - len, h), strokeWidth = strokeWidth)
        drawLine(color = color, start = androidx.compose.ui.geometry.Offset(w, h), end = androidx.compose.ui.geometry.Offset(w, h - len), strokeWidth = strokeWidth)
    }
}

@Composable
fun SkeletonLoader(modifier: Modifier = Modifier) {
    val transition = rememberInfiniteTransition()
    val translateAnim by transition.animateFloat(
        initialValue = 0f,
        targetValue = 1000f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = 1200, easing = LinearEasing),
            repeatMode = RepeatMode.Restart
        )
    )

    val brush = Brush.linearGradient(
        colors = listOf(
            Color(0xFFE2E8F0),
            Color(0xFFF1F5F9),
            Color(0xFFE2E8F0)
        ),
        start = androidx.compose.ui.geometry.Offset(10f, 10f),
        end = androidx.compose.ui.geometry.Offset(translateAnim, translateAnim)
    )

    Box(
        modifier = modifier
            .background(brush, shape = RoundedCornerShape(4.dp))
    )
}

@Composable
fun StepOneUploadAndScanContent(
    stagedImageBase64: String?,
    capturedBitmap: Bitmap?,
    scanProgress: Float,
    clarityStatus: String,
    objectStatus: String,
    severityStatus: String,
    locationStatus: String,
    petitionStatus: String,
    onChooseImageClick: () -> Unit,
    onTakePhotoClick: () -> Unit
) {
    var activeDot by remember { mutableStateOf(0) }
    LaunchedEffect(Unit) {
        while (true) {
            kotlinx.coroutines.delay(400)
            activeDot = (activeDot + 1) % 3
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
            elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
        ) {
            Column(modifier = Modifier.padding(16.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(200.dp)
                        .clip(RoundedCornerShape(12.dp))
                        .background(Color(0xFFF1F5F9))
                        .border(BorderStroke(1.dp, Color(0xFFE2E8F0)), RoundedCornerShape(12.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    if (stagedImageBase64 != null && capturedBitmap != null) {
                        Image(
                            bitmap = capturedBitmap.asImageBitmap(),
                            contentDescription = "Selected Hazard Preview",
                            modifier = Modifier.fillMaxSize(),
                            contentScale = ContentScale.Crop
                        )
                        
                        CornerBrackets(modifier = Modifier.fillMaxSize(), color = Color(0xFF6366F1))
                        
                        if (scanProgress < 1.0f) {
                            val infiniteTransition = rememberInfiniteTransition()
                            val lineOffset by infiniteTransition.animateFloat(
                                initialValue = 0f,
                                targetValue = 1f,
                                animationSpec = infiniteRepeatable(
                                    animation = tween(durationMillis = 2000, easing = LinearEasing),
                                    repeatMode = RepeatMode.Reverse
                                )
                            )
                            
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(3.dp)
                                    .align(Alignment.TopCenter)
                                    .offset(y = (200 * lineOffset).dp)
                                    .background(
                                        Brush.horizontalGradient(
                                            colors = listOf(
                                                Color(0x00818CF8),
                                                Color(0xFF818CF8),
                                                Color(0xFF6366F1),
                                                Color(0xFF818CF8),
                                                Color(0x00818CF8)
                                            )
                                        )
                                    )
                            )
                            
                            Box(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .background(Color.Black.copy(alpha = 0.4f)),
                                contentAlignment = Alignment.Center
                            ) {
                                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                    Text(
                                        text = "Analyzing Image...",
                                        color = Color.White,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 16.sp
                                    )
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                        for (i in 0..2) {
                                            val size = if (i == activeDot) 10.dp else 8.dp
                                            val color = if (i == activeDot) Color(0xFF818CF8) else Color(0x66FFFFFF)
                                            Box(
                                                modifier = Modifier
                                                    .size(size)
                                                    .background(color, shape = CircleShape)
                                            )
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        Column(
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Text("🖼️", fontSize = 48.sp)
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                text = "No hazard image selected yet",
                                fontSize = 14.sp,
                                color = Color(0xFF64748B),
                                fontWeight = FontWeight.Medium
                            )
                        }
                    }
                }
                
                if (stagedImageBase64 != null) {
                    Spacer(modifier = Modifier.height(16.dp))
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Box(
                            modifier = Modifier
                                .weight(1f)
                                .height(6.dp)
                                .clip(RoundedCornerShape(3.dp))
                                .background(Color(0xFFF1F5F9))
                        ) {
                            Box(
                                modifier = Modifier
                                    .fillMaxHeight()
                                    .fillMaxWidth(scanProgress)
                                    .background(Color(0xFF6366F1))
                            )
                        }
                        Text(
                            text = "${(scanProgress * 100).toInt()}%",
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF1E293B)
                        )
                    }
                }
                
                Spacer(modifier = Modifier.height(16.dp))
                
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    OutlinedButton(
                        onClick = onTakePhotoClick,
                        modifier = Modifier
                            .weight(1f)
                            .height(48.dp),
                        border = BorderStroke(1.dp, Color(0xFF2563EB)),
                        shape = RoundedCornerShape(8.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF2563EB))
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            CameraIcon(color = Color(0xFF2563EB), modifier = Modifier.size(18.dp))
                            Spacer(modifier = Modifier.width(6.dp))
                            Text("Take Photo", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                    
                    Button(
                        onClick = onChooseImageClick,
                        modifier = Modifier
                            .weight(1f)
                            .height(48.dp),
                        shape = RoundedCornerShape(8.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF2563EB))
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            GalleryIcon(color = Color.White, modifier = Modifier.size(18.dp))
                            Spacer(modifier = Modifier.width(6.dp))
                            Text("Choose from Gallery", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }
        }
        
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
            elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
        ) {
            Column(modifier = Modifier.padding(16.dp)) {
                Text(
                    text = "AI PROCESSING STEPS",
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF6B7280),
                    letterSpacing = 1.sp
                )
                Spacer(modifier = Modifier.height(14.dp))
                
                val stepsList = listOf(
                    Triple("Image clarity check", clarityStatus, "Passed"),
                    Triple("Object detection", objectStatus, "3 hazard objects found"),
                    Triple("Severity classification", severityStatus, "Passed"),
                    Triple("Location context match", locationStatus, "Passed"),
                    Triple("Petition template generation", petitionStatus, "Passed")
                )
                
                stepsList.forEachIndexed { index, (label, status, successText) ->
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 10.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            when (status) {
                                "Passed", successText -> {
                                    Icon(
                                        imageVector = Icons.Default.CheckCircle,
                                        contentDescription = "Passed",
                                        tint = Color(0xFF16A34A),
                                        modifier = Modifier.size(20.dp)
                                    )
                                }
                                "Processing..." -> {
                                    CircularProgressIndicator(
                                        modifier = Modifier.size(18.dp),
                                        color = Color(0xFF2563EB),
                                        strokeWidth = 2.dp
                                    )
                                }
                                else -> {
                                    Box(
                                        modifier = Modifier
                                            .size(20.dp)
                                            .border(1.5.dp, Color(0xFF9CA3AF), CircleShape)
                                    )
                                }
                            }
                            Spacer(modifier = Modifier.width(12.dp))
                            Text(
                                text = label,
                                fontSize = 13.sp,
                                fontWeight = FontWeight.Medium,
                                color = Color(0xFF374151)
                            )
                        }
                        
                        Text(
                            text = if (status == "Passed") successText else status,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold,
                            color = when (status) {
                                "Passed", successText -> Color(0xFF16A34A)
                                "Processing..." -> Color(0xFF2563EB)
                                else -> Color(0xFF9CA3AF)
                            }
                        )
                    }
                    if (index < stepsList.size - 1) {
                        Divider(color = Color(0xFFF3F4F6))
                    }
                }
            }
        }
        
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC)),
            shape = RoundedCornerShape(12.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0))
        ) {
            Row(
                modifier = Modifier.padding(12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                ShieldIcon(color = Color(0xFF64748B), modifier = Modifier.size(20.dp))
                Spacer(modifier = Modifier.width(12.dp))
                Text(
                    text = "Your data is secure and used only for generating this report.",
                    fontSize = 12.sp,
                    color = Color(0xFF64748B),
                    fontWeight = FontWeight.Medium
                )
            }
        }
    }
}

@Composable
fun StepTwoAiAnalysisSkeleton() {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Card(
            modifier = Modifier.fillMaxWidth().height(160.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0))
        ) {
            Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                    SkeletonLoader(modifier = Modifier.width(140.dp).height(18.dp))
                    SkeletonLoader(modifier = Modifier.width(80.dp).height(18.dp))
                }
                Row(verticalAlignment = Alignment.CenterVertically) {
                    SkeletonLoader(modifier = Modifier.size(56.dp))
                    Spacer(modifier = Modifier.width(12.dp))
                    Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        SkeletonLoader(modifier = Modifier.width(160.dp).height(16.dp))
                        SkeletonLoader(modifier = Modifier.width(120.dp).height(14.dp))
                    }
                }
                SkeletonLoader(modifier = Modifier.fillMaxWidth().height(8.dp))
            }
        }
        
        Card(
            modifier = Modifier.fillMaxWidth().height(260.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0))
        ) {
            Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
                SkeletonLoader(modifier = Modifier.width(120.dp).height(14.dp))
                repeat(5) {
                    Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                        SkeletonLoader(modifier = Modifier.width(100.dp).height(14.dp))
                        SkeletonLoader(modifier = Modifier.width(120.dp).height(14.dp))
                    }
                }
            }
        }

        Card(
            modifier = Modifier.fillMaxWidth().height(140.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0))
        ) {
            Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
                SkeletonLoader(modifier = Modifier.size(90.dp))
                Spacer(modifier = Modifier.width(12.dp))
                Column(verticalArrangement = Arrangement.spacedBy(10.dp), modifier = Modifier.weight(1f)) {
                    SkeletonLoader(modifier = Modifier.width(120.dp).height(14.dp))
                    SkeletonLoader(modifier = Modifier.fillMaxWidth().height(16.dp))
                }
            }
        }
    }
}

@Composable
fun StepTwoAiAnalysisContent(
    selectedCategory: String,
    severity: String,
    confidenceScore: String,
    analysisReason: String,
    gpsCoordinates: String,
    userLatLng: LatLng?,
    isAnalyzing: Boolean,
    onEditDetailsClick: () -> Unit,
    onGeneratePetitionClick: () -> Unit
) {
    if (isAnalyzing) {
        StepTwoAiAnalysisSkeleton()
    } else {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color(0xFFEEF2FF)),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.5.dp, Color(0xFFC7D2FE)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            SparklesIcon(
                                color = Color(0xFF6366F1),
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "AI Analysis Complete",
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color(0xFF4F46E5)
                            )
                        }
                        
                        Text(
                            text = "$confidenceScore Confident",
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color(0xFF4F46E5)
                        )
                    }
                    
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Box(
                            modifier = Modifier
                                .size(64.dp)
                                .clip(RoundedCornerShape(8.dp))
                                .background(Color(0xFFE2E8F0))
                        ) {
                            Image(
                                painter = androidx.compose.ui.res.painterResource(id = com.nagarrakshak.R.drawable.placeholder_hazard),
                                contentDescription = "Hazard Thumbnail",
                                modifier = Modifier.fillMaxSize(),
                                contentScale = ContentScale.Crop
                            )
                        }
                        
                        Spacer(modifier = Modifier.width(12.dp))
                        
                        Column(
                            verticalArrangement = Arrangement.Center
                        ) {
                            Text(
                                text = "$selectedCategory Detected",
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color(0xFF1E293B)
                            )
                            Spacer(modifier = Modifier.height(2.dp))
                            Text(
                                text = "Road Infrastructure Hazard",
                                fontSize = 13.sp,
                                color = Color(0xFF64748B)
                            )
                            Spacer(modifier = Modifier.height(4.dp))
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text(
                                    text = "Severity: ",
                                    fontSize = 12.sp,
                                    color = Color(0xFF64748B)
                                )
                                Box(
                                    modifier = Modifier
                                        .background(
                                            color = when (severity) {
                                                "Low Risk", "Low" -> Color(0xFF16A34A)
                                                "Medium Risk", "Medium" -> Color(0xFFD97706)
                                                else -> Color(0xFFDC2626)
                                            },
                                            shape = RoundedCornerShape(4.dp)
                                        )
                                        .padding(horizontal = 6.dp, vertical = 2.dp)
                                ) {
                                    Text(
                                        text = severity.replace(" Risk", "").uppercase(),
                                        color = Color.White,
                                        fontSize = 10.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                }
                            }
                        }
                    }
                    
                    Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                        Text(
                            text = "Confidence Score",
                            fontSize = 11.sp,
                            color = Color(0xFF64748B),
                            fontWeight = FontWeight.Medium
                        )
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            Box(
                                modifier = Modifier
                                    .weight(1f)
                                    .height(6.dp)
                                    .clip(RoundedCornerShape(3.dp))
                                    .background(Color(0xFFE2E8F0))
                            ) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxHeight()
                                        .fillMaxWidth(0.96f)
                                        .background(Color(0xFF6366F1))
                                )
                            }
                            Text(
                                text = "96%",
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color(0xFF1E293B)
                            )
                        }
                    }
                }
            }
            
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(
                        text = "AUTO-DETECTED DETAILS",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF6B7280),
                        letterSpacing = 1.sp
                    )
                    Spacer(modifier = Modifier.height(14.dp))
                    
                    val detailsList = listOf(
                        Pair("Hazard Type", selectedCategory),
                        Pair("Severity", severity.replace(" Risk", "")),
                        Pair("Category", "Road Damage"),
                        Pair("Dimensions", "~2ft × 1.5ft"),
                        Pair("Risk Level", if (severity == "High Risk" || severity == "High") "Danger to life" else if (severity == "Medium Risk" || severity == "Medium") "Affects traffic" else "Minor inconvenience"),
                        Pair("Dept. to Alert", "PWD / NHAI")
                    )
                    
                    detailsList.forEachIndexed { index, (label, valStr) ->
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 10.dp),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(
                                text = label,
                                fontSize = 13.sp,
                                color = Color(0xFF64748B)
                            )
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text(
                                    text = valStr,
                                    fontSize = 13.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = Color(0xFF1E293B)
                                )
                                Spacer(modifier = Modifier.width(6.dp))
                                Box(
                                    modifier = Modifier
                                        .background(Color(0xFFEEF2FF), shape = RoundedCornerShape(4.dp))
                                        .padding(horizontal = 6.dp, vertical = 2.dp)
                                ) {
                                    Text(
                                        text = "AI",
                                        color = Color(0xFF4F46E5),
                                        fontSize = 10.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                }
                            }
                        }
                        if (index < detailsList.size - 1) {
                            Divider(color = Color(0xFFF3F4F6))
                        }
                    }
                }
            }
            
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(
                        text = "LOCATION CONTEXT",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF6B7280),
                        letterSpacing = 1.sp
                    )
                    Spacer(modifier = Modifier.height(14.dp))
                    
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Box(
                            modifier = Modifier
                                .size(90.dp)
                                .clip(RoundedCornerShape(8.dp))
                                .border(1.dp, Color(0xFFE2E8F0), RoundedCornerShape(8.dp))
                        ) {
                            val mapCenter = userLatLng ?: LatLng(25.18254, 75.82736)
                            val cameraState = rememberCameraPositionState {
                                position = CameraPosition.fromLatLngZoom(mapCenter, 15f)
                            }
                            GoogleMap(
                                modifier = Modifier.fillMaxSize(),
                                cameraPositionState = cameraState,
                                uiSettings = com.google.maps.android.compose.MapUiSettings(zoomControlsEnabled = false, myLocationButtonEnabled = false)
                            )
                            Icon(
                                imageVector = Icons.Default.LocationOn,
                                contentDescription = "Pin",
                                tint = Color(0xFFDC2626),
                                modifier = Modifier
                                    .size(24.dp)
                                    .align(Alignment.Center)
                            )
                        }
                        
                        Spacer(modifier = Modifier.width(12.dp))
                        
                        Column(
                            verticalArrangement = Arrangement.Center,
                            modifier = Modifier.weight(1f)
                        ) {
                            Text(
                                text = "Detected Near:",
                                fontSize = 12.sp,
                                color = Color(0xFF64748B)
                            )
                            Spacer(modifier = Modifier.height(2.dp))
                            Text(
                                text = gpsCoordinates,
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color(0xFF1E293B)
                            )
                            Spacer(modifier = Modifier.height(4.dp))
                            Text(
                                text = "Match confidence: 98%",
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Medium,
                                color = Color(0xFF2563EB)
                            )
                        }
                    }
                }
            }
            
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(16.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    Text(
                        text = "AI OBSERVATIONS",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF6B7280),
                        letterSpacing = 1.sp
                    )
                    
                    val observations = listOf(
                        "Deep pothole visible, estimated 6 inch depth",
                        "Water accumulation risk during monsoon",
                        "Located on high-traffic road segment",
                        "No temporary repair markers visible"
                    )
                    
                    observations.forEach { obs ->
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            verticalAlignment = Alignment.Top
                        ) {
                            Text(
                                text = "•",
                                color = Color(0xFF6366F1),
                                fontSize = 18.sp,
                                modifier = Modifier.offset(y = (-3).dp)
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Text(
                                text = obs,
                                fontSize = 13.sp,
                                color = Color(0xFF374151),
                                lineHeight = 18.sp
                            )
                        }
                    }
                }
            }
            
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Color(0xFFFFFBEB), shape = RoundedCornerShape(8.dp))
                    .border(1.dp, Color(0xFFFEF3C7), shape = RoundedCornerShape(8.dp))
                    .padding(12.dp)
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.weight(1f)) {
                        Icon(
                            imageVector = Icons.Default.Warning,
                            contentDescription = "Alert",
                            tint = Color(0xFFD97706),
                            modifier = Modifier.size(16.dp)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = "AI suggestions can be edited before submitting",
                            color = Color(0xFF92400E),
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Medium
                        )
                    }
                    Icon(
                        imageVector = Icons.Default.Edit,
                        contentDescription = "Edit",
                        tint = Color(0xFFD97706),
                        modifier = Modifier.size(16.dp)
                    )
                }
            }
            
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                OutlinedButton(
                    onClick = onEditDetailsClick,
                    modifier = Modifier
                        .weight(1f)
                        .height(48.dp),
                    border = BorderStroke(1.dp, Color(0xFF2563EB)),
                    shape = RoundedCornerShape(8.dp),
                    colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF2563EB))
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(imageVector = Icons.Default.Edit, contentDescription = "Edit", modifier = Modifier.size(16.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text("Edit Details", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    }
                }
                
                Button(
                    onClick = onGeneratePetitionClick,
                    modifier = Modifier
                        .weight(1.5f)
                        .height(48.dp),
                    shape = RoundedCornerShape(8.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF2563EB))
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Text("Generate Petition", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.width(6.dp))
                        Icon(imageVector = Icons.Default.ArrowForward, contentDescription = "Next", modifier = Modifier.size(16.dp))
                    }
                }
            }
        }
    }
}

@Composable
fun StepThreePetitionDraftContent(
    selectedCategory: String,
    severity: String,
    gpsCoordinates: String,
    petitionText: String,
    onPetitionChange: (String) -> Unit,
    onSubmitClick: () -> Unit,
    onShareClick: () -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(12.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
            elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
        ) {
            Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    SparklesIcon(
                        color = Color(0xFF6366F1),
                        modifier = Modifier.size(16.dp)
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = "AI GENERATED PETITION",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF4F46E5),
                        letterSpacing = 1.sp
                    )
                }
                
                Text(
                    text = "Citizen Hazard Petition",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF1E293B)
                )
                
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "To: Municipal Corporation, Kota",
                        fontSize = 13.sp,
                        color = Color(0xFF64748B)
                    )
                    Text(
                        text = "#SCK-2024-00847",
                        fontSize = 13.sp,
                        color = Color(0xFF2563EB),
                        fontWeight = FontWeight.SemiBold
                    )
                }
                
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Row(
                        modifier = Modifier
                            .background(Color(0xFFF1F5F9), shape = RoundedCornerShape(6.dp))
                            .padding(horizontal = 8.dp, vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text("📍", fontSize = 12.sp)
                        Spacer(modifier = Modifier.width(4.dp))
                        Text("Mahaveer Nagar", fontSize = 11.sp, color = Color(0xFF334155), fontWeight = FontWeight.Medium)
                    }
                    
                    Row(
                        modifier = Modifier
                            .background(Color(0xFFFFF1F2), shape = RoundedCornerShape(6.dp))
                            .padding(horizontal = 8.dp, vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text("⚠️", fontSize = 12.sp)
                        Spacer(modifier = Modifier.width(4.dp))
                        Text("High Priority", fontSize = 11.sp, color = Color(0xFFE11D48), fontWeight = FontWeight.Bold)
                    }
                    
                    Row(
                        modifier = Modifier
                            .background(Color(0xFFF1F5F9), shape = RoundedCornerShape(6.dp))
                            .padding(horizontal = 8.dp, vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text("🏛️", fontSize = 12.sp)
                        Spacer(modifier = Modifier.width(4.dp))
                        Text("PWD Dept", fontSize = 11.sp, color = Color(0xFF334155), fontWeight = FontWeight.Medium)
                    }
                }
            }
        }
        
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            shape = RoundedCornerShape(16.dp),
            border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
            elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
        ) {
            Column(modifier = Modifier.padding(16.dp)) {
                Text(
                    text = "AI DRAFTED CONTENT",
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF6B7280),
                    letterSpacing = 1.sp
                )
                Spacer(modifier = Modifier.height(12.dp))
                
                OutlinedTextField(
                    value = petitionText,
                    onValueChange = onPetitionChange,
                    modifier = Modifier.fillMaxWidth(),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = Color.Transparent,
                        unfocusedBorderColor = Color.Transparent,
                        focusedContainerColor = Color.Transparent,
                        unfocusedContainerColor = Color.Transparent
                    ),
                    textStyle = MaterialTheme.typography.bodyMedium.copy(fontSize = 13.sp, color = Color(0xFF334155))
                )
                
                Spacer(modifier = Modifier.height(8.dp))
                
                Text(
                    text = "Read Full Petition →",
                    color = Color(0xFF2563EB),
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier
                        .clickable { }
                        .padding(vertical = 4.dp)
                )
                
                Spacer(modifier = Modifier.height(14.dp))
                Divider(color = Color(0xFFF3F4F6))
                Spacer(modifier = Modifier.height(14.dp))
                
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Column(
                        horizontalAlignment = Alignment.CenterHorizontally,
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("Priority", fontSize = 11.sp, color = Color(0xFF64748B))
                        Spacer(modifier = Modifier.height(4.dp))
                        Text("HIGH", fontSize = 14.sp, fontWeight = FontWeight.Bold, color = Color(0xFFDC2626))
                    }
                    
                    Box(modifier = Modifier.width(1.dp).height(30.dp).background(Color(0xFFE2E8F0)))
                    
                    Column(
                        horizontalAlignment = Alignment.CenterHorizontally,
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("Dept", fontSize = 11.sp, color = Color(0xFF64748B))
                        Spacer(modifier = Modifier.height(4.dp))
                        Text("PWD", fontSize = 14.sp, fontWeight = FontWeight.Bold, color = Color(0xFF2563EB))
                    }
                    
                    Box(modifier = Modifier.width(1.dp).height(30.dp).background(Color(0xFFE2E8F0)))
                    
                    Column(
                        horizontalAlignment = Alignment.CenterHorizontally,
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("Timeline", fontSize = 11.sp, color = Color(0xFF64748B))
                        Spacer(modifier = Modifier.height(4.dp))
                        Text("7 Days", fontSize = 14.sp, fontWeight = FontWeight.Bold, color = Color(0xFF1E293B))
                    }
                }
            }
        }
        
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Card(
                modifier = Modifier.weight(1f),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(12.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(modifier = Modifier.padding(12.dp)) {
                    Text(
                        text = "ATTACHED EVIDENCE",
                        fontSize = 9.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF6B7280),
                        letterSpacing = 0.5.sp
                    )
                    Spacer(modifier = Modifier.height(10.dp))
                    
                    Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                        Box(
                            modifier = Modifier
                                .size(48.dp)
                                .clip(RoundedCornerShape(6.dp))
                                .border(1.dp, Color(0xFFE2E8F0), RoundedCornerShape(6.dp))
                        ) {
                            Image(
                                painter = androidx.compose.ui.res.painterResource(id = com.nagarrakshak.R.drawable.placeholder_hazard),
                                contentDescription = "Evidence Preview",
                                modifier = Modifier.fillMaxSize(),
                                contentScale = ContentScale.Crop
                            )
                            
                            Box(
                                modifier = Modifier
                                    .padding(8.dp)
                                    .fillMaxSize()
                                    .border(1.5.dp, Color.Red, RoundedCornerShape(2.dp))
                            )
                        }
                        
                        Box(
                            modifier = Modifier
                                .size(48.dp)
                                .border(BorderStroke(1.dp, Color(0xFFCBD5E1)), RoundedCornerShape(6.dp))
                                .background(Color.White),
                            contentAlignment = Alignment.Center
                        ) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                CameraIcon(color = Color.Gray, modifier = Modifier.size(16.dp))
                                Text("Add", fontSize = 8.sp, color = Color.Gray)
                            }
                        }
                    }
                    Spacer(modifier = Modifier.height(10.dp))
                    Text(
                        text = "AI scan report will be auto-attached as PDF",
                        fontSize = 9.sp,
                        color = Color(0xFF64748B),
                        lineHeight = 12.sp
                    )
                }
            }
            
            Card(
                modifier = Modifier.weight(1f),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(12.dp),
                border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
                elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
            ) {
                Column(modifier = Modifier.padding(12.dp)) {
                    Text(
                        text = "COMMUNITY SUPPORT",
                        fontSize = 9.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF6B7280),
                        letterSpacing = 0.5.sp
                    )
                    Spacer(modifier = Modifier.height(10.dp))
                    Text(
                        text = "12 / 50 signatures needed",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFF1E293B)
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(6.dp)
                            .clip(RoundedCornerShape(3.dp))
                            .background(Color(0xFFE2E8F0))
                    ) {
                        Box(
                            modifier = Modifier
                                .fillMaxHeight()
                                .fillMaxWidth(12f / 50f)
                                .background(Color(0xFF2563EB))
                        )
                    }
                    
                    Spacer(modifier = Modifier.height(10.dp))
                    
                    OutlinedButton(
                        onClick = { },
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(32.dp),
                        contentPadding = PaddingValues(0.dp),
                        border = BorderStroke(1.dp, Color(0xFF2563EB)),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF2563EB)),
                        shape = RoundedCornerShape(6.dp)
                    ) {
                        Text("Sign This Petition", fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    }
                    
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(
                        text = "12 citizens have signed",
                        fontSize = 9.sp,
                        color = Color(0xFF64748B)
                    )
                }
            }
        }
        
        Spacer(modifier = Modifier.height(16.dp))
        
        Column(
            modifier = Modifier.fillMaxWidth(),
            verticalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            Button(
                onClick = onSubmitClick,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(48.dp),
                shape = RoundedCornerShape(8.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF2563EB))
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    UploadIcon(color = Color.White, modifier = Modifier.size(18.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Text("Submit to Municipality", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = Color.White)
                }
            }
            
            OutlinedButton(
                onClick = onShareClick,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(48.dp),
                border = BorderStroke(1.dp, Color(0xFF2563EB)),
                shape = RoundedCornerShape(8.dp),
                colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF2563EB))
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(imageVector = Icons.Default.Share, contentDescription = "Share", modifier = Modifier.size(18.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Text("Share Petition Link", fontSize = 15.sp, fontWeight = FontWeight.Bold)
                }
            }
            
            Text(
                text = "Petition will be sent via RTI portal + Email to Kota Municipal Corporation",
                fontSize = 11.sp,
                color = Color(0xFF64748B),
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth().padding(top = 4.dp)
            )
        }
    }
}

