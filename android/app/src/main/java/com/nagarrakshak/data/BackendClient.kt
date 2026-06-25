package com.nagarrakshak.data

import android.util.Log
import com.google.android.gms.maps.model.LatLng
import com.nagarrakshak.data.models.HazardReport
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
    // Use 10.0.2.2 to point to host machine's localhost from Android Emulator
    private const val BASE_URL = "http://10.0.2.2:8000/api"

    // Hardcoded mock fallbacks to keep app functional if backend is offline or empty
    val mockHazards = listOf(
        HazardReport(
            id = "1",
            title = "Open Drain",
            category = "Open Drain",
            locationName = "Talwandi, Kota",
            latitude = 25.18254,
            longitude = 75.82736,
            severity = Severity.HIGH,
            verificationStatus = VerificationStatus.PENDING,
            verificationCount = 14,
            reportTime = "2h ago",
            description = "Uncovered open drain canal causing foul smell, water contamination, and mosquito breeding. Poses high risk to pedestrians and children."
        ),
        HazardReport(
            id = "2",
            title = "Garbage Dump",
            category = "Garbage Dump",
            locationName = "Mahaveer Nagar, Kota",
            latitude = 25.18421,
            longitude = 75.82912,
            severity = Severity.MEDIUM,
            verificationStatus = VerificationStatus.VERIFIED,
            verificationCount = 22,
            reportTime = "5h ago",
            description = "Accumulated garbage dump pile blocking part of the street side. Not collected for 3 days, causing bad odor and hygienic concerns."
        ),
        HazardReport(
            id = "3",
            title = "Water Logging",
            category = "Water Logging",
            locationName = "Shrinath Puram, Kota",
            latitude = 25.18112,
            longitude = 75.82512,
            severity = Severity.HIGH,
            verificationStatus = VerificationStatus.VERIFIED,
            verificationCount = 8,
            reportTime = "6h ago",
            description = "Severe waterlogging on road street after heavy rain, making it difficult for vehicles and two-wheelers to navigate safely."
        ),
        HazardReport(
            id = "4",
            title = "Broken Street Light",
            category = "Broken Street Light",
            locationName = "Vivekananda Nagar, Kota",
            latitude = 25.18556,
            longitude = 75.82667,
            severity = Severity.MEDIUM,
            verificationStatus = VerificationStatus.VERIFIED,
            verificationCount = 3,
            reportTime = "1d ago",
            description = "Road streetlight is broken or completely inactive for over a week, leading to dark blindspots and increasing safety risks at night."
        )
    )

    /**
     * Fetch nearby hazard reports from backend.
     */
    suspend fun fetchNearbyHazards(): List<HazardReport> = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/hazards")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "GET"
            conn.connectTimeout = 3000
            conn.readTimeout = 3000
            conn.setRequestProperty("Accept", "application/json")

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
                    val list = mutableListOf<HazardReport>()
                    for (i in 0 until dataArray.length()) {
                        val obj = dataArray.getJSONObject(i)
                        list.add(parseHazard(obj))
                    }
                    return@withContext list
                }
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error fetching hazards from backend: ${e.message}. Falling back to mock data.")
        }
        return@withContext mockHazards
    }

    /**
     * Fetch detail of a single hazard.
     */
    suspend fun fetchHazardDetail(id: String): HazardReport? = withContext(Dispatchers.IO) {
        // Since we load all hazards and they are small in number, we can fetch all and find the match
        val list = fetchNearbyHazards()
        return@withContext list.find { it.id == id } ?: mockHazards.find { it.id == id }
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
        aiAnalysisSummary: String?
    ): Boolean = withContext(Dispatchers.IO) {
        try {
            val url = URL("$BASE_URL/hazards")
            val conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.connectTimeout = 5000
            conn.readTimeout = 5000
            conn.doOutput = true
            conn.setRequestProperty("Content-Type", "application/json")
            conn.setRequestProperty("Accept", "application/json")

            val body = JSONObject().apply {
                put("category", category)
                put("location_name", locationName)
                put("latitude", latitude)
                put("longitude", longitude)
                put("severity", severity)
                put("description", description)
                put("ai_analysis_summary", aiAnalysisSummary)
            }

            val writer = OutputStreamWriter(conn.outputStream)
            writer.write(body.toString())
            writer.flush()
            writer.close()

            if (conn.responseCode == HttpURLConnection.HTTP_CREATED || conn.responseCode == HttpURLConnection.HTTP_OK) {
                return@withContext true
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error submitting hazard: ${e.message}")
        }
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

            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                return@withContext true
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error verifying hazard: ${e.message}")
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

            if (conn.responseCode == HttpURLConnection.HTTP_OK) {
                return@withContext true
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error resolving hazard: ${e.message}")
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
            imageUrl = if (obj.isNull("image_path")) null else obj.getString("image_path")
        )
    }
}
