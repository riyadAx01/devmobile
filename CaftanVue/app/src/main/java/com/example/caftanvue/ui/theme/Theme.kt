package com.example.caftanvue.ui.theme

import android.app.Activity
import android.os.Build
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.dynamicDarkColorScheme
import androidx.compose.material3.dynamicLightColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext

private val DarkColorScheme = darkColorScheme(
    primary = CaftanBlue,
    secondary = CaftanCoffee,
    tertiary = CaftanBrown,
    background = Color(0xFF121212),
    surface = Color(0xFF121212),
    onPrimary = CaftanWhite,
    onSecondary = CaftanWhite,
    onTertiary = CaftanWhite,
    onBackground = CaftanWhite,
    onSurface = CaftanWhite,
)

private val LightColorScheme = lightColorScheme(
    primary = CaftanBlue,
    secondary = CaftanCoffee,
    tertiary = CaftanBrown,
    background = CaftanWhite,
    surface = CaftanWhite,
    onPrimary = CaftanWhite,
    onSecondary = CaftanWhite,
    onTertiary = CaftanWhite,
    onBackground = Color(0xFF1C1B1F),
    onSurface = Color(0xFF1C1B1F),
)

@Composable
fun CaftanVueTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    // Dynamic color is available on Android 12+
    dynamicColor: Boolean = false,
    content: @Composable () -> Unit
) {
    val colorScheme = when {
        dynamicColor && Build.VERSION.SDK_INT >= Build.VERSION_CODES.S -> {
            val context = LocalContext.current
            if (darkTheme) dynamicDarkColorScheme(context) else dynamicLightColorScheme(context)
        }

        darkTheme -> DarkColorScheme
        else -> LightColorScheme
    }

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography,
        content = content
    )
}