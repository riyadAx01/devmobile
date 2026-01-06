package com.example.caftanvue.ui.client

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.caftanvue.data.CaftanApi
import com.example.caftanvue.data.Client
import kotlinx.coroutines.launch
import java.io.IOException

sealed interface ClientUiState {
    data class Success(val clients: List<Client>) : ClientUiState
    object Error : ClientUiState
    object Loading : ClientUiState
}

class ClientViewModel : ViewModel() {

    var clientUiState: ClientUiState by mutableStateOf(ClientUiState.Loading)
        private set

    var searchQuery by mutableStateOf("")
        private set

    init {
        getClients()
    }

    fun getClients() {
        viewModelScope.launch {
            clientUiState = ClientUiState.Loading
            clientUiState = try {
                ClientUiState.Success(CaftanApi.retrofitService.getClients())
            } catch (e: IOException) {
                ClientUiState.Error
            }
        }
    }

    fun createClient(client: Client, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.createClient(client)
                getClients()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun updateClient(id: Int, client: Client, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.updateClient(id, client)
                getClients()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun deleteClient(id: Int, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.deleteClient(id)
                getClients()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun updateSearchQuery(query: String) {
        searchQuery = query
    }
}
