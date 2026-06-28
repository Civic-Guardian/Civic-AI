package com.nagarrakshak.ui.navigation

import androidx.compose.animation.EnterTransition
import androidx.compose.animation.ExitTransition
import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.navArgument
import com.nagarrakshak.ui.screens.*

@Composable
fun NagarRakshakNavGraph(
    navController: NavHostController,
    startDestination: String = Screen.Splash.route
) {
    NavHost(
        navController = navController,
        startDestination = startDestination,
        enterTransition = { EnterTransition.None },
        exitTransition = { ExitTransition.None },
        popEnterTransition = { EnterTransition.None },
        popExitTransition = { ExitTransition.None }
    ) {
        composable(Screen.Splash.route) {
            SplashScreen(
                onNavigateToHome = {
                    navController.navigate(Screen.Home.route) {
                        popUpTo(Screen.Splash.route) { inclusive = true }
                    }
                },
                onNavigateToAuth = {
                    navController.navigate(Screen.Auth.route) {
                        popUpTo(Screen.Splash.route) { inclusive = true }
                    }
                },
                onNavigateToMaintenance = {
                    navController.navigate(Screen.Maintenance.route) {
                        popUpTo(Screen.Splash.route) { inclusive = true }
                    }
                },
                onNavigateToUpdate = { requiredVersion ->
                    navController.navigate(Screen.AppUpdate.createRoute(requiredVersion)) {
                        popUpTo(Screen.Splash.route) { inclusive = true }
                    }
                }
            )
        }
        composable(Screen.Auth.route) {
            AuthScreen(
                onNavigateToHome = {
                    navController.navigate(Screen.Home.route) {
                        popUpTo(Screen.Auth.route) { inclusive = true }
                    }
                }
            )
        }
        composable(Screen.Home.route) {
            HomeScreen(
                onNavigateToReport = { navController.navigate(Screen.Report.route) },
                onNavigateToDetail = { hazardId -> navController.navigate(Screen.HazardDetail.createRoute(hazardId)) },
                onNavigateToMap = { navController.navigate(Screen.Map.route) },
                onNavigateToAlerts = { navController.navigate(Screen.Alerts.route) },
                onNavigateToNotifications = { navController.navigate(Screen.Notifications.route) }
            )
        }
        composable(Screen.Map.route) {
            MapScreen(
                onNavigateToDetail = { hazardId -> navController.navigate(Screen.HazardDetail.createRoute(hazardId)) }
            )
        }
        composable(Screen.Report.route) {
            ReportScreen(
                onReportSubmitted = { navController.popBackStack() }
            )
        }
        composable(Screen.Alerts.route) {
            AlertsScreen(
                onNavigateToDetail = { hazardId -> navController.navigate(Screen.HazardDetail.createRoute(hazardId)) },
                onNavigateToNotifications = { navController.navigate(Screen.Notifications.route) }
            )
        }
        composable(Screen.Leaderboard.route) {
            LeaderboardScreen()
        }
        composable(Screen.Profile.route) {
            ProfileScreen(
                onNavigateToDetail = { hazardId -> navController.navigate(Screen.HazardDetail.createRoute(hazardId)) },
                onNavigateToSettings = { navController.navigate(Screen.Settings.route) },
                onNavigateToReports = { navController.navigate(Screen.MyReports.route) },
                onNavigateToSavedAlerts = { navController.navigate(Screen.SavedAlerts.route) },
                onNavigateToImpact = { navController.navigate(Screen.MyImpact.route) },
                onLogout = {
                    navController.navigate(Screen.Auth.route) {
                        popUpTo(Screen.Home.route) { inclusive = true }
                    }
                },
                onBackClicked = {
                    navController.popBackStack()
                }
            )
        }
        composable(Screen.Settings.route) {
            SettingsScreen(
                onNavigateToPersonalInfo = { navController.navigate(Screen.PersonalInfo.route) },
                onNavigateToChangePassword = { navController.navigate(Screen.ChangePassword.route) },
                onNavigateToAccountSecurity = { navController.navigate(Screen.AccountSecurity.route) },
                onNavigateToVerificationDetails = { navController.navigate(Screen.VerificationDetails.route) },
                onNavigateToNotifications = { navController.navigate(Screen.NotificationSettings.route) },
                onNavigateToLocationSettings = { navController.navigate(Screen.LocationSettings.route) },
                onNavigateToDataUsage = { navController.navigate(Screen.DataUsage.route) },
                onNavigateToVoiceSounds = { navController.navigate(Screen.VoiceSoundAlerts.route) },
                onBackClicked = { navController.popBackStack() },
                onLogout = {
                    navController.navigate(Screen.Auth.route) {
                        popUpTo(Screen.Home.route) { inclusive = true }
                    }
                }
            )
        }
        composable(Screen.MyReports.route) {
            MyReportsScreen(
                onNavigateToDetail = { id -> navController.navigate(Screen.HazardDetail.createRoute(id)) },
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.SavedAlerts.route) {
            SavedAlertsScreen(
                onNavigateToDetail = { id -> navController.navigate(Screen.HazardDetail.createRoute(id)) },
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.MyImpact.route) {
            MyImpactScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.PersonalInfo.route) {
            PersonalInfoScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.ChangePassword.route) {
            ChangePasswordScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.AccountSecurity.route) {
            AccountSecurityScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.VerificationDetails.route) {
            VerificationDetailsScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.NotificationSettings.route) {
            NotificationSettingsScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.LocationSettings.route) {
            LocationSettingsScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.DataUsage.route) {
            DataUsageScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.VoiceSoundAlerts.route) {
            VoiceSoundAlertsScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.Notifications.route) {
            NotificationsScreen(
                onBackClicked = { navController.popBackStack() }
            )
        }
        composable(Screen.Maintenance.route) {
            MaintenanceScreen(
                onMaintenanceCleared = {
                    navController.navigate(Screen.Splash.route) {
                        popUpTo(Screen.Maintenance.route) { inclusive = true }
                    }
                }
            )
        }
        composable(
            route = Screen.AppUpdate.route,
            arguments = listOf(navArgument("requiredVersion") { type = NavType.StringType })
        ) { backStackEntry ->
            val requiredVersion = backStackEntry.arguments?.getString("requiredVersion") ?: "1.3.0"
            AppUpdateScreen(
                requiredVersion = requiredVersion,
                onUpdateInstalled = {
                    navController.navigate(Screen.Splash.route) {
                        popUpTo(Screen.AppUpdate.route) { inclusive = true }
                    }
                }
            )
        }
        composable(
            route = Screen.HazardDetail.route,
            arguments = listOf(navArgument("hazardId") { type = NavType.StringType })
        ) { backStackEntry ->
            val hazardId = backStackEntry.arguments?.getString("hazardId") ?: ""
            DetailScreen(
                hazardId = hazardId,
                onBackClicked = { navController.popBackStack() }
            )
        }
    }
}
