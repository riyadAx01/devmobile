# PowerShell Script to Download Sample Caftan Images
# Save as: download_caftans.ps1

$outputDir = "C:\Users\abdel\AndroidStudioProjects\CaftanVue-API\public\images\caftans"

# Create directory if it doesn't exist
New-Item -ItemType Directory -Force -Path $outputDir | Out-Null

Write-Host "Downloading Moroccan Caftan images..." -ForegroundColor Green

# Sample caftan images (using placeholder images - replace with actual URLs)
$images = @(
    @{ url = "https://picsum.photos/800/1200?random=1"; name = "caftan_traditional_01.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=2"; name = "caftan_modern_01.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=3"; name = "caftan_wedding_01.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=4"; name = "caftan_traditional_02.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=5"; name = "caftan_modern_02.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=6"; name = "caftan_wedding_02.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=7"; name = "caftan_casual_01.jpg" }
    @{ url = "https://picsum.photos/800/1200?random=8"; name = "caftan_casual_02.jpg" }
)

foreach ($image in $images) {
    $outputPath = Join-Path $outputDir $image.name
    Write-Host "Downloading $($image.name)..." -ForegroundColor Cyan
    
    try {
        Invoke-WebRequest -Uri $image.url -OutFile $outputPath -UseBasicParsing
        Write-Host "✓ Downloaded: $($image.name)" -ForegroundColor Green
    } catch {
        Write-Host "✗ Failed to download $($image.name): $_" -ForegroundColor Red
    }
}

Write-Host "`nDownload complete! Images saved to: $outputDir" -ForegroundColor Green
Write-Host "`nIMPORTANT: Replace the placeholder URLs in this script with actual Moroccan caftan image URLs!" -ForegroundColor Yellow
