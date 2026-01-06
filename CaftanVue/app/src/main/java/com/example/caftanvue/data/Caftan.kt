package com.example.caftanvue.data

import kotlinx.serialization.Serializable
import kotlinx.serialization.Transient

@Serializable
data class Caftan(
    val id: Int,
    val name: String,
    val description: String,
    val imageUrl: String?,
    val price: Double,
    val collection: String, // e.g., "Traditional", "Modern", "Wedding", "Casual"
    val color: String, // Primary color
    val size: String, // S, M, L, XL
    val status: String, // "available", "rented", "maintenance"
    val isAvailable: Boolean,
    val shopAddress: String? = null,
    val shopName: String? = null,
    val adminId: Int
)