# GitHub-Status

Dieses Modul zeigt den letzten Status der GitHub-Dienste an.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Funktionsreferenz](#4-funktionsreferenz) 
5. [Anhang](#5-anhang)

## 1. Funktionsumfang

 Über die JSON-API von  [Git-Hub](https://github.com) wird der aktuelle Status ermittelt und in Variablen abgebildet.  

## 2. Voraussetzungen

 - IPS 4.x
 
## 3. Installation

   - IPS 4.x  
        Über das 'Modul Control' folgende URL hinzufügen:  
        `git://github.com/Nall-chan/IPSGitHubStatus.git`  


## 4. Funktionsreferenz

```php
GH_Update( interger $InstanceID );
```
 Startet eine neue Abfrage.  

## 5. Anhang

**GUID's:**  
 `{F790E9C6-B6D5-4FF1-B521-0B65A4CDA907}`

**Changelog:**  
 Version 1.0:
  - Erstes Release
