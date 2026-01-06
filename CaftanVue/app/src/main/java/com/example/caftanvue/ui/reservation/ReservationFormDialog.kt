package com.example.caftanvue.ui.reservation

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.caftanvue.data.Reservation
import java.time.LocalDate
import java.time.format.DateTimeFormatter

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReservationFormDialog(
    reservation: Reservation? = null,
    onDismiss: () -> Unit,
    onSave: (Reservation) -> Unit
) {
    var caftanId by remember { mutableStateOf(reservation?.caftanId?.toString() ?: "") }
    var clientId by remember { mutableStateOf(reservation?.clientId?.toString() ?: "") }
    var startDate by remember { mutableStateOf(reservation?.startDate ?: LocalDate.now().toString()) }
    var endDate by remember { mutableStateOf(reservation?.endDate ?: LocalDate.now().plusDays(7).toString()) }
    var totalPrice by remember { mutableStateOf(reservation?.totalPrice?.toString() ?: "") }
    var status by remember { mutableStateOf(reservation?.status ?: "pending") }
    var notes by remember { mutableStateOf(reservation?.notes ?: "") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(if (reservation == null) "Create Reservation" else "Edit Reservation") },
        text = {
            Column(
                modifier = Modifier.fillMaxWidth(),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                OutlinedTextField(
                    value = caftanId,
                    onValueChange = { caftanId = it },
                    label = { Text("Caftan ID *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    enabled = reservation == null // Usually caftan ID doesn't change for a booking
                )

                OutlinedTextField(
                    value = clientId,
                    onValueChange = { clientId = it },
                    label = { Text("Client ID *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    enabled = reservation == null
                )

                // Status dropdown (visible when editing)
                var expanded by remember { mutableStateOf(false) }
                ExposedDropdownMenuBox(
                    expanded = expanded,
                    onExpandedChange = { expanded = !expanded }
                ) {
                    OutlinedTextField(
                        value = status.uppercase(),
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
                        listOf("pending", "confirmed", "completed", "cancelled").forEach { option ->
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

                OutlinedTextField(
                    value = startDate,
                    onValueChange = { startDate = it },
                    label = { Text("Start Date (YYYY-MM-DD)") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = endDate,
                    onValueChange = { endDate = it },
                    label = { Text("End Date (YYYY-MM-DD)") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = totalPrice,
                    onValueChange = { totalPrice = it },
                    label = { Text("Total Price (MAD)") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = notes,
                    onValueChange = { notes = it },
                    label = { Text("Notes") },
                    modifier = Modifier.fillMaxWidth(),
                    minLines = 2
                )
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    val newReservation = Reservation(
                        id = reservation?.id ?: 0,
                        caftanId = caftanId.toIntOrNull() ?: 0,
                        clientId = clientId.toIntOrNull() ?: 0,
                        startDate = startDate,
                        endDate = endDate,
                        status = status,
                        totalPrice = totalPrice.toDoubleOrNull() ?: 0.0,
                        notes = notes.ifBlank { null }
                    )
                    onSave(newReservation)
                },
                enabled = caftanId.isNotBlank() && clientId.isNotBlank()
            ) {
                Text(if (reservation == null) "Create" else "Save")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel")
            }
        }
    )
}
