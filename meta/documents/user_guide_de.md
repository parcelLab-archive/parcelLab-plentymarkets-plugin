<div class="alert alert-warning" role="alert">
   <strong><i>Hinweis:</strong></i> Das parcelLab-Plugin ist für die Nutzung mit dem Webshop Ceres entwickelt und funktioniert nur mit dessen Logikstruktur oder anderen Template-Plugins.
</div>

# parcelLab – Eigene Versandnachrichten für Ihren Shop

Mit individuellen Versandnachrichten machen Sie Ihren Versand zum Kundenerlebnis und schaffen ganz nebenbei wertvolle Kontaktpunkte, die sonst an DHL, Hermes & Co verloren gehen.

## Kontaktaufnahme

Bevor Sie die Versandnachrichten in plentymarkets einrichten können, ist die [Registrierung bei parcelLab](https://parcellab.com/) erforderlich. Sie erhalten dann Informationen sowie Zugangsdaten, die Sie für die Einrichtung benötigen.

## Plugin-Installation

Bevor das Modul verwendet werden kann, muss dieses in plentymarkets installiert werden.

**Installation des parcelLab-Plugins via plentyMarketplace:**

1. [plentyMarketplace](https://marketplace.plentymarkets.com/) im Browser aufrufen
2. Finden Sie das Plugin unter **Integration** → **ParcelLab**
3. **Go to checkout** (Login) und den Einkauf bestätigen
4. Backend vom Shop aufrufen
5. Menü **Plugins » Purchases** öffnen
6. Schaltfläche **Install** für das parcelLab-Plugin betätigen

**Installation des parcelLab-Plugins via GIT:**

1. Menü **Plugins » Git** öffnen
2. **New Plugin** auswählen. Es öffnet sich das Fenster **Settings**.
3. Verbinden Sie Ihren GitHub-Zugang und tragen Sie **User Name** und **Password** ein
4. Tragen Sie die Remote-URL des parcelLab-Plugins ein: <https://github.com/parcelLab/parcelLab-plentymarkets-plugin.git>
5. Aktivieren Sie **Auto fetch**
6. Abschließend speichern mit **Save**

## parcelLab in plentymarkets einrichten

Bevor Sie die Funktionen des parcelLab-Plugins nutzen können, müssen Sie zuerst Ihr parcelLab-Konto mit Ihrem plentymarkets System verbinden.

##### parcelLab-Konto anbinden:

1. Öffnen Sie das Menü **Plugins » Übersicht**.
2. Klicken Sie auf das Plugin **ParcelLab** und wählen dann **Konfiguration**.
3. Tragen Sie in die Felder _parcelLab ID_ und _parcelLab Token_ die jeweiligen Werte ein.<br />
    → Diese Informationen erhalten Sie nach der Kontaktaufnahme zu parcelLab.
4. Speichern Sie die Einstellungen ab.

<table>
  <caption>Tab. 1: parcelLab-Plugineinstellungen / Grundeinstellungen</caption>
  <thead>
    <th>
      Einstellung
    </th>
    <th>
      Erläuterung
    </th>
  </thead>
  <tbody>
    <tr>
      <td>
        <b>parcelLab ID</b>
      </td>
      <td>
      	 Benutzer für Authentifizierung
      </td>
    </tr>
    <tr>
      <td>
        <b>parcelLab Token</b>
      </td>
      <td>
      	 Token für Authentifizierung
      </td>
    </tr>
  </tbody>
</table>

## Integration: Versandnachrichten

Richten Sie eine Ereignisaktion ein, um die Erstellung der Versandnachrichten zu automatisieren. Hinweis: ohne eine Paketnummer im Auftrag wird die Versandnachricht abgelehnt.

##### Ereignisaktion einrichten:

1. Öffnen Sie das Menü **Einstellungen » Aufträge » Ereignisaktionen**.
2. Klicken Sie auf **Ereignisaktion hinzufügen**.<br />
    → Das Fenster **Neue Ereignisaktion erstellen** wird geöffnet.
3. Geben Sie einen Namen ein.
4. Wählen Sie das Ereignis gemäß Tabelle 2.
5. **Speichern** Sie die Einstellungen.
6. Nehmen Sie die Einstellungen gemäß Tabelle 2 vor.
7. Setzen Sie ein Häkchen bei **Aktiv**.
8. **Speichern** Sie die Einstellungen.

<table>
  <thead>
    <th>
      Einstellung
    </th>
    <th>
      Option
    </th>
    <th>
      Auswahl
    </th>
  </thead>
  <tbody>
    <tr>
      <td><strong>Ereignis</strong></td>
      <td>Auftragsänderung > Warenausgang gebucht</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><strong>Filter 1</strong></td>
      <td>Auftrag > Auftragstyp</td>
      <td>Auftrag</td>
    </tr>
    <tr>
      <td><strong>Aktion</strong></td>
      <td>Plugin > parcelLab | Trackings erstellen</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
  <caption>
    Tab. 2: Ereignisaktion zur Erstellung von Versandnachrichten
  </caption>
</table>

---------------------------------------

Weitere Informationen zu allen Themen stehen Ihnen über die [parcelLab Docs](https://docs.parcellab.com/) zur Verfügung.
