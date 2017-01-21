# GitHub-Status

Dieses Modul zeigt den aktuellen Status der GitHub-Dienste an.  

## Dokumentation

**Inhaltsverzeichnis**  

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Funktionsreferenz](#4-funktionsreferenz) 
5. [Anhang](#5-anhang)
6. [Lizenz](#6-lizenz)

## 1. Funktionsumfang

 Über die JSON-API von  [Git-Hub](https://github.com) wird der aktuelle Status ermittelt und in Variablen abgebildet.  

## 2. Voraussetzungen

 - IPS 4.x
 
## 3. Installation

   - IPS 4.x  
        Über das 'Modul Control' folgende URL hinzufügen:  
        `git://github.com/Nall-chan/IPSGitHubStatus.git`  

   **Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.**  

## 4. Funktionsreferenz

```php
    boolean GH_Update( integer $InstanceID );
```
 Startet eine neue Abfrage.  

**Beispiel:**  
```php
    GH_Update(12345);
```

## 5. Anhang

**GUID's:**  
GUID des Modul (z.B. wenn Instanz per PHP angelegt werden soll):  
 `{F790E9C6-B6D5-4FF1-B521-0B65A4CDA907}`  

**Eigenschaften von GitHubStatus:**  
  keine  

**Changelog:**  
 Version 1.02:  
    - Fix: Timer in Create verschoben

 Version 1.01:  
    - Readme und Doku erweitert.  

 Version 1.0:  
    - Erstes Release  

## 6. Lizenz

  [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
