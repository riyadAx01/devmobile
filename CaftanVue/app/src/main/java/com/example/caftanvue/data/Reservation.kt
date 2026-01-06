package com.example.caftanvue.data

import kotlinx.serialization.Serializable

@Serializable
data class Reservation(
    val id: Int,
    val caftanId: Int,
    val clientId: Int,
    val startDate: String,
    val endDate: String,
    val status: String, // "pending", "confirmed", "completed", "cancelled"
    val totalPrice: Double,
    val notes: String? = null
)