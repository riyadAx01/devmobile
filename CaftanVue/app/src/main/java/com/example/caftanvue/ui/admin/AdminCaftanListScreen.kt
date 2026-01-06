package com.example.caftanvue.ui.admin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.compose.foundation.background
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import coil.compose.AsyncImage
import com.example.caftanvue.data.Caftan
import com.example.caftanvue.ui.caftan.CaftanViewModel
import com.example.caftanvue.ui.caftan.CaftanUiState
import com.example.caftanvue.ui.caftan.CaftanFormDialog
import com.example.caftanvue.ui.components.SnackbarManager

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminCaftanListScreen(
    viewModel: CaftanViewModel = viewModel(),
    snackbarManager: SnackbarManager
) {
    var showAddDialog by remember { mutableStateOf(false) }
    var showEditDialog by remember { mutableStateOf(false) }
    var showDeleteDialog by remember { mutableStateOf(false) }
    var selectedCaftan by remember { mutableStateOf<Caftan?>(null) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("My Caftans") },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.primaryContainer,
                    titleContentColor = MaterialTheme.colorScheme.onPrimaryContainer
                )
            )
        },
        floatingActionButton = {
            FloatingActionButton(
                onClick = { showAddDialog = true },
                containerColor = MaterialTheme.colorScheme.primary
            ) {
                Icon(Icons.Default.Add, "Add Caftan")
            }
        }
    ) { padding ->
        when (val state = viewModel.caftanUiState) {
            is CaftanUiState.Loading -> {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(padding),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator()
                }
            }
            is CaftanUiState.Error -> {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(padding),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(
                            "Error loading caftans",
                            style = MaterialTheme.typography.bodyLarge,
                            color = MaterialTheme.colorScheme.error
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Button(onClick = { viewModel.getCaftans() }) {
                            Text("Retry")
                        }
                    }
                }
            }
            is CaftanUiState.Success -> {
                if (state.caftans.isEmpty()) {
                    Box(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(padding),
                        contentAlignment = Alignment.Center
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(
                                "No caftans yet",
                                style = MaterialTheme.typography.bodyLarge
                            )
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                "Click + to add your first caftan",
                                style = MaterialTheme.typography.bodyMedium,
                                color = MaterialTheme.colorScheme.onSurfaceVariant
                            )
                        }
                    }
                } else {
                    LazyVerticalGrid(
                        columns = GridCells.Fixed(2),
                        modifier = Modifier.padding(padding),
                        contentPadding = PaddingValues(16.dp),
                        horizontalArrangement = Arrangement.spacedBy(12.dp),
                        verticalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        items(state.caftans) { caftan ->
                            AdminCaftanCard(
                                caftan = caftan,
                                onEdit = {
                                    selectedCaftan = caftan
                                    showEditDialog = true
                                },
                                onDelete = {
                                    selectedCaftan = caftan
                                    showDeleteDialog = true
                                }
                            )
                        }
                    }
                }
            }
        }
    }

    // Add Dialog
    if (showAddDialog) {
        CaftanFormDialog(
            caftan = null,
            onDismiss = { showAddDialog = false },
            onSave = { caftan ->
                viewModel.createCaftan(
                    caftan,
                    onSuccess = {
                        snackbarManager.showSuccess("Caftan added successfully!")
                        showAddDialog = false
                    },
                    onError = {
                        snackbarManager.showError("Failed to add caftan")
                    }
                )
            }
        )
    }

    // Edit Dialog
    if (showEditDialog && selectedCaftan != null) {
        CaftanFormDialog(
            caftan = selectedCaftan,
            onDismiss = { showEditDialog = false },
            onSave = { caftan ->
                viewModel.updateCaftan(
                    caftan.id,
                    caftan,
                    onSuccess = {
                        snackbarManager.showSuccess("Caftan updated!")
                        showEditDialog = false
                    },
                    onError = {
                        snackbarManager.showError("Failed to update caftan")
                    }
                )
            }
        )
    }

    // Delete Confirmation Dialog
    if (showDeleteDialog && selectedCaftan != null) {
        AlertDialog(
            onDismissRequest = { showDeleteDialog = false },
            title = { Text("Delete Caftan?") },
            text = {
                Text("Are you sure you want to delete \"${selectedCaftan!!.name}\"? This action cannot be undone.")
            },
            confirmButton = {
                Button(
                    onClick = {
                        viewModel.deleteCaftan(
                            selectedCaftan!!.id,
                            onSuccess = {
                                snackbarManager.showSuccess("Caftan deleted")
                                showDeleteDialog = false
                            },
                            onError = {
                                snackbarManager.showError("Failed to delete caftan")
                            }
                        )
                    },
                    colors = ButtonDefaults.buttonColors(
                        containerColor = MaterialTheme.colorScheme.error
                    )
                ) {
                    Text("Delete")
                }
            },
            dismissButton = {
                TextButton(onClick = { showDeleteDialog = false }) {
                    Text("Cancel")
                }
            }
        )
    }
}

@Composable
fun AdminCaftanCard(
    caftan: Caftan,
    onEdit: () -> Unit,
    onDelete: () -> Unit
) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
    ) {
        Column {
            // Image
            val imageModel = remember(caftan.imageUrl) {
                val cleanUrl = caftan.imageUrl?.replace("localhost:", "10.0.2.2:")?.replace("127.0.0.1:", "10.0.2.2:")
                if (cleanUrl != null) "$cleanUrl?t=${System.currentTimeMillis()}" else null
            }

            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(180.dp)
                    .background(Color.White)
            ) {
                AsyncImage(
                    model = imageModel,
                    contentDescription = caftan.name,
                    modifier = Modifier.fillMaxSize(),
                    contentScale = ContentScale.Crop,
                    error = painterResource(android.R.drawable.stat_notify_error),
                    placeholder = painterResource(android.R.drawable.ic_menu_gallery)
                )
            }

            // Details
            Column(
                modifier = Modifier.padding(12.dp)
            ) {
                Text(
                    text = "#${caftan.id} - ${caftan.name}",
                    style = MaterialTheme.typography.titleMedium,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = "${caftan.price} MAD",
                    style = MaterialTheme.typography.bodyLarge,
                    color = MaterialTheme.colorScheme.primary
                )
                Spacer(modifier = Modifier.height(4.dp))
                
                // Status Badge
                Surface(
                    color = when (caftan.status) {
                        "available" -> MaterialTheme.colorScheme.primaryContainer
                        "reserved" -> MaterialTheme.colorScheme.secondaryContainer
                        else -> MaterialTheme.colorScheme.errorContainer
                    },
                    shape = MaterialTheme.shapes.small
                ) {
                    Text(
                        text = caftan.status.uppercase(),
                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp),
                        style = MaterialTheme.typography.labelSmall
                    )
                }

                Spacer(modifier = Modifier.height(8.dp))

                // Action Buttons
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    OutlinedButton(
                        onClick = onEdit,
                        modifier = Modifier.weight(1f),
                        contentPadding = PaddingValues(8.dp)
                    ) {
                        Icon(
                            Icons.Default.Edit,
                            contentDescription = "Edit",
                            modifier = Modifier.size(16.dp)
                        )
                        Spacer(Modifier.width(4.dp))
                        Text("Edit", style = MaterialTheme.typography.labelSmall)
                    }

                    OutlinedButton(
                        onClick = onDelete,
                        modifier = Modifier.weight(1f),
                        colors = ButtonDefaults.outlinedButtonColors(
                            contentColor = MaterialTheme.colorScheme.error
                        ),
                        contentPadding = PaddingValues(8.dp)
                    ) {
                        Icon(
                            Icons.Default.Delete,
                            contentDescription = "Delete",
                            modifier = Modifier.size(16.dp)
                        )
                        Spacer(Modifier.width(4.dp))
                        Text("Delete", style = MaterialTheme.typography.labelSmall)
                    }
                }
            }
        }
    }
}
