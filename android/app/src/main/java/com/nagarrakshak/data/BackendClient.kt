package com.nagarrakshak.data

import android.content.Context
import android.content.SharedPreferences
import android.util.Log
import com.google.android.gms.maps.model.LatLng
import com.nagarrakshak.data.models.HazardReport
import com.nagarrakshak.data.models.NotificationItem
import com.nagarrakshak.data.models.Severity
import com.nagarrakshak.data.models.VerificationStatus
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import org.json.JSONArray
import org.json.JSONObject
import java.io.BufferedReader
import java.io.InputStreamReader
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL

object BackendClient {
    private const val TAG = "BackendClient"
    private const val BASE_HOST = "https://nagarakshak.showcodebase.space"
    private const val BASE_URL = "$BASE_HOST/api"
    private const val PREFS_NAME = "nagarrakshak_offline_reports"
    private const val KEY_REPORTS = "offline_reports"

    private lateinit var appContext: Context
    private var token: String? = null

    fun init(context: Context) {
        appContext = context.applicationContext
    }

    private fun getPrefs(): SharedPreferences? {
        return if (::appContext.isInitialized) {
            appContext.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
        } else {
            null
        }
    }

    private fun saveOfflineHazard(
        category: String,
        locationName: String,
        latitude: Double,
        longitude: Double,
        severity: String,
        description: String,
        aiAnalysisSummary: String?,
        imagePath: String?
    ) {
        val prefs = getPrefs() ?: return
        val currentReportsJson = prefs.getString(KEY_REPORTS, "[]") ?: "[]"
        try {
            val jsonArray = JSONArray(currentReportsJson)
            val newReport = JSONObject().apply {
                put("id", "offline_${System.currentTimeMillis()}")
                put("category", category)
                put("location_name", locationName)
                put("latitude", latitude)
                put("longitude", longitude)
                put("severity", severity)
                put("description", description)
                put("status", "Pending (Offline)")
                put("verification_count", 0)
                put("created_at", "Just now")
                if (aiAnalysisSummary != null) {
                    put("ai_analysis_summary", aiAnalysisSummary)
                }
                if (imagePath != null) {
                    put("image_path", imagePath)
                }
            }
            jsonArray.put(newReport)
            prefs.edit().putString(KEY_REPORTS, jsonArray.toString()).apply()
            Log.d(TAG, "Saved offline report locally: $newReport")
        } catch (e: Exception) {
            Log.e(TAG, "Failed to save offline report locally", e)
        }
    }

    fun getOfflineHazards(): List<HazardReport> {
        val prefs = getPrefs() ?: return emptyList()
        val currentReportsJson = prefs.getString(KEY_REPORTS, "[]") ?: "[]"
        val list = mutableListOf<HazardReport>()
        try {
            val jsonArray = JSONArray(currentReportsJson)
            for (i in 0 until jsonArray.length()) {
                val obj = jsonArray.getJSONObject(i)
                list.add(parseHazard(obj))
            }
        } catch (e: Exception) {
            Log.e(TAG, "Failed to load offline reports", e)
        }
        return list
    }

    fun setAuthToken(authToken: String?) {
        token = authToken
        Log.d(TAG, "Authorization token updated: ${if (authToken != null) "PRESENT" else "NULL"}")
    }

    suspend fun fetchNearbyHazards(): List<HazardReport> = withContext(Dispatchers.IO) {
        val localList = getOfflineHazards()
        val serverList = mutableListOf<HazardReport>()
        try {
            val url = URL("$BASE_URL/hazards")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 3000
            conn.readTimeout = 3000
            conn.setRequestProperty("Accept", "application/json")
            token?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                val reader = BufferedReader(InputStreamReader(conn.inputStream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()

                val jsonResponse = JSONObject(response.toString())
                if (jsonResponse.optBoolean("success", false)) {
                    val dataArray = jsonResponse.getJSONArray("data")
                    for (i in 0 until dataArray.length()) {
                        val obj = dataArray.getJSONObject(i)
                        serverList.add(parseHazard(obj))
                    }
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error fetching hazards from backend: ${e.message}", e)
        }
        return@withContext localList + serverList
    }

    /**
     * Fetch detail of a single hazard.
     */
    suspend fun fetchHazardDetail(id: String): HazardReport? = withContext(Dispatchers.IO) {
        val list = fetchNearbyHazards()
        return@withContext list.find { it.id == id }
    }

    /**
     * Fetch feature flags from backend settings.
     * Returns a JSONObject with keys: gemini_analysis_enabled, petition_enabled, etc.
     */
    suspend fun fetchSettings(): JSONObject = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/settings")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 10000
            conn.readTimeout = 10000
            conn.setRequestProperty("Accept", "application/json")

            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                val responseText = conn.inputStream.bufferedReader().use { it.readText() }
                val json = JSONObject(responseText)
                if (json.optBoolean("success", false)) {
                    return@withContext json.getJSONObject("data")
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "fetchSettings failed: ${e.message}", e)
        }
        // Return defaults if fetch fails
        return@withContext JSONObject().apply {
            put("gemini_analysis_enabled", true)
            put("petition_enabled", true)
        }
    }

    /**
     * Send hazard image and details to backend for AI analysis.
     */
    suspend fun analyzeHazardImage(
        imageBase64: String,
        latitude: Double?,
        longitude: Double?,
        description: String?,
        city: String?,
        userName: String?
    ): JSONObject = withContext(Dispatchers.IO) {
        val url = URL("$BASE_URL/ai/analyze")
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "POST"
        conn.connectTimeout = 30000
        conn.readTimeout = 30000
        conn.doOutput = true
        
        val boundary = "Boundary-" + System.currentTimeMillis()
        conn.setRequestProperty("Content-Type", "multipart/form-data; boundary=$boundary")
        conn.setRequestProperty("Accept", "application/json")
        token?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

        val imageBytes = android.util.Base64.decode(imageBase64, android.util.Base64.DEFAULT)

        conn.outputStream.use { os ->
            val writer = os.bufferedWriter(Charsets.UTF_8)
            
            if (latitude != null) {
                writer.write("--$boundary\r\n")
                writer.write("Content-Disposition: form-data; name=\"latitude\"\r\n\r\n")
                writer.write("$latitude\r\n")
            }
            if (longitude != null) {
                writer.write("--$boundary\r\n")
                writer.write("Content-Disposition: form-data; name=\"longitude\"\r\n\r\n")
                writer.write("$longitude\r\n")
            }
            if (description != null) {
                writer.write("--$boundary\r\n")
                writer.write("Content-Disposition: form-data; name=\"description\"\r\n\r\n")
                writer.write("$description\r\n")
            }
            if (city != null) {
                writer.write("--$boundary\r\n")
                writer.write("Content-Disposition: form-data; name=\"city\"\r\n\r\n")
                writer.write("$city\r\n")
            }
            if (userName != null) {
                writer.write("--$boundary\r\n")
                writer.write("Content-Disposition: form-data; name=\"user_name\"\r\n\r\n")
                writer.write("$userName\r\n")
            }
            
            writer.write("--$boundary\r\n")
            writer.write("Content-Disposition: form-data; name=\"image\"; filename=\"hazard.jpg\"\r\n")
            writer.write("Content-Type: image/jpeg\r\n\r\n")
            writer.flush()
            
            os.write(imageBytes)
            os.flush()
            
            writer.write("\r\n--$boundary--\r\n")
            writer.flush()
        }

        val responseCode = conn.responseCode
        val stream = if (responseCode == HttpURLConnection.HTTP_OK) {
            conn.inputStream
        } else {
            conn.errorStream
        }

        if (stream != null) {
            val responseText = stream.bufferedReader().use { it.readText() }
            val jsonResponse = JSONObject(responseText)
            if (jsonResponse.optBoolean("success", false)) {
                return@withContext jsonResponse.getJSONObject("data")
            } else {
                val errMsg = jsonResponse.optString("message", "API request failed")
                val detail = jsonResponse.optString("error", "")
                throw Exception(if (detail.isNotEmpty()) "$errMsg: $detail" else errMsg)
            }
        } else {
            throw Exception("HTTP connection failed with status code $responseCode")
        }
    }

    /**
     * Submit a new hazard report to backend.
     */
    suspend fun submitHazard(
        category: String,
        locationName: String,
        latitude: Double,
        longitude: Double,
        severity: String,
        description: String,
        aiAnalysisSummary: String?,
        imagePath: String?
    ): Boolean = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/hazards")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 15000
            conn.readTimeout = 15000
            conn.doOutput = true
            conn.setRequestProperty("Content-Type", "application/json")
            conn.setRequestProperty("Accept", "application/json")
            token?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

            val body = JSONObject().apply {
                put("category", category)
                put("location_name", locationName)
                put("latitude", latitude)
                put("longitude", longitude)
                put("severity", severity)
                put("description", description)
                put("ai_analysis_summary", aiAnalysisSummary)
                put("image_path", imagePath)
            }

            val writer = OutputStreamWriter(conn.outputStream)
            writer.write(body.toString())
            writer.flush()
            writer.close()

            val responseCode = conn.responseCode
            Log.d(TAG, "submitHazard response code: $responseCode")

            if (responseCode == HttpURLConnection.HTTP_CREATED || responseCode == HttpURLConnection.HTTP_OK) {
                val responseText = conn.inputStream.bufferedReader().use { it.readText() }
                Log.d(TAG, "submitHazard success response: $responseText")
                return@withContext true
            } else {
                // Read error body for diagnostics
                val errorText = conn.errorStream?.bufferedReader()?.use { it.readText() } ?: "No error body"
                Log.e(TAG, "submitHazard server error ($responseCode): $errorText")
            }
        } catch (e: Exception) {
            Log.e(TAG, "submitHazard exception: ${e.message}", e)
        }
        // Save locally as offline fallback if server submission fails
        saveOfflineHazard(category, locationName, latitude, longitude, severity, description, aiAnalysisSummary, imagePath)
        return@withContext false
    }

    /**
     * Submit a verification vote for a hazard.
     */
    suspend fun verifyHazard(id: String): Boolean = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/hazards/$id/verify")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 3000
            conn.readTimeout = 3000
            conn.setRequestProperty("Accept", "application/json")
            token?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                return@withContext true
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error verifying hazard: ${e.message}", e)
        }
        return@withContext false
    }

    /**
     * Mark a hazard as resolved.
     */
    suspend fun resolveHazard(id: String): Boolean = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/hazards/$id/resolve")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 3000
            conn.readTimeout = 3000
            conn.setRequestProperty("Accept", "application/json")
            token?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                return@withContext true
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error resolving hazard: ${e.message}", e)
        }
        return@withContext false
    }

    /**
     * Parse single JSONObject to HazardReport model.
     */
    private fun parseHazard(obj: JSONObject): HazardReport {
        val categoryRaw = obj.optString("category", "Pothole")
        
        // Map database categories to UI titles
        val title = when (categoryRaw.lowercase()) {
            "pothole" -> "Pothole"
            "open drain" -> "Open Drain"
            "open manhole" -> "Open Manhole"
            "waterlogging" -> "Water Logging"
            "broken streetlight" -> "Broken Street Light"
            "garbage" -> "Garbage Dump"
            else -> categoryRaw
        }

        val severityStr = obj.optString("severity", "Medium Risk")
        val severity = when {
            severityStr.contains("High", ignoreCase = true) || severityStr.contains("Critical", ignoreCase = true) -> Severity.HIGH
            severityStr.contains("Low", ignoreCase = true) -> Severity.LOW
            else -> Severity.MEDIUM
        }

        val statusStr = obj.optString("status", "Pending")
        val verificationStatus = if (statusStr.contains("Verified", ignoreCase = true) || statusStr.contains("Resolved", ignoreCase = true)) {
            VerificationStatus.VERIFIED
        } else {
            VerificationStatus.PENDING
        }

        return HazardReport(
            id = obj.optString("id", ""),
            title = title,
            category = categoryRaw,
            locationName = obj.optString("location_name", ""),
            latitude = obj.optDouble("latitude", 0.0),
            longitude = obj.optDouble("longitude", 0.0),
            severity = severity,
            verificationStatus = verificationStatus,
            verificationCount = obj.optInt("verification_count", 0),
            reportTime = "Recent", // Simple relative time string
            description = obj.optString("description", ""),
            aiAnalysisSummary = if (obj.isNull("ai_analysis_summary")) null else obj.getString("ai_analysis_summary"),
            imageUrl = if (obj.isNull("image_path")) null else {
                val path = obj.getString("image_path")
                when {
                    path.startsWith("http://") || path.startsWith("https://") -> path
                    path.startsWith("/") -> "$BASE_HOST$path"
                    else -> "$BASE_HOST/storage/$path"
                }
            },
            rawSeverity = severityStr
        )
    }

    suspend fun fetchGoogleRoutes(
        origin: LatLng,
        destination: LatLng,
        hazards: List<HazardReport>
    ): List<GoogleRouteInfo> = withContext(Dispatchers.IO) {
        val apiKey = "AIzaSyBF3rOaJLB3Bcl-PdkrtMXFTuLUp_xECl8"
        val originStr = "${origin.latitude},${origin.longitude}"
        val destStr = "${destination.latitude},${destination.longitude}"
        val urlStr = "https://maps.googleapis.com/maps/api/directions/json?origin=$originStr&destination=$destStr&alternatives=true&key=$apiKey"
        try {
            val url = URL(urlStr)
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.setRequestProperty("Accept", "application/json")
            
            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                val reader = BufferedReader(InputStreamReader(conn.inputStream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()
                return@withContext parseGoogleDirectionsResponse(response.toString(), hazards)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error fetching Google Routes: ${e.message}", e)
        }
        return@withContext emptyList()
    }

    private fun parseGoogleDirectionsResponse(jsonStr: String, hazards: List<HazardReport>): List<GoogleRouteInfo> {
        val list = mutableListOf<GoogleRouteInfo>()
        try {
            val root = JSONObject(jsonStr)
            val routesArray = root.optJSONArray("routes") ?: return emptyList()
            for (i in 0 until routesArray.length()) {
                val routeObj = routesArray.getJSONObject(i)
                val polylineObj = routeObj.optJSONObject("overview_polyline") ?: continue
                val encodedPoints = polylineObj.optString("points", "")
                if (encodedPoints.isBlank()) continue
                
                val points = decodePolyline(encodedPoints)
                
                val legsArray = routeObj.optJSONArray("legs")
                var durationText = ""
                var durationSeconds = 0
                var distanceText = ""
                var distanceMeters = 0
                
                if (legsArray != null && legsArray.length() > 0) {
                    var totalDurationSec = 0
                    var totalDistanceMet = 0
                    for (j in 0 until legsArray.length()) {
                        val leg = legsArray.getJSONObject(j)
                        val dur = leg.optJSONObject("duration")
                        if (dur != null) {
                            totalDurationSec += dur.optInt("value", 0)
                            if (durationText.isEmpty()) {
                                durationText = dur.optString("text", "")
                            }
                        }
                        val dist = leg.optJSONObject("distance")
                        if (dist != null) {
                            totalDistanceMet += dist.optInt("value", 0)
                            if (distanceText.isEmpty()) {
                                distanceText = dist.optString("text", "")
                            }
                        }
                    }
                    durationSeconds = totalDurationSec
                    distanceMeters = totalDistanceMet
                }
                
                val riskScore = calculateRouteRiskScore(points, hazards)
                
                list.add(
                    GoogleRouteInfo(
                        points = points,
                        durationText = durationText,
                        durationSeconds = durationSeconds,
                        distanceText = distanceText,
                        distanceMeters = distanceMeters,
                        riskScore = riskScore
                    )
                )
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error parsing Directions Response: ${e.message}", e)
        }
        return list
    }

    private fun decodePolyline(encoded: String): List<LatLng> {
        val poly = ArrayList<LatLng>()
        var index = 0
        val len = encoded.length
        var lat = 0
        var lng = 0

        while (index < len) {
            var b: Int
            var shift = 0
            var result = 0
            do {
                if (index >= len) break
                b = encoded[index++].code - 63
                result = result or (b and 0x1f shl shift)
                shift += 5
            } while (b >= 0x20)
            val dlat = if (result and 1 != 0) (result shr 1).inv() else result shr 1
            lat += dlat

            shift = 0
            result = 0
            do {
                if (index >= len) break
                b = encoded[index++].code - 63
                result = result or (b and 0x1f shl shift)
                shift += 5
            } while (b >= 0x20)
            val dlng = if (result and 1 != 0) (result shr 1).inv() else result shr 1
            lng += dlng

            val p = LatLng(lat.toDouble() / 1E5, lng.toDouble() / 1E5)
            poly.add(p)
        }
        return poly
    }

    fun calculateRouteRiskScore(routePoints: List<LatLng>, hazards: List<HazardReport>): Int {
        var score = 0
        val matchedHazardIds = mutableSetOf<String>()
        
        for (hazard in hazards) {
            for (point in routePoints) {
                val dist = distanceInMeters(hazard.latitude, hazard.longitude, point.latitude, point.longitude)
                if (dist <= 100.0) {
                    if (!matchedHazardIds.contains(hazard.id)) {
                        matchedHazardIds.add(hazard.id)
                        val severityWeight = when {
                            hazard.rawSeverity.contains("Critical", ignoreCase = true) -> 5
                            hazard.severity == Severity.HIGH -> 3
                            hazard.severity == Severity.MEDIUM -> 2
                            hazard.severity == Severity.LOW -> 1
                            else -> 1
                        }
                        score += severityWeight
                    }
                    break
                }
            }
        }
        return score
    }

    fun distanceInMeters(lat1: Double, lon1: Double, lat2: Double, lon2: Double): Double {
        val r = 6371000.0 // meters
        val dLat = Math.toRadians(lat2 - lat1)
        val dLon = Math.toRadians(lon2 - lon1)
        val a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(Math.toRadians(lat1)) * Math.cos(Math.toRadians(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2)
        val c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
        return r * c
    }

    suspend fun fetchPlaceSuggestions(input: String): List<PlaceSuggestion> = withContext(Dispatchers.IO) {
        val apiKey = "AIzaSyBF3rOaJLB3Bcl-PdkrtMXFTuLUp_xECl8"
        val encodedInput = java.net.URLEncoder.encode(input, "UTF-8")
        val urlStr = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=$encodedInput&key=$apiKey&location=25.18,75.83&radius=10000"
        try {
            val url = URL(urlStr)
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.setRequestProperty("Accept", "application/json")
            
            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                val reader = BufferedReader(InputStreamReader(conn.inputStream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()
                
                val root = JSONObject(response.toString())
                val predictions = root.optJSONArray("predictions") ?: return@withContext emptyList()
                val list = mutableListOf<PlaceSuggestion>()
                for (i in 0 until predictions.length()) {
                    val pred = predictions.getJSONObject(i)
                    list.add(
                        PlaceSuggestion(
                            description = pred.optString("description", ""),
                            placeId = pred.optString("place_id", "")
                        )
                    )
                }
                return@withContext list
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error fetching place suggestions: ${e.message}", e)
        }
        return@withContext emptyList()
    }

    suspend fun fetchPlaceDetails(placeId: String): Pair<LatLng, String>? = withContext(Dispatchers.IO) {
        val apiKey = "AIzaSyBF3rOaJLB3Bcl-PdkrtMXFTuLUp_xECl8"
        val urlStr = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$placeId&fields=geometry,formatted_address&key=$apiKey"
        try {
            val url = URL(urlStr)
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.setRequestProperty("Accept", "application/json")
            
            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                val reader = BufferedReader(InputStreamReader(conn.inputStream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()
                
                val root = JSONObject(response.toString())
                val result = root.optJSONObject("result") ?: return@withContext null
                val formattedAddress = result.optString("formatted_address", "")
                val geometry = result.optJSONObject("geometry") ?: return@withContext null
                val location = geometry.optJSONObject("location") ?: return@withContext null
                val lat = location.optDouble("lat", 0.0)
                val lng = location.optDouble("lng", 0.0)
                
                return@withContext Pair(LatLng(lat, lng), formattedAddress)
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error fetching place details: ${e.message}", e)
        }
        return@withContext null
    }

    suspend fun reverseGeocode(latLng: LatLng): String = withContext(Dispatchers.IO) {
        val apiKey = "AIzaSyBF3rOaJLB3Bcl-PdkrtMXFTuLUp_xECl8"
        val urlStr = "https://maps.googleapis.com/maps/api/geocode/json?latlng=${latLng.latitude},${latLng.longitude}&key=$apiKey"
        try {
            val url = URL(urlStr)
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.setRequestProperty("Accept", "application/json")
            
            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                val reader = BufferedReader(InputStreamReader(conn.inputStream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()
                
                val root = JSONObject(response.toString())
                val results = root.optJSONArray("results")
                if (results != null && results.length() > 0) {
                    return@withContext results.getJSONObject(0).optString("formatted_address", "Selected Location")
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error reverse geocoding: ${e.message}", e)
        }
        return@withContext "Selected Location (${String.format("%.4f", latLng.latitude)}, ${String.format("%.4f", latLng.longitude)})"
    }

    /**
     * Register a new user.
     */
    suspend fun register(name: String, email: String, password: String): AuthResponse? = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/auth/register")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.doOutput = true
            conn.setRequestProperty("Content-Type", "application/json")
            conn.setRequestProperty("Accept", "application/json")

            val body = JSONObject().apply {
                put("name", name)
                put("email", email)
                put("password", password)
            }

            val writer = OutputStreamWriter(conn.outputStream)
            writer.write(body.toString())
            writer.flush()
            writer.close()

            val responseCode = conn.responseCode
            val stream = if (responseCode == HttpURLConnection.HTTP_OK || responseCode == HttpURLConnection.HTTP_CREATED) {
                conn.inputStream
            } else {
                conn.errorStream
            }

            if (stream != null) {
                val reader = BufferedReader(InputStreamReader(stream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()

                val jsonResponse = JSONObject(response.toString())
                val success = jsonResponse.optBoolean("success", false)
                val message = jsonResponse.optString("message", "").takeIf { it.isNotEmpty() }

                if (success) {
                    val dataObj = jsonResponse.getJSONObject("data")
                    val userObj = dataObj.getJSONObject("user")
                    val authToken = dataObj.getString("token")

                    val authUser = AuthUser(
                        id = userObj.getInt("id"),
                        name = userObj.getString("name"),
                        email = userObj.getString("email"),
                        role = userObj.getString("role")
                    )
                    return@withContext AuthResponse(
                        success = true,
                        message = message,
                        data = AuthData(authUser, authToken)
                    )
                } else {
                    return@withContext AuthResponse(
                        success = false,
                        message = message ?: "Registration failed",
                        data = null
                    )
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error registering: ${e.message}", e)
        }
        return@withContext null
    }

    /**
     * Log in a user with email and password.
     */
    suspend fun login(email: String, password: String): AuthResponse? = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/auth/login")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.doOutput = true
            conn.setRequestProperty("Content-Type", "application/json")
            conn.setRequestProperty("Accept", "application/json")

            val body = JSONObject().apply {
                put("email", email)
                put("password", password)
            }

            val writer = OutputStreamWriter(conn.outputStream)
            writer.write(body.toString())
            writer.flush()
            writer.close()

            val responseCode = conn.responseCode
            val stream = if (responseCode == HttpURLConnection.HTTP_OK) {
                conn.inputStream
            } else {
                conn.errorStream
            }

            if (stream != null) {
                val reader = BufferedReader(InputStreamReader(stream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()

                val jsonResponse = JSONObject(response.toString())
                val success = jsonResponse.optBoolean("success", false)
                val message = jsonResponse.optString("message", "").takeIf { it.isNotEmpty() }

                if (success) {
                    val dataObj = jsonResponse.getJSONObject("data")
                    val userObj = dataObj.getJSONObject("user")
                    val authToken = dataObj.getString("token")

                    val authUser = AuthUser(
                        id = userObj.getInt("id"),
                        name = userObj.getString("name"),
                        email = userObj.getString("email"),
                        role = userObj.getString("role")
                    )
                    return@withContext AuthResponse(
                        success = true,
                        message = message,
                        data = AuthData(authUser, authToken)
                    )
                } else {
                    return@withContext AuthResponse(
                        success = false,
                        message = message ?: "Login failed",
                        data = null
                    )
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error logging in: ${e.message}", e)
        }
        return@withContext null
    }

    /**
     * Log in a user with Google.
     */
    suspend fun googleLogin(name: String, email: String, photoUrl: String?): AuthResponse? = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/auth/google-login")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.doOutput = true
            conn.setRequestProperty("Content-Type", "application/json")
            conn.setRequestProperty("Accept", "application/json")

            val body = JSONObject().apply {
                put("name", name)
                put("email", email)
                put("photo_url", photoUrl)
            }

            val writer = OutputStreamWriter(conn.outputStream)
            writer.write(body.toString())
            writer.flush()
            writer.close()

            val responseCode = conn.responseCode
            val stream = if (responseCode == HttpURLConnection.HTTP_OK) {
                conn.inputStream
            } else {
                conn.errorStream
            }

            if (stream != null) {
                val reader = BufferedReader(InputStreamReader(stream))
                val response = StringBuilder()
                var line: String?
                while (reader.readLine().also { line = it } != null) {
                    response.append(line)
                }
                reader.close()

                val jsonResponse = JSONObject(response.toString())
                val success = jsonResponse.optBoolean("success", false)
                val message = jsonResponse.optString("message", "").takeIf { it.isNotEmpty() }

                if (success) {
                    val dataObj = jsonResponse.getJSONObject("data")
                    val userObj = dataObj.getJSONObject("user")
                    val authToken = dataObj.getString("token")

                    val authUser = AuthUser(
                        id = userObj.getInt("id"),
                        name = userObj.getString("name"),
                        email = userObj.getString("email"),
                        role = userObj.getString("role")
                    )
                    return@withContext AuthResponse(
                        success = true,
                        message = message,
                        data = AuthData(authUser, authToken)
                    )
                } else {
                    return@withContext AuthResponse(
                        success = false,
                        message = message ?: "Google Login failed",
                        data = null
                    )
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error logging in with Google: ${e.message}", e)
        }
        return@withContext null
    }

    private fun executeRequest(method: String, endpoint: String, jsonBody: String? = null): String? {
        try {
            val url = URL("$BASE_URL/$endpoint")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = method
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.setRequestProperty("Accept", "application/json")
            token?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

            if (jsonBody != null && (method == "POST" || method == "PUT")) {
                conn.setRequestProperty("Content-Type", "application/json")
                conn.doOutput = true
                conn.outputStream.use { os ->
                    os.write(jsonBody.toByteArray(Charsets.UTF_8))
                }
            }

            val code = conn.responseCode
            val stream = if (code in 200..299) conn.inputStream else conn.errorStream
            if (stream != null) {
                return stream.bufferedReader().use { it.readText() }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Request failed: $method $endpoint", e)
        }
        return null
    }

    suspend fun fetchProfileStats(): JSONObject? = withContext(Dispatchers.IO) {
        val response = executeRequest("GET", "profile/stats")
        if (response != null) {
            try {
                val json = JSONObject(response)
                if (json.optBoolean("success", false)) {
                    return@withContext json.optJSONObject("data")
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing profile stats", e)
            }
        }
        return@withContext null
    }

    suspend fun fetchUserReports(): List<HazardReport> = withContext(Dispatchers.IO) {
        val response = executeRequest("GET", "profile/reports")
        val list = mutableListOf<HazardReport>()
        if (response != null) {
            try {
                val json = JSONObject(response)
                if (json.optBoolean("success", false)) {
                    val data = json.getJSONArray("data")
                    for (i in 0 until data.length()) {
                        list.add(parseHazard(data.getJSONObject(i)))
                    }
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing user reports", e)
            }
        }
        return@withContext list
    }

    suspend fun fetchSavedAlerts(): List<HazardReport> = withContext(Dispatchers.IO) {
        val response = executeRequest("GET", "profile/saved")
        val list = mutableListOf<HazardReport>()
        if (response != null) {
            try {
                val json = JSONObject(response)
                if (json.optBoolean("success", false)) {
                    val data = json.getJSONArray("data")
                    for (i in 0 until data.length()) {
                        list.add(parseHazard(data.getJSONObject(i)))
                    }
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing saved alerts", e)
            }
        }
        return@withContext list
    }

    suspend fun updateProfile(name: String, email: String, phone: String?): Boolean = withContext(Dispatchers.IO) {
        val body = JSONObject().apply {
            put("name", name)
            put("email", email)
            put("phone", phone)
        }
        val response = executeRequest("PUT", "profile", body.toString())
        if (response != null) {
            try {
                return@withContext JSONObject(response).optBoolean("success", false)
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing profile update response", e)
            }
        }
        return@withContext false
    }

    suspend fun changePassword(old: String, new: String): Pair<Boolean, String> = withContext(Dispatchers.IO) {
        val body = JSONObject().apply {
            put("old_password", old)
            put("new_password", new)
        }
        val response = executeRequest("PUT", "profile/password", body.toString())
        if (response != null) {
            try {
                val json = JSONObject(response)
                val success = json.optBoolean("success", false)
                val message = json.optString("message", "Operation completed")
                return@withContext Pair(success, message)
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing password change response", e)
            }
        }
        return@withContext Pair(false, "Network error or invalid password")
    }

    suspend fun updateSecurity(twoFactor: Boolean): Boolean = withContext(Dispatchers.IO) {
        val body = JSONObject().apply {
            put("two_factor_enabled", twoFactor)
        }
        val response = executeRequest("PUT", "profile/security", body.toString())
        if (response != null) {
            try {
                return@withContext JSONObject(response).optBoolean("success", false)
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing security update response", e)
            }
        }
        return@withContext false
    }

    suspend fun updateVerification(aadhaar: String): Boolean = withContext(Dispatchers.IO) {
        val body = JSONObject().apply {
            put("aadhaar_number", aadhaar)
        }
        val response = executeRequest("PUT", "profile/verification", body.toString())
        if (response != null) {
            try {
                return@withContext JSONObject(response).optBoolean("success", false)
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing verification update response", e)
            }
        }
        return@withContext false
    }

    suspend fun fetchPreferences(): JSONObject? = withContext(Dispatchers.IO) {
        val response = executeRequest("GET", "settings/preferences")
        if (response != null) {
            try {
                val json = JSONObject(response)
                if (json.optBoolean("success", false)) {
                    return@withContext json.optJSONObject("data")
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing preferences", e)
            }
        }
        return@withContext null
    }

    suspend fun updatePreferences(prefs: JSONObject): Boolean = withContext(Dispatchers.IO) {
        val response = executeRequest("PUT", "settings/preferences", prefs.toString())
        if (response != null) {
            try {
                return@withContext JSONObject(response).optBoolean("success", false)
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing preferences update response", e)
            }
        }
        return@withContext false
    }

    suspend fun fetchNotifications(): List<NotificationItem> = withContext(Dispatchers.IO) {
        val response = executeRequest("GET", "notifications")
        val list = mutableListOf<NotificationItem>()
        if (response != null) {
            try {
                val json = JSONObject(response)
                if (json.optBoolean("success", false)) {
                    val data = json.getJSONArray("data")
                    for (i in 0 until data.length()) {
                        val obj = data.getJSONObject(i)
                        list.add(
                            NotificationItem(
                                id = obj.optString("id", ""),
                                title = obj.optString("title", ""),
                                body = obj.optString("body", ""),
                                type = obj.optString("type", "Announcement"),
                                time = obj.optString("created_at", "Just now")
                            )
                        )
                    }
                }
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing notifications response", e)
            }
        }
        return@withContext list
    }

    suspend fun deleteNotification(id: String): Boolean = withContext(Dispatchers.IO) {
        val response = executeRequest("DELETE", "notifications/$id")
        if (response != null) {
            try {
                return@withContext JSONObject(response).optBoolean("success", false)
            } catch (e: Exception) {
                Log.e(TAG, "Error parsing delete notification response", e)
            }
        }
        return@withContext false
    }
}

data class GoogleRouteInfo(
    val points: List<LatLng>,
    val durationText: String,
    val durationSeconds: Int,
    val distanceText: String,
    val distanceMeters: Int,
    val riskScore: Int,
    val isSafest: Boolean = false
)

data class PlaceSuggestion(
    val description: String,
    val placeId: String
)

data class AuthResponse(
    val success: Boolean,
    val message: String?,
    val data: AuthData?
)

data class AuthData(
    val user: AuthUser,
    val token: String
)

data class AuthUser(
    val id: Int,
    val name: String,
    val email: String,
    val role: String
)
