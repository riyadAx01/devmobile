# SIMPLE FINAL FIX - Windows Firewall Rule

## The Problem:
Windows Firewall is blocking the Android emulator from connecting to Apache on port 80.

## The Solution:
Add a firewall rule to allow it.

## Run This Command ONCE (as Administrator):

```powershell
New-NetFirewallRule -DisplayName "XAMPP Apache" -Direction Inbound -Action Allow -Protocol TCP -LocalPort 80
```

## How to Run:
1. Open PowerShell **AS ADMINISTRATOR** (right-click → Run as Administrator)
2. Paste the command above
3. Press Enter

## After Running:
1. In Android Studio, click **Run ▶️**
2. Your app will connect!
3. Data will appear!

## That's It!
The firewall was blocking port 80. This opens it.

Your API works. Your app is complete. Just need this firewall rule.
