package com.example.caftanvue.ui.caftan

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.unit.dp
import coil.compose.AsyncImage
import com.example.caftanvue.data.Caftan

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CaftanFormDialog(
    caftan: Caftan? = null,
    onDismiss: () -> Unit,
    onSave: (Caftan) -> Unit
) {
    var idStr by remember { mutableStateOf(if (caftan != null) caftan.id.toString() else "") }
    var name by remember { mutableStateOf(caftan?.name ?: "") }
    var description by remember { mutableStateOf(caftan?.description ?: "") }
    var imageUrl by remember { 
        val rawUrl = caftan?.imageUrl ?: ""
        // Extract just the filename if it's a full URL
        val cleanUrl = if (rawUrl.contains("/")) rawUrl.substringAfterLast("/") else rawUrl
        mutableStateOf(cleanUrl)
    }
    var price by remember { mutableStateOf(caftan?.price?.toString() ?: "") }
    var collection by remember { mutableStateOf(caftan?.collection ?: "") }
    var color by remember { mutableStateOf(caftan?.color ?: "") }
    var size by remember { mutableStateOf(caftan?.size ?: "") }
    var status by remember { mutableStateOf(caftan?.status ?: "available") }
    var shopAddress by remember { mutableStateOf(caftan?.shopAddress ?: "") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(if (caftan == null) "Add New Caftan" else "Edit Caftan") },
        text = {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                 OutlinedTextField(
                    value = idStr,
                    onValueChange = { if (it.all { char -> char.isDigit() }) idStr = it },
                    label = { Text("ID *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    enabled = caftan == null, // Only editable when creating
                    placeholder = { Text("e.g. 101") }
                )

                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Name *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = description,
                    onValueChange = { description = it },
                    label = { Text("Description *") },
                    modifier = Modifier.fillMaxWidth(),
                    minLines = 3
                )

                OutlinedTextField(
                    value = imageUrl,
                    onValueChange = { imageUrl = it },
                    label = { Text("Image URL") },
                    modifier = Modifier.fillMaxWidth(),
                    placeholder = { Text("https://example.com/image.jpg") },
                    singleLine = true
                )

                if (imageUrl.isNotBlank()) {
                    var trimmedUrl = imageUrl.trim()
                    
                    // Handle file:/// URLs (from browser drag-drop)
                    if (trimmedUrl.startsWith("file:///")) {
                        // Extract just the filename
                        trimmedUrl = trimmedUrl.substringAfterLast("/")
                    }
                    
                    val previewUrl = if (trimmedUrl.startsWith("http")) {
                        trimmedUrl
                    } else {
                        // For convenience, if user types just the filename, point to uploads
                        val relativePath = if (trimmedUrl.startsWith("uploads/")) {
                            trimmedUrl
                        } else {
                            "uploads/caftans/$trimmedUrl"
                        }
                        // Auto-add .png if no extension is present
                        val finalPath = if (relativePath.contains(".")) relativePath else "$relativePath.png"
                        "http://10.0.2.2:8000/$finalPath"
                    }

                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(150.dp)
                    ) {
                        Box(
                            modifier = Modifier
                                .fillMaxSize()
                                .background(Color.White)
                        ) {
                            AsyncImage(
                                model = previewUrl.replace("localhost:", "10.0.2.2:").replace("127.0.0.1:", "10.0.2.2:"),
                                contentDescription = "Preview",
                                modifier = Modifier.fillMaxSize(),
                                contentScale = ContentScale.Crop,
                                alignment = Alignment.Center,
                                error = painterResource(android.R.drawable.stat_notify_error),
                                placeholder = painterResource(android.R.drawable.ic_menu_gallery),
                                onError = {
                                    // Clear preview if URL is invalid (like just a directory)
                                }
                            )
                        }
                    }
                }

                OutlinedTextField(
                    value = price,
                    onValueChange = { price = it },
                    label = { Text("Price (MAD) *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = collection,
                    onValueChange = { collection = it },
                    label = { Text("Collection") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    placeholder = { Text("e.g., Spring 2024") }
                )

                OutlinedTextField(
                    value = color,
                    onValueChange = { color = it },
                    label = { Text("Color") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = size,
                    onValueChange = { size = it },
                    label = { Text("Size") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    placeholder = { Text("e.g., M, L, XL") }
                )

                OutlinedTextField(
                    value = shopAddress,
                    onValueChange = { shopAddress = it },
                    label = { Text("Shop Address") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    placeholder = { Text("e.g., Casablanca, Morocco") }
                )

                // Status dropdown
                var expanded by remember { mutableStateOf(false) }
                ExposedDropdownMenuBox(
                    expanded = expanded,
                    onExpandedChange = { expanded = !expanded }
                ) {
                    OutlinedTextField(
                        value = status,
                        onValueChange = {},
                        readOnly = true,
                        label = { Text("Status") },
                        trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) },
                        modifier = Modifier
                            .fillMaxWidth()
                            .menuAnchor()
                    )
                    ExposedDropdownMenu(
                        expanded = expanded,
                        onDismissRequest = { expanded = false }
                    ) {
                        listOf("available", "reserved", "rented").forEach { option ->
                            DropdownMenuItem(
                                text = { Text(option.uppercase()) },
                                onClick = {
                                    status = option
                                    expanded = false
                                }
                            )
                        }
                    }
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    val priceValue = price.toDoubleOrNull() ?: 0.0
                    // Use entered ID if creating new, otherwise keep existing ID
                    val idValue = if (caftan == null) (idStr.toIntOrNull() ?: 0) else caftan.id
                    
                    val newCaftan = Caftan(
                        id = idValue,
                        name = name,
                        description = description,
                        imageUrl = imageUrl.ifBlank { null },
                        price = priceValue,
                        collection = collection.ifBlank { "Default" },
                        color = color.ifBlank { "Mixed" },
                        size = size.ifBlank { "One Size" },
                        status = status,
                        isAvailable = status == "available",
                        shopAddress = shopAddress.ifBlank { null },
                        shopName = caftan?.shopName,
                        adminId = caftan?.adminId ?: 1
                    )
                    onSave(newCaftan)
                },
                enabled = name.isNotBlank() && description.isNotBlank() && price.isNotBlank() && (caftan != null || idStr.isNotBlank())
            ) {
                Text(if (caftan == null) "Add" else "Save")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel")
            }
        }
    )
}
