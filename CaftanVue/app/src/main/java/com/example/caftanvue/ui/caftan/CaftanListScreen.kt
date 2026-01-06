package com.example.caftanvue.ui.caftan

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.FilterList
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.compose.foundation.background
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import coil.compose.AsyncImage
import com.example.caftanvue.data.Caftan

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CaftanListScreen(
    viewModel: CaftanViewModel = viewModel(),
    onCaftanClick: (Caftan) -> Unit = {},
    onAddClick: () -> Unit = {}
) {
    var showFilterDialog by remember { mutableStateOf(false) }
    var searchText by remember { mutableStateOf("") }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Caftans") },
                actions = {
                    IconButton(onClick = { showFilterDialog = true }) {
                        Icon(Icons.Default.FilterList, "Filters")
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.primaryContainer,
                    titleContentColor = MaterialTheme.colorScheme.onPrimaryContainer
                )
            )
        },
        floatingActionButton = {
            FloatingActionButton(
                onClick = onAddClick,
                containerColor = MaterialTheme.colorScheme.primary
            ) {
                Icon(Icons.Default.Add, "Add Caftan")
            }
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
        ) {
            // Search Bar
            OutlinedTextField(
                value = searchText,
                onValueChange = {
                    searchText = it
                    viewModel.updateSearchQuery(it)
                    viewModel.searchCaftans()
                },
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                placeholder = { Text("Search caftans...") },
                leadingIcon = { Icon(Icons.Default.Search, null) },
                singleLine = true
            )

            // Content
            when (val state = viewModel.caftanUiState) {
                is CaftanUiState.Loading -> {
                    Box(
                        modifier = Modifier.fillMaxSize(),
                        contentAlignment = Alignment.Center
                    ) {
                        CircularProgressIndicator()
                    }
                }
                is CaftanUiState.Error -> {
                    Box(
                        modifier = Modifier.fillMaxSize(),
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
                            modifier = Modifier.fillMaxSize(),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                "No caftans found",
                                style = MaterialTheme.typography.bodyLarge
                            )
                        }
                    } else {
                        LazyVerticalGrid(
                            columns = GridCells.Fixed(2),
                            contentPadding = PaddingValues(16.dp),
                            horizontalArrangement = Arrangement.spacedBy(12.dp),
                            verticalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            items(state.caftans) { caftan ->
                                CaftanCard(
                                    caftan = caftan,
                                    onClick = { onCaftanClick(caftan) }
                                )
                            }
                        }
                    }
                }
            }
        }
    }

    // Filter Dialog
    if (showFilterDialog) {
        FilterDialog(
            viewModel = viewModel,
            onDismiss = { showFilterDialog = false },
            onApply = {
                viewModel.searchCaftans()
                showFilterDialog = false
            }
        )
    }
}

@Composable
fun CaftanCard(
    caftan: Caftan,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(onClick = onClick),
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
                    .clip(CardDefaults.shape)
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
                    text = caftan.name,
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
                Surface(
                    color = if (caftan.status == "available")
                        MaterialTheme.colorScheme.primaryContainer
                    else
                        MaterialTheme.colorScheme.errorContainer,
                    shape = MaterialTheme.shapes.small
                ) {
                    Text(
                        text = caftan.status,
                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp),
                        style = MaterialTheme.typography.labelSmall
                    )
                }
            }
        }
    }
}

@Composable
fun FilterDialog(
    viewModel: CaftanViewModel,
    onDismiss: () -> Unit,
    onApply: () -> Unit
) {
    val collections = listOf("Traditional", "Modern", "Wedding", "Casual")
    val colors = listOf("Red", "Blue", "Green", "Gold", "Silver", "White", "Black")
    val statuses = listOf("available", "rented", "maintenance")

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Filter Caftans") },
        text = {
            Column {
                Text("Collection", style = MaterialTheme.typography.labelLarge)
                collections.forEach { collection ->
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .clickable {
                                viewModel.setCollection(
                                    if (viewModel.selectedCollection == collection) null else collection
                                )
                            }
                            .padding(vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        RadioButton(
                            selected = viewModel.selectedCollection == collection,
                            onClick = {
                                viewModel.setCollection(
                                    if (viewModel.selectedCollection == collection) null else collection
                                )
                            }
                        )
                        Text(collection)
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                Text("Status", style = MaterialTheme.typography.labelLarge)
                statuses.forEach { status ->
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .clickable {
                                viewModel.setStatus(
                                    if (viewModel.selectedStatus == status) null else status
                                )
                            }
                            .padding(vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        RadioButton(
                            selected = viewModel.selectedStatus == status,
                            onClick = {
                                viewModel.setStatus(
                                    if (viewModel.selectedStatus == status) null else status
                                )
                            }
                        )
                        Text(status)
                    }
                }
            }
        },
        confirmButton = {
            TextButton(onClick = onApply) {
                Text("Apply")
            }
        },
        dismissButton = {
            TextButton(onClick = {
                viewModel.clearFilters()
                onDismiss()
            }) {
                Text("Clear")
            }
        }
    )
}
