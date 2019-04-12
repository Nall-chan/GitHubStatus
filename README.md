[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.00-blue.svg)]()
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
[![Version](https://img.shields.io/badge/Symcon%20Version-5.1%20%3E-green.svg)](https://www.symcon.de/forum/threads/40060-IP-Symcon-5-1-%28Testing%29)  
[![StyleCI](https://styleci.io/repos/41359652/shield?style=flat)](https://styleci.io/repos/41359652)  

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

 - IPS 5.1 oder höher  
 
## 3. Installation

**IPS 5.1:**  
   Bei privater Nutzung: Über das 'Module-Control' in IPS folgende URL hinzufügen.  
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

**Spenden:**  
Die Library ist für die nicht kommzerielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G2SLW2MEMQZH2" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>

**GUID's:**  
GUID des Modul (z.B. wenn Instanz per PHP angelegt werden soll):  
 `{F790E9C6-B6D5-4FF1-B521-0B65A4CDA907}`  

**Eigenschaften von GitHubStatus:**  
  keine  

**Changelog:**  

 Version 2.00:  
    - Neu: Neue Statuspage von GitHub wird verwendet.  
    - Neu: Anpassungen für IPS 5.1.  

 Version 1.03:  
    - Neu: Übersetzungen für IPS 4.3.  

 Version 1.02:  
    - Fix: Timer in Create verschoben.  

 Version 1.01:  
    - Readme und Doku erweitert.  

 Version 1.0:  
    - Erstes Release  

## 6. Lizenz

  [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
