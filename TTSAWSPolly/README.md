# TTSAWSPolly
Das Modul dient dazu Sounddaten-/dateien zu generieren, welche z.B. für Audio Notifications oder VoIP Ansagen genutzt werden können.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Erstellen von Sounddaten, die z.B. in einer Medien Datei gespeichert werden können für eine Ausgabe über Audio Notifications
* Erstellen von Sounddateien, die für eine Ausgabe im VoIP Modul verwendet werden können

### 2. Voraussetzungen

- IP-Symcon ab Version 5.1
- Konto bei Amazon Webservices
- Benutzer mit passendem Access Key/Secret Key und Zugriffsrechten zu Polly (z.B. AmazonPollyFullAccess)

### 3. Software-Installation

Das "Text to Speech (AWS Polly)" Modul kann direkt über den Module Store installiert werden.

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" ist das 'Text to Speech (AWS Polly)'-Modul unter dem Hersteller '(Sonstige)' aufgeführt.  

__Konfigurationsseite__:

Name           | Beschreibung
---------------| ---------------------------------
Access Key     | Access Key vom AWS Benutzer der Zugriff auf Polly hat
Secret Key     | Secret Key vom AWS Benutzer der Zugriff auf Polly hat
Region         | Region in der Polly genutzt werden soll
Sprache        | Sprache in der die Ausgabe stattfinden soll
Ausgabeformat  | Format (MP3/WAV/OGG) der Ausgabe
Abtastrate     | Abtastrate (Standard, 8000 Hz, 16000 Hz, 22050 Hz) der Ausgabe

Nach Eintragung des Access Key/Secret Key muss die Konfiguration gespeichert werden, um die verfügbaren Sprachen zu Laden.

### 5. Statusvariablen und Profile

Es werden keine Statusvariablen oder Profile angelegt.

### 6. WebFront

Über das WebFront ist keine weitere Konfiguration ode Anzeige möglich.

### 7. PHP-Befehlsreferenz

`string TTSAWSPOLLY_GenerateData(integer $InstanzID, String $Text);`
Fragt über AWS den Text an und liefert die Sprachdaten in der Rückgabe zuürck.
`echo TTSAWSPOLLY_GenerateData(12345, "Dies ist ein Test");``

`string TTSAWSPOLLY_GenerateFile(integer $InstanzID, String $Text);`
Fragt über AWS den Text an und liefert den Dateinamen zu den Sprachdaten zurück.
`echo TTSAWSPOLLY_GenerateFile(12345, "Dies ist ein Test");``