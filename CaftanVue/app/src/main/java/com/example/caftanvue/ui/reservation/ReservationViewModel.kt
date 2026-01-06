package com.example.caftanvue.ui.reservation

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.compose.runtime.snapshotFlow
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.caftanvue.data.CaftanApi
import com.example.caftanvue.data.Reservation
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch
import java.io.IOException

sealed interface ReservationUiState {
    data class Success(val reservations: List<Reservation>) : ReservationUiState
    object Error : ReservationUiState
    object Loading : ReservationUiState
}

class ReservationViewModel : ViewModel() {

    var reservationUiState: ReservationUiState by mutableStateOf(ReservationUiState.Loading)
        private set
    
    // Expose reservations list for dashboard
    val reservations: StateFlow<List<Reservation>> = snapshotFlow {
        when (val state = reservationUiState) {
            is ReservationUiState.Success -> state.reservations
            else -> emptyList()
        }
    }.stateIn(viewModelScope, SharingStarted.Lazily, emptyList())

    var filterStatus by mutableStateOf<String?>(null)
        private set

    var searchQuery by mutableStateOf("")
        private set

    init {
        getReservations()
    }

    fun getReservations() {
        viewModelScope.launch {
            reservationUiState = ReservationUiState.Loading
            reservationUiState = try {
                ReservationUiState.Success(CaftanApi.retrofitService.getReservations())
            } catch (e: IOException) {
                ReservationUiState.Error
            }
        }
    }

    // ... existing CRUD methods ...
    fun createReservation(reservation: Reservation, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.createReservation(reservation)
                getReservations()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun updateReservation(id: Int, reservation: Reservation, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.updateReservation(id, reservation)
                getReservations()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun deleteReservation(id: Int, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                CaftanApi.retrofitService.deleteReservation(id)
                getReservations()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun confirmReservation(reservation: Reservation, onSuccess: () -> Unit, onError: () -> Unit) {
        viewModelScope.launch {
            try {
                val updated = reservation.copy(status = "confirmed")
                CaftanApi.retrofitService.updateReservation(reservation.id, updated)
                getReservations()
                onSuccess()
            } catch (e: Exception) {
                onError()
            }
        }
    }

    fun updateSearchQuery(query: String) {
        searchQuery = query
    }

    fun updateFilterStatus(status: String?) {
        filterStatus = status
    }
}
