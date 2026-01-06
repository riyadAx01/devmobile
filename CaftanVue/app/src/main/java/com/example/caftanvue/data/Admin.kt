package com.example.caftanvue.data

import kotlinx.serialization.Serializable

@Serializable
data class Admin(
    val id: Int,
    val username: String,
    val email: String,
    val shopName: String? = null,
    val shopAddress: String? = null
)

@Serializable
data class AuthResponse(
    val admin: Admin,
    val token: String
)

@Serializable
data class LoginRequest(
    val email: String,
    val password: String
)

@Serializable
data class RegisterRequest(
    val username: String,
    val email: String,
    val password: String,
    val shop_name: String,
    val shop_address: String
)

@Serializable
data class LoginResponse(
    val success: Boolean,
    val admin: Admin?,
    val message: String
)
