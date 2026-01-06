package com.example.caftanvue.ui.admin

import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Event
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Pending
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.caftanvue.ui.caftan.CaftanViewModel
import com.example.caftanvue.ui.reservation.ReservationViewModel

@Composable
fun AdminDashboard(
    caftanViewModel: CaftanViewModel = viewModel(),
    reservationViewModel: ReservationViewModel = viewModel()
) {
    val caftans by caftanViewModel.caftans.collectAsState()
    val reservations by reservationViewModel.reservations.collectAsState()
    
    val totalCaftans = caftans.size
    val totalReservations = reservations.size
    val confirmedReservations = reservations.count { it.status.equals("confirmed", ignoreCase = true) }
    val pendingReservations = reservations.count { it.status.equals("pending", ignoreCase = true) }
    
    LaunchedEffect(Unit) {
        caftanViewModel.getCaftans()
        reservationViewModel.getReservations()
    }
    
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Text(
            text = "Admin Dashboard",
            style = MaterialTheme.typography.headlineMedium,
            fontWeight = FontWeight.Bold
        )
        
        // Statistics Cards
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            StatCard(
                title = "Caftans",
                value = totalCaftans.toString(),
                icon = Icons.Default.Home,
                color = MaterialTheme.colorScheme.primary,
                modifier = Modifier.weight(1f)
            )
            
            StatCard(
                title = "Reservations",
                value = totalReservations.toString(),
                icon = Icons.Default.Event,
                color = MaterialTheme.colorScheme.secondary,
                modifier = Modifier.weight(1f)
            )
        }
        
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            StatCard(
                title = "Confirmed",
                value = confirmedReservations.toString(),
                icon = Icons.Default.CheckCircle,
                color = MaterialTheme.colorScheme.tertiary,
                modifier = Modifier.weight(1f)
            )
            
            StatCard(
                title = "Pending",
                value = pendingReservations.toString(),
                icon = Icons.Default.Pending,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.weight(1f)
            )
        }
        
        Divider()
        
        Text(
            text = "Quick Stats",
            style = MaterialTheme.typography.titleMedium,
            fontWeight = FontWeight.SemiBold
        )
        
        Text(
            text = "• You have $totalCaftans caftan(s) available",
            style = MaterialTheme.typography.bodyMedium
        )
        Text(
            text = "• $pendingReservations reservation(s) waiting for confirmation",
            style = MaterialTheme.typography.bodyMedium
        )
        Text(
            text = "• $confirmedReservations reservation(s) confirmed",
            style = MaterialTheme.typography.bodyMedium
        )
    }
}

@Composable
fun StatCard(
    title: String,
    value: String,
    icon: ImageVector,
    color: androidx.compose.ui.graphics.Color,
    modifier: Modifier = Modifier
) {
    Card(
        modifier = modifier,
        colors = CardDefaults.cardColors(
            containerColor = color.copy(alpha = 0.1f)
        )
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            Icon(
                imageVector = icon,
                contentDescription = title,
                tint = color,
                modifier = Modifier.size(32.dp)
            )
            
            Text(
                text = value,
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Bold,
                color = color
            )
            
            Text(
                text = title,
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant
            )
        }
    }
}
