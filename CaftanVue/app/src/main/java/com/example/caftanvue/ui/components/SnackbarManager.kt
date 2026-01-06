package com.example.caftanvue.ui.components

import androidx.compose.material3.SnackbarDuration
import androidx.compose.material3.SnackbarHostState
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.launch

class SnackbarManager(
    private val snackbarHostState: SnackbarHostState,
    private val scope: CoroutineScope
) {
    fun showSuccess(message: String) {
        scope.launch {
            snackbarHostState.showSnackbar(
                message = "✓ $message",
                duration = SnackbarDuration.Short
            )
        }
    }
    
    fun showError(message: String) {
        scope.launch {
            snackbarHostState.showSnackbar(
                message = "✗ $message",
                duration = SnackbarDuration.Long
            )
        }
    }
}
