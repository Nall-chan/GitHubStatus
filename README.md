[![SDK](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Module Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FGitHubStatus%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.version&label=Modul%20Version&color=blue)]()
[![Symcon Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2FNall-chan%2FGitHubStatus%2Frefs%2Fheads%2Fmaster%2Flibrary.json&query=%24.compatibility.version&suffix=%3E&label=Symcon%20Version&color=green)](https://www.symcon.de/de/service/dokumentation/installation/migrationen/v80-v81-q3-2025/)  
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Check Style](https://github.com/Nall-chan/GitHubStatus/workflows/Check%20Style/badge.svg)](https://github.com/Nall-chan/GitHubStatus/actions)
[![Run Tests](https://github.com/Nall-chan/GitHubStatus/workflows/Run%20Tests/badge.svg)](https://github.com/Nall-chan/GitHubStatus/actions)  
[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](#7-spenden)
[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](#7-spenden)  

# GitHub-Status <!-- omit in toc -->  

Dieses Modul zeigt den aktuellen Status der GitHub-Dienste an.  

## Dokumentation <!-- omit in toc -->

**Inhaltsverzeichnis**  

- [1. Funktionsumfang](#1-funktionsumfang)
- [2. Voraussetzungen](#2-voraussetzungen)
- [3. Installation](#3-installation)
- [5. Einrichten der Instanzen in IPS](#5-einrichten-der-instanzen-in-ips)
- [6. Statusvariablen](#6-statusvariablen)
- [7. Spenden](#7-spenden)
- [8. Changelog](#8-changelog)
- [9. Lizenz](#9-lizenz)

## 1. Funktionsumfang

 Über die JSON-API von  [GitHub](https://github.com) wird der aktuelle Status ermittelt und in Variablen abgebildet.  

## 2. Voraussetzungen
  
 - IP-Symcon ab Version 8.1
 
## 3. Installation

   Bei privater Nutzung: Über den `Module-Store` in IPS das Modul `GitHub Status` hinzufügen.  

   **Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.**  

## 5. Einrichten der Instanzen in IPS

   Unter Instanz hinzufügen ist die Instanz `GitHubStatus` unter dem Hersteller `GitHub` aufgeführt.  

## 6. Statusvariablen 

   ![Logischer Baum](imgs/vars.png)  
   ![Kachel Visu](imgs/tile.png)  
   ![WebFront](imgs/wf.png)  

   Die Statusvariablen werden automatisch angelegt.

## 7. Spenden  

  Die Library ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

[![PayPal.Me](https://img.shields.io/badge/PayPal-Me-lightblue.svg)](https://paypal.me/Nall4chan)  

[![Wunschliste](https://img.shields.io/badge/Wunschliste-Amazon-ff69fb.svg)](https://www.amazon.de/hz/wishlist/ls/YU4AI9AQT9F?ref_=wl_share)

## 8. Changelog  

Version 3.10:  
    - Version für IPS 8.1.  

Version 3.01:  
    - Version für IPS 7.0.  

Version 3.00:  
    - Statusmeldungen der Dienste erweitert.  

Version 2.10:  
    - Release für IPS 5.1 und den Module-Store .  

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

## 9. Lizenz

  [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
