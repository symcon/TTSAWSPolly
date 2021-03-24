# TTSAWSPolly
Das Modul dient dazu Sounddaten-/dateien zu generieren, welche z.B. für Audio Notifications oder VoIP Ansagen genutzt werden können.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten des Benutzers im AWS IAM](#4-einrichten-des-benutzers-im-aws-iam)
5. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
6. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
7. [WebFront](#6-webfront)
8. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Erstellen von Sounddaten, die z.B. in einer Medien Datei gespeichert werden können für eine Ausgabe über Audio Notifications
* Erstellen von Sounddateien, die für eine Ausgabe im VoIP Modul verwendet werden können

### 2. Voraussetzungen

- IP-Symcon ab Version 5.1
- Konto bei Amazon Webservices
- Benutzer mit passendem Access Key/Secret Key und Zugriffsrechten zu Polly (z.B. AmazonPollyFullAccess)

### 3. Software-Installation

Das "Text to Speech (AWS Polly)" Modul kann direkt über den Module Store installiert werden.

### 4. Einrichten des Benutzers im AWS IAM

Auf der Amazon AWS Homepage muss bei den AWS Services nach dem IAM gesucht werden.  
![Managementkonsole][console]


Innerhalb des IAM muss ein neuer Benutzer hinzugefügt werden  
![Benutzerverwaltung][user]
![Hinzufügen des Benutzers][add]

Anschließend sollte ein sprechender Benutzername vergeben und "Programmgesteuerter Zugriff" aktiviert werden.  
![Details des Benutzers][details]

Als Richtlinie muss "AmazonPollyFullAccess" aktiviert werden.  
![Berechtigungen][permissions]

Den nächsten Dialog mit "Weiter: Prüfen" überspringen.  
Den Benutzer legt man über "Benutzer erstellen" fertig an.

Nach dem Anlegen des Benutzers wird diesem eine ZugriffsschlüsselID und geheimer Zugriffsschlüssel zugewiesen.
![Zugriffsschlüssel][keys]

### 5. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" kann das 'Text to Speech (AWS Polly)'-Modul mithilfe des Schnellfilters gefunden werden.
    - Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name           | Beschreibung
---------------| ---------------------------------
Access Key     | Access Key vom AWS Benutzer der Zugriff auf Polly hat
Secret Key     | Secret Key vom AWS Benutzer der Zugriff auf Polly hat
Region         | Region in der Polly genutzt werden soll
Sprache        | Sprache in der die Ausgabe stattfinden soll
Ausgabeformat  | Format (MP3/WAV/OGG) der Ausgabe
Abtastrate     | Abtastrate (Standard, 8000 Hz, 16000 Hz, 22050 Hz) der Ausgabe
Texttyp        | Typ vom Text. Bei SSML können die speziellen SSML Tags verwendet werden

Nach Eintragung des Access Key/Secret Key muss die Konfiguration gespeichert werden, um die verfügbaren Sprachen zu Laden.

### 6. Statusvariablen und Profile

Es werden keine Statusvariablen oder Profile angelegt.

### 7. WebFront

Über das WebFront ist keine weitere Konfiguration ode Anzeige möglich.

### 8. PHP-Befehlsreferenz

`string TTSAWSPOLLY_GenerateData(integer $InstanzID, String $Text);`
Fragt über AWS den Text an und liefert die Sprachdaten Base64-kodiert in der Rückgabe zurück.
`echo TTSAWSPOLLY_GenerateData(12345, "Dies ist ein Test");``

`string TTSAWSPOLLY_GenerateFile(integer $InstanzID, String $Text);`
Fragt über AWS den Text an und liefert den Dateinamen zu den Sprachdaten zurück.
`echo TTSAWSPOLLY_GenerateFile(12345, "Dies ist ein Test");``

[console]: ../imgs/aws-managementconsole.png
[user]: ../imgs/aws-iam-user.png
[add]: ../imgs/aws-iam-useradd.png
[details]: ../imgs/aws-iam-userdetails.png
[keys]: ../imgs/aws-iam-userkeys.png
[permissions]: ../imgs/aws-iam-userpermissions.png

