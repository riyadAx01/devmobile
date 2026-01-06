package com.example.caftanvue.data

import com.jakewharton.retrofit2.converter.kotlinx.serialization.asConverterFactory
import kotlinx.serialization.json.Json
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.http.*

// 10.0.2.2 is the special IP for emulator to reach host machine
// This MUST be 10.0.2.2 for Android emulator (NOT localhost)
private const val BASE_URL = "http://10.0.2.2:8000/"

private val json = Json {
    ignoreUnknownKeys = true
    coerceInputValues = true
}

private val loggingInterceptor = HttpLoggingInterceptor().apply {
    level = HttpLoggingInterceptor.Level.BODY
}

private val okHttpClient = OkHttpClient.Builder()
    .addInterceptor(loggingInterceptor)
    .build()

private val retrofit = Retrofit.Builder()
    .addConverterFactory(json.asConverterFactory("application/json".toMediaType()))
    .baseUrl(BASE_URL)
    .client(okHttpClient)
    .build()

interface CaftanApiService {
    // Public endpoints - Caftans
    @GET("v1/caftans")
    suspend fun getCaftans(): List<Caftan>

    @GET("v1/caftans/{id}")
    suspend fun getCaftan(@Path("id") id: Int): Caftan
    
    // Authentication endpoints
    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): AuthResponse
    
    @POST("auth/register")
    suspend fun register(@Body request: RegisterRequest): AuthResponse
    
    // Admin endpoints (require Authorization header)
    @GET("v1/admin/caftans")
    suspend fun getAdminCaftans(@Header("Authorization") token: String): List<Caftan>
    
    @POST("v1/caftans")
    suspend fun createCaftan(@Body caftan: Caftan): Caftan
    
    @PUT("v1/caftans/{id}")
    suspend fun updateCaftan(@Path("id") id: Int, @Body caftan: Caftan): Caftan
    
    @DELETE("v1/admin/caftans/{id}")
    suspend fun deleteCaftan(
        @Header("Authorization") token: String,
        @Path("id") id: Int
    ): MessageResponse

    // Clients endpoints - now using v1/admin/caftans

    // Clients endpoints
    @GET("v1/clients")
    suspend fun getClients(): List<Client>

    @GET("v1/clients/{id}")
    suspend fun getClient(@Path("id") id: Int): Client

    @POST("v1/clients")
    suspend fun createClient(@Body client: Client): Client

    @PUT("v1/clients/{id}")
    suspend fun updateClient(@Path("id") id: Int, @Body client: Client): Client

    @DELETE("v1/clients/{id}")
    suspend fun deleteClient(@Path("id") id: Int)

    // Reservations endpoints
    @GET("v1/reservations")
    suspend fun getReservations(): List<Reservation>

    @GET("v1/reservations/{id}")
    suspend fun getReservation(@Path("id") id: Int): Reservation

    @POST("v1/reservations")
    suspend fun createReservation(@Body reservation: Reservation): Reservation

    @PUT("v1/reservations/{id}")
    suspend fun updateReservation(@Path("id") id: Int, @Body reservation: Reservation): Reservation

    @DELETE("reservations/{id}")
    suspend fun deleteReservation(@Path("id") id: Int)

    // Removed duplicate auth endpoints - using auth/login above
}

object CaftanApi {
    val retrofitService: CaftanApiService by lazy {
        retrofit.create(CaftanApiService::class.java)
    }
}
