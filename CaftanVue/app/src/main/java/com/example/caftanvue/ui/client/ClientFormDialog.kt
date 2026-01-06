package com.example.caftanvue.ui.client

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.caftanvue.data.Client

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ClientFormDialog(
    client: Client? = null,
    onDismiss: () -> Unit,
    onSave: (Client) -> Unit
) {
    var name by remember { mutableStateOf(client?.name ?: "") }
    var email by remember { mutableStateOf(client?.email ?: "") }
    var phone by remember { mutableStateOf(client?.phone ?: "") }
    var address by remember { mutableStateOf(client?.address ?: "") }
    var cin by remember { mutableStateOf(client?.cin ?: "") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(if (client == null) "Add New Client" else "Edit Client") },
        text = {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Name *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true
                )

                OutlinedTextField(
                    value = email,
                    onValueChange = { email = it },
                    label = { Text("Email *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    placeholder = { Text("example@email.com") }
                )

                OutlinedTextField(
                    value = phone,
                    onValueChange = { phone = it },
                    label = { Text("Phone *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    placeholder = { Text("+212 6XX XXX XXX") }
                )

                OutlinedTextField(
                    value = address,
                    onValueChange = { address = it },
                    label = { Text("Address *") },
                    modifier = Modifier.fillMaxWidth(),
                    minLines = 2,
                    placeholder = { Text("Full address") }
                )

                OutlinedTextField(
                    value = cin,
                    onValueChange = { cin = it },
                    label = { Text("CIN (ID Card) *") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    placeholder = { Text("e.g., AB123456") }
                )
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    val newClient = Client(
                        id = client?.id ?: 0,
                        name = name,
                        email = email,
                        phone = phone,
                        address = address,
                        cin = cin,
                        createdAt = client?.createdAt ?: ""
                    )
                    onSave(newClient)
                },
                enabled = name.isNotBlank() && email.isNotBlank() && phone.isNotBlank() && address.isNotBlank() && cin.isNotBlank()
            ) {
                Text(if (client == null) "Add" else "Save")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel")
            }
        }
    )
}
