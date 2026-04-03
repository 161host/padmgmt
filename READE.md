# CryptPad / Authentik Middleware

Eine Middleware die zwischen CryptPad & Authentik sitzt und die User Verwaltung, sowie selbstverwaltung von Gruppen ermöglicht.

[![AGPLv3 License](https://img.shields.io/badge/license-AGPL-blue.svg)](https://choosealicense.com/licenses/agpl-3.0/)

## Warum?
Wir brauchten für https://pad.1312.rocks eine Möglichkeit, das Gruppen selbst Einladungen generieren können. Da CryptPad selbst keine Möglichkeit bietet einfach Gruppen zu ermöglichen sich selbst Einladungen zu generieren, haben wir diese Middleware entwickelt.

## Wie funktioniert das?
Die Middleware bietet zwei Webinterfaces, eines für die Gruppenverwaltung (wwwmgmt) und eines für die Registrierung (wwwsignup).

### Gruppenverwaltung (wwwmgmt)
Hier können Mitglieder von Gruppen mit einem internen Authentik Account Einladungslinks generieren, die sie dann an andere weitergeben können. Es gibt zwei Arten von Einladungen:
- Einladungen für einzelne Nutzer (single): Diese Einladungen können nur einmal verwendet werden und laufen nach maximal 7 Tagen ab.
- Einladungen für Gruppen (multi): Diese Einladungen können mehrfach verwendet werden, bis sie auslaufen (maximal 2 Tage).

### Registrierung (wwwsignup)
Hier können Nutzer*innen sich für einen Account anmelden.
Nachdem die User ihre E-Mail Adresse bestätigt haben, wird via Telegram eine Benachrichtigung in einen Chat geschickt, mit einem Link zum Verwalten der Anfrage.
Die Anfrage kann angenommen oder abgelehnt werden. Je nach dem ob eine Anfrage für eine Gruppe oder für eine Einzelperson generiert wird, wird dem User entweder eine einzelne Einladung als "External" oder eine Einladung als "Internal" generiert. Bei der Einladung als "Internal" hat der User danach selber die Möglichkeit wwwmgmt zu verwenden.

## Installation
### Requirements:
- Docker & Docker Compose

### Steps
1. **Installiere Authentik & CryptPad**
2. Konfiguriere Authentik mit den entsprechenden Flows und Gruppen
3. Generiere einen API Key für einen Admin User in Authentik
4. Die `compose.yml` & `.env.example` Datei herunterladen.
5. Kopiere die `.env.example` Datei in `.env` und passe die Werte entsprechend an.
6. `mkdir signupdata`
7. `chown 33:33 signupdata`
8.  `docker compose up -d`
9.  Binde die Webinterfaces in deinem Reverse Proxy ein:
    - Gruppenverwaltung: `http://localhost:9123`
    - Registrierung: `http://localhost:9124`

### Flows
Falls du keine Enrollment Flows in Authentik hast sind hier 2 Beispiel Flows für internal & external enrollment:
- [External Enrollment Flow](./examples/external-enrollment-flow.yaml)
- [Internal Enrollment Flow](./examples/internal-enrollment-flow.yaml)


### Hinweise:

Wir nutzen 2 unterschiedliche Admin User & 2 unterschiedliche API Keys für die Anwendungen, um unterscheiden zu können woher die Einladungen kommen. Es ist aber ohne Probleme möglich den API Key bei beiden Anwendungen einzusetzen.


## Lizenz & andere Tools:
AGPL-3.0 License: https://choosealicense.com/licenses/agpl-3.0/
tailwind.css: https://tailwindcss.com
qrcode.min.js: https://github.com/davidshimjs/qrcodejs

**Proudly made without AI tools and with ❤️ by 161host.NET🏳️‍🌈**

# See it live:
https://signup.pad.1312.rocks