package com.example.caftanvue.data

import kotlinx.serialization.Serializable

@Serializable
data class Client(
    val id: Int,
    val name: String,
    val email: String,
    val phone: String,
    val address: String,
    val cin: String, // Moroccan ID card number
    val createdAt: String
)