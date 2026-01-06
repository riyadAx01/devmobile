package com.example.caftanvue

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.width
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.People
import androidx.compose.material.icons.filled.DateRange
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.NavigationBar
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.unit.dp
import androidx.navigation.NavDestination.Companion.hierarchy
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.example.caftanvue.ui.theme.CaftanVueTheme
import com.example.caftanvue.ui.caftan.CaftanListScreen
import com.example.caftanvue.ui.caftan.CaftanFormDialog
import com.example.caftanvue.ui.client.ClientListScreen
import com.example.caftanvue.ui.client.ClientFormDialog
import com.example.caftanvue.ui.client.ClientViewModel
import com.example.caftanvue.ui.reservation.ReservationListScreen
import com.example.caftanvue.ui.reservation.ReservationFormDialog
import com.example.caftanvue.ui.admin.AdminDashboard
import com.example.caftanvue.ui.admin.AdminCaftanListScreen
import com.example.caftanvue.ui.auth.LoginDialog
import com.example.caftanvue.ui.components.SnackbarManager
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.automirrored.filled.Logout
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.caftanvue.ui.caftan.CaftanViewModel
import com.example.caftanvue.ui.reservation.ReservationViewModel

sealed class Screen(val route: String, val label: String, val icon: ImageVector) {
    object Caftan : Screen("caftan", "Caftans", Icons.Default.Home)
    object Client : Screen("client", "Clients", Icons.Default.People)
    object Reservation : Screen("reservation", "Bookings", Icons.Default.DateRange)
    object Dashboard : Screen("dashboard", "Dashboard", Icons.Default.Dashboard)
    object AdminCaftans : Screen("admin_caftans", "My Caftans", Icons.Default.Home)
}

val items = listOf(
    Screen.Caftan,
    Screen.Client,
    Screen.Reservation,
)

val adminItems = listOf(
    Screen.Dashboard,
    Screen.Caftan,           // Browse all caftans (client view)
    Screen.AdminCaftans,     // Manage my caftans (admin view)
    Screen.Client,
    Screen.Reservation,
)

@OptIn(ExperimentalMaterial3Api::class)
class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            CaftanVueTheme {
                val navController = rememberNavController()
                var isAdminLoggedIn by remember { mutableStateOf(false) }
                var showLoginDialog by remember { mutableStateOf(false) }
                val snackbarHostState = remember { SnackbarHostState() }
                val scope = rememberCoroutineScope()
                
                // Admin CRUD dialogs
                var showCaftanForm by remember { mutableStateOf(false) }
                var showReservationForm by remember { mutableStateOf(false) }
                var showClientForm by remember { mutableStateOf(false) }
                var editingReservation by remember { mutableStateOf<com.example.caftanvue.data.Reservation?>(null) }
                
                if (showLoginDialog) {
                    LoginDialog(
                        onDismiss = { showLoginDialog = false },
                        onLoginSuccess = { isAdminLoggedIn = true }
                    )
                }
                
                val caftanViewModel: CaftanViewModel = viewModel()
                val reservationViewModel: ReservationViewModel = viewModel()
                val clientViewModel: ClientViewModel = viewModel()
                val snackbarManager = remember { SnackbarManager(snackbarHostState, scope) }
                
                // Caftan Form Dialog
                if (showCaftanForm && isAdminLoggedIn) {
                    CaftanFormDialog(
                        onDismiss = { showCaftanForm = false },
                        onSave = { caftan ->
                            caftanViewModel.createCaftan(
                                caftan,
                                onSuccess = {
                                    snackbarManager.showSuccess("Caftan added successfully!")
                                    showCaftanForm = false
                                },
                                onError = {
                                    snackbarManager.showError("Failed to add caftan")
                                }
                            )
                        }
                    )
                }
                
                // Reservation Form Dialog (Create or Edit)
                if ((showReservationForm || editingReservation != null) && isAdminLoggedIn) {
                    ReservationFormDialog(
                        reservation = editingReservation,
                        onDismiss = { 
                            showReservationForm = false
                            editingReservation = null
                        },
                        onSave = { reservation ->
                            if (editingReservation == null) {
                                reservationViewModel.createReservation(
                                    reservation,
                                    onSuccess = {
                                        snackbarManager.showSuccess("Reservation created!")
                                        showReservationForm = false
                                    },
                                    onError = {
                                        snackbarManager.showError("Failed to create reservation")
                                    }
                                )
                            } else {
                                reservationViewModel.updateReservation(
                                    reservation.id,
                                    reservation,
                                    onSuccess = {
                                        snackbarManager.showSuccess("Reservation updated!")
                                        editingReservation = null
                                    },
                                    onError = {
                                        snackbarManager.showError("Failed to update reservation")
                                    }
                                )
                            }
                        }
                    )
                }
                
                // Client Form Dialog
                if (showClientForm && isAdminLoggedIn) {
                    ClientFormDialog(
                        onDismiss = { showClientForm = false },
                        onSave = { client ->
                            clientViewModel.createClient(
                                client,
                                onSuccess = {
                                    snackbarManager.showSuccess("Client added successfully!")
                                    showClientForm = false
                                },
                                onError = {
                                    snackbarManager.showError("Failed to add client")
                                }
                            )
                        }
                    )
                }
                
                Scaffold(
                    topBar = {
                        TopAppBar(
                            title = { Text("CaftanVue") },
                            actions = {
                                if (isAdminLoggedIn) {
                                    IconButton(onClick = { isAdminLoggedIn = false }) {
                                        Icon(Icons.AutoMirrored.Filled.Logout, "Logout")
                                    }
                                } else {
                                    TextButton(onClick = { showLoginDialog = true }) {
                                        Icon(Icons.Default.Person, contentDescription = null)
                                        Spacer(Modifier.width(4.dp))
                                        Text("Admin Login")
                                    }
                                }
                            }
                        )
                    },
                    bottomBar = {
                        NavigationBar {
                            val navBackStackEntry by navController.currentBackStackEntryAsState()
                            val currentDestination = navBackStackEntry?.destination
                            // Filter items based on admin status
                            val visibleItems = if (isAdminLoggedIn) {
                                adminItems // Show all tabs including dashboard for admin
                            } else {
                                listOf(Screen.Caftan) // Only show Caftans for clients
                            }
                            visibleItems.forEach { screen ->
                                NavigationBarItem(
                                    icon = { Icon(screen.icon, contentDescription = null) },
                                    label = { Text(screen.label) },
                                    selected = currentDestination?.hierarchy?.any { it.route == screen.route } == true,
                                    onClick = {
                                        navController.navigate(screen.route) {
                                            // Pop up to the start destination of the graph to
                                            // avoid building up a large stack of destinations
                                            // on the back stack as users select items
                                            popUpTo(navController.graph.findStartDestination().id) {
                                                saveState = true
                                            }
                                            // Avoid multiple copies of the same destination when
                                            // reselecting the same item
                                            launchSingleTop = true
                                            // Restore state when reselecting a previously selected item
                                            restoreState = true
                                        }
                                    }
                                )
                            }
                        }
                    },
                    snackbarHost = { SnackbarHost(snackbarHostState) }
                ) { innerPadding ->
                    NavHost(navController, startDestination = Screen.Caftan.route, Modifier.padding(innerPadding)) {
                        composable(Screen.Dashboard.route) {
                            AdminDashboard()
                        }
                        composable(Screen.AdminCaftans.route) {
                            AdminCaftanListScreen(
                                snackbarManager = snackbarManager
                            )
                        }
                        composable(Screen.Caftan.route) {
                            CaftanListScreen(
                                onAddClick = { if (isAdminLoggedIn) showCaftanForm = true }
                            )
                        }
                        composable(Screen.Client.route) {
                            ClientListScreen(
                                onAddClick = { if (isAdminLoggedIn) showClientForm = true }
                            )
                        }
                        composable(Screen.Reservation.route) {
                            ReservationListScreen(
                                onReservationClick = { reservation ->
                                    if (isAdminLoggedIn) editingReservation = reservation
                                },
                                onAddClick = { if (isAdminLoggedIn) showReservationForm = true }
                            )
                        }
                    }
                }
            }
        }
    }
}
