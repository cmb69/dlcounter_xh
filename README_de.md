# Dlcounter_XH

Dlcounter_XH ist ein einfacher Downloadzähler für CMSimple_XH. Anstelle
eines Links zur herunter zu ladenden Datei wird der Download als
HTML-Formular angeboten, das von Bots ignoriert werden sollte, so dass der
Downloadzähler etwas akkurater sein sollte. Bitte beachten Sie, dass absolut
akkurate Downloadzahlen mit Dlcounter_XH nicht erzielt werden können (und
vermutlich auch nicht mit anderen Downloadzählern), da beispielsweise
abgebrochene Downloads oder mehrfache Downloads, die von Download-Managern
angestoßen werden, nicht besonders berücksichtigt werden.

Download-Statistiken können im Plugin-Backend eingesehen werden.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Dlcounter_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.
Dlcounter_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.2;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/dlcounter_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

The Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie das herunter geladene Archiv auf Ihrem Computer.
1. Laden Sie das komplette Verzeichnis `dlcounter/` auf Ihren Server in
   das CMSimple_XH-Pluginverzeichnis hoch.
1. Machen Sie die Unterverzeichnisse `config/`, `css/`
   und `languages/` beschreibbar.
1. Browsen Sie zu `Plugins` → `Dlcounter` im Administrationsbereich,
   um zu prüfen, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration wird wie bei vielen anderen CMSimple_XH-Plugins
auch im Administrationsbereich durchgeführt.
Gehen Sie zu `Plugins` → `Dlcounter`.

Sie können die Voreinstellungen von Dlcounter_XH unter `Konfiguration` ändern.
Hinweise zu den Optionen werden angezeigt, wenn Sie die Hilfe-Icons
mit der Maus überfahren.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können dort die
Sprachtexte in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder diese gemäß Ihren Wünschen anpassen.

Das Aussehen von Dlcounter_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Alle Downloads, die gezählt werden sollen, müssen direkt im konfigurierten
Downloadordner (siehe `Konfiguration` → `Folder` → `Downloads`) gespeichert werden.
Um das Downloadformular für die Datei `download.pdf` auf einer Seite anzuzeigen,
geben Sie folgendes ein:

    {{{dlcounter('download.pdf')}}}

Sie können unbesorgt prüfen, ob der Download wie gewünscht funktioniert,
indem Sie den Download auslösen während Sie als Administration angemeldet
sind; diese Downloads werden nicht gezählt.

Um das direkte herunterladen der Dateien zu verhindern (jemand könnte die
URL einer Datei erraten), müssen sie den konfigurierten Downloadordner auf
eine Weise schützen, die Ihr Server unterstützt (für Apache Server können
sie normalerweise eine Kopie von `cmsimple/.htaccess` verwenden).

Um die Download-Statistiken zu sehen, navigieren Sie zu
`Plugins` → `Dlcounter` → `Statistiken`.
Die Tabellen können durch Klick auf die entsprechende Spaltenüberschrift sortiert werden.

## Einschränkungen

Ist die Fileinfo PHP-Extension nicht verfügbar, werden die Downloads mit dem
generischen MIME-Typen `application/octet-stream` ausgeliefert.
Das *kann*  in manchen Browser zu unvollkommenem Verhalten führen, ist
aber kein Grund zur Besorgnis.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/dlcounter_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Dlcounter_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Dlcounter_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Dlcounter_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

Estnische Übersetzung © Alo Tanavots<br>
Russische Übersetzung © Lybomyr Kydray<br>
Slovakische Übersetzung © Dr. Martin Sereday

## Danksagung

Dlcounter_XH verwendet das [jQuery Tablesorter Plugin](https://github.com/christianbach/tablesorter).
Vielen Dank an Christian Bach für die Veröffentlichung unter GPL.

Das Plugin-Logo wurde von [YellowIcon](http://yellowicon.com/) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Vielen Dank an die Gemeinschaft im [CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
für Tipps, Anregungen und das Testen.
Besonderer Dank an *frase* für das zur Verfügung stellen eines überholten
Backend Stylesheets.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
