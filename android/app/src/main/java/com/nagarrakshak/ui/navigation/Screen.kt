package com.nagarrakshak.ui.navigation

sealed class Screen(val route: String, val title: String) {
    object Splash : Screen("splash", "Splash")
    object Auth : Screen("auth", "Auth")
    object Home : Screen("home", "Home")
    object Map : Screen("map?lat={lat}&lng={lng}&name={name}", "Safety Map") {
        fun createRoute(lat: Double, lng: Double, name: String) = "map?lat=$lat&lng=$lng&name=$name"
    }
    object Report : Screen("report", "Report Hazard")
    object Alerts : Screen("alerts", "Alerts")
    object Leaderboard : Screen("leaderboard", "Leaderboard")
    object Profile : Screen("profile", "Profile")
    object Settings : Screen("settings", "Settings")
    object MyReports : Screen("my_reports", "My Reports")
    object SavedAlerts : Screen("saved_alerts", "Saved Alerts")
    object MyImpact : Screen("my_impact", "My Impact")
    object PersonalInfo : Screen("personal_info", "Personal Information")
    object ChangePassword : Screen("change_password", "Change Password")
    object AccountSecurity : Screen("account_security", "Account Security")
    object VerificationDetails : Screen("verification_details", "Verification Details")
    object NotificationSettings : Screen("notification_settings", "Notification Settings")
    object LocationSettings : Screen("location_settings", "Location Settings")
    object DataUsage : Screen("data_usage", "Data Usage")
    object VoiceSoundAlerts : Screen("voice_sound_alerts", "Voices & Sound Alerts")
    object Notifications : Screen("notifications", "Notifications")
    object Maintenance : Screen("maintenance", "Maintenance")
    object AppUpdate : Screen("app_update/{requiredVersion}", "App Update") {
        fun createRoute(requiredVersion: String) = "app_update/$requiredVersion"
    }
    object HazardDetail : Screen("hazard_detail/{hazardId}", "Hazard Details") {
        fun createRoute(hazardId: String) = "hazard_detail/$hazardId"
    }
}
