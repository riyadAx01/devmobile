package com.example.caftanvue.ui.caftan

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.compose.runtime.snapshotFlow
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.caftanvue.data.Caftan
import com.example.caftanvue.data.CaftanApi
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch
import java.io.IOException

sealed interface CaftanUiState {
    data class Success(val caftans: List<Caftan>) : CaftanUiState
    object Error : CaftanUiState
    object Loading : CaftanUiState
}

class CaftanViewModel : ViewModel() {

    var caftanUiState: CaftanUiState by mutableStateOf(CaftanUiState.Loading)
        private set
    
    // Expose caftans list for dashboard
    val caftans: StateFlow<List<Caftan>> = snapshotFlow {
        when (val state = caftanUiState) {
            is CaftanUiState.Success -> state.caftans
            else -> emptyList()
        }
    }.stateIn(viewModelScope, SharingStarted.Lazily, emptyList())

    var searchQuery by mutableStateOf("")
        private set

    var selectedCollection by mutableStateOf<String?>(null)
        private set

    var selectedColor by mutableStateOf<String?>(null)
        private set

    var selectedStatus by mutableStateOf<String?>(null)
        private set

    init {
        getCaftans()
    }

    fun getCaftans() {
        viewModelScope.launch {
            caftanUiState = CaftanUiState.Loading
            caftanUiState = try {
                CaftanUiState.Success(CaftanApi.retrofitService.getCaftans())
            } catch (e: IOException) {
                CaftanUiState.Error
            }
        }
    }

    // TODO: Implement search after adding search endpoint
    fun searchCaftans() {
        // Temporarily using getCaftans() - will add search endpoint later
        getCaftans()
    }

    fun createCaftan(caftan: Caftan, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.createCaftan(caftan)
                getCaftans() // Refresh list
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun updateCaftan(id: Int, caftan: Caftan, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.updateCaftan(id, caftan)
                getCaftans() // Refresh list
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun deleteCaftan(id: Int, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.deleteCaftan("Bearer token_placeholder", id)
                getCaftans() // Refresh list
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun updateSearchQuery(query: String) {
        searchQuery = query
    }

    fun setCollection(collection: String?) {
        selectedCollection = collection
    }

    fun setColor(color: String?) {
        selectedColor = color
    }

    fun setStatus(status: String?) {
        selectedStatus = status
    }

    fun clearFilters() {
        searchQuery = ""
        selectedCollection = null
        selectedColor = null
        selectedStatus = null
        getCaftans()
    }
}