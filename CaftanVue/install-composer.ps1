# Download and Install Composer automatically

Write-Host "=== Installing Composer for CaftanVue Backend ===" -ForegroundColor Green
Write-Host ""

# Download Composer installer
$installerPath = "$env:TEMP\composer-setup.exe"
$composerUrl = "https://getcomposer.org/Composer-Setup.exe"

Write-Host "Downloading Composer installer..." -ForegroundColor Cyan
Invoke-WebRequest -Uri $composerUrl -OutFile $installerPath -UseBasicParsing

Write-Host "Installer downloaded to: $installerPath" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANT: Running the installer now..." -ForegroundColor Yellow
Write-Host ""
Write-Host "When the installer asks for PHP location, use:" -ForegroundColor Yellow
Write-Host "   C:\xampp\php\php.exe" -ForegroundColor Cyan
Write-Host ""
Write-Host "Click through the installer (use defaults for everything else)" -ForegroundColor Yellow
Write-Host ""

# Run the installer
Start-Process -FilePath $installerPath -Wait

Write-Host ""
Write-Host "=== Composer installation complete! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Close this PowerShell window" -ForegroundColor White
Write-Host "2. Open a NEW PowerShell window" -ForegroundColor White
Write-Host "3. Run: composer --version" -ForegroundColor Cyan
Write-Host "4. Then tell me 'composer ready' and I'll create Laravel!" -ForegroundColor White
Write-Host ""
