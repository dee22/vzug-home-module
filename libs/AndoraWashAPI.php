<?php

declare(strict_types=1);

trait AndoraWashAPI {
    private function request(string $ip, string $endpoint, string $command, array $params = []) {
        $isSimulated = $this->ReadPropertyBoolean('Simulated');
        if ($isSimulated) {
            return $this->simulateRequest($endpoint, $command, $params);
        }
        $milliseconds = floor(microtime(true) * 1000);
        $parameters = implode('&', array_merge(["command=$command"], $params, ["_=$milliseconds"]));
        $response = file_get_contents("http://$ip/$endpoint?$parameters");
        $result = json_decode($response);
        if (gettype($result) === 'NULL') {
            return $response;
        }
        return $result;
    }

    // START GENERATED CODE
    public function getSupportedLanguages(string $ip): array {
        return $this->request($ip, 'hh', 'getSupportedLanguages');
    }

    public function getLanguage(string $ip): string {
        return $this->request($ip, 'hh', 'getLanguage');
    }

    public function getProperty(string $ip, string $value = 'de'): object {
        return $this->request($ip, 'ai', 'getProperty', ["value=$value"]);
    }

    public function getTimeSettings(string $ip): object {
        return $this->request($ip, 'hh', 'getTimeSettings');
    }

    public function getTime(string $ip): string {
        return $this->request($ip, 'hh', 'getTime');
    }

    public function getCategories(string $ip): array {
        return $this->request($ip, 'hh', 'getCategories');
    }

    public function getCategory(string $ip, string $value = 'settings'): object {
        return $this->request($ip, 'hh', 'getCategory', ["value=$value"]);
    }

    public function getCommands(string $ip, string $value = 'settings'): array {
        return $this->request($ip, 'hh', 'getCommands', ["value=$value"]);
    }

    public function getCommand(string $ip, string $value = 'buttonXtone'): object {
        return $this->request($ip, 'hh', 'getCommand', ["value=$value"]);
    }

    public function isDigestAuthenticationEnabled(string $ip): bool {
        return $this->request($ip, 'ai', 'isDigestAuthenticationEnabled');
    }

    public function getUsername(string $ip): string {
        return $this->request($ip, 'ai', 'getUsername');
    }

    public function getModelDescription(string $ip): string {
        return $this->request($ip, 'ai', 'getModelDescription');
    }

    public function getAddressConfiguration(string $ip): string {
        return $this->request($ip, 'ai', 'getAddressConfiguration');
    }

    public function getMacAddress(string $ip): string {
        return $this->request($ip, 'ai', 'getMacAddress');
    }

    public function getIP(string $ip): string {
        return $this->request($ip, 'ai', 'getIP');
    }

    public function getNetmask(string $ip): string {
        return $this->request($ip, 'ai', 'getNetmask');
    }

    public function getGateway(string $ip): string {
        return $this->request($ip, 'ai', 'getGateway');
    }

    public function getDNS1(string $ip): string {
        return $this->request($ip, 'ai', 'getDNS1');
    }

    public function getDNS2(string $ip): string {
        return $this->request($ip, 'ai', 'getDNS2');
    }

    public function getModel(string $ip): string {
        return $this->request($ip, 'hh', 'getModel');
    }

    public function getSerialNumber(string $ip): string {
        return $this->request($ip, 'ai', 'getSerialNumber');
    }

    public function getFWVersionHome(string $ip): object {
        return $this->request($ip, 'hh', 'getFWVersion');
    }

    public function getFWVersion(string $ip): object {
        return $this->request($ip, 'ai', 'getFWVersion');
    }

    public function getUpdateStatus(string $ip): object {
        return $this->request($ip, 'ai', 'getUpdateStatus');
    }

    public function getZHMode(string $ip): object {
        return $this->request($ip, 'hh', 'getZHMode');
    }

    public function getDeviceStatus(string $ip): object {
        return $this->request($ip, 'ai', 'getDeviceStatus');
    }

    public function getLastPUSHNotifications(string $ip): array {
        return $this->request($ip, 'ai', 'getLastPUSHNotifications');
    }
    // END GENERATED CODE

    public function getCommandValues(string $ip, string $category = 'settings'): array {
        $commandNames = $this->getCommands($ip, $category);
        $commands = [];
        foreach ($commandNames as $commandName) {
            $commands[] = $this->getCommand($ip, $commandName);
        }
        return $commands;
    }

    private function simulateRequest(string $endpoint, string $command, array $params) {
        $simData = array(
            'hh' =>
            array(
                'getSupportedLanguages' => '[{"lan":"de","desc":"Deutsch"},{"lan":"fr","desc":"Français"},{"lan":"it","desc":"Italiano"},{"lan":"rm","desc":"Rumantsch"},{"lan":"en","desc":"English"},{"lan":"es","desc":"Español"},{"lan":"ru","desc":"Русский"},{"lan":"tr","desc":"Türkçe"},{"lan":"zh","desc":"中文"},{"lan":"ca","desc":"Català"},{"lan":"da","desc":"Dansk"},{"lan":"nl","desc":"Nederlands"},{"lan":"nb","desc":"Norsk"},{"lan":"sv","desc":"Svenska"},{"lan":"vi","desc":"T.Việt"},{"lan":"th","desc":"ภาษาไท"}]',
                'getLanguage' => 'de',
                'getTimeSettings' => '{"timeSyncEnabled":true,"autoSummerWinter":true,"location":"Europe/Zurich","UTCTime":"2023-08-01T10:08:34Z","localTime":"2023-08-01T12:08:34+02:00","offset":7200}',
                'getTime' => '2023-08-01T12:08:34',
                'getCategories' => '["settings"]',
                'getCategory' => '{"description":"Einstellungen"}',
                'getCommands' => '["buttonXtone","backgroundXimage","brightness","spinningXat","soiling","aquaplus","autoXdoorXopener","startXdelay","hygieneXinformation","childproofXlock","trommellicht"]',
                'getCommand' => '{"type":"boolean","description":"Tastenton","command":"buttonXtone","value":"true"}',
                'getModel' => 'AW2T',
                'getFWVersion' => '{"fn":"11041 005016","an":"1104100001","v":"1032589-R17","vr01":"1032596-R04","v2":"1032591-R06","vr10":"1032592-R04","vi2":"1039144-R01","vh1":"1077219-R08","vh2":"1037283-R07","vr0B":"1069071-R05","vp":"1064277-R21","vr0C":"1069072-R01","vr0E":"1032590-R03","Mh":"1016761-R04","MD":"1016096-R04","Zh":"???????-???","ZV":"???????-???","ZHSW":"1052633-R16","device-type":"KUNDE"}',
                'getZHMode' => '{"value":2}',
            ),
            'ai' =>
            array(
                'getProperty' => '{
              "lang": "de",
              "language": "Deutsch",
              "overview": "Übersicht",
              "networkSettings": "Netzwerkeinstellungen",
              "userSettings": "Benutzereinstellungen",
              "serviceSettings": "Service",
              "general": "Allgemein",
              "model": "Modell",
              "serialNumber": "SN/FN",
              "program": "Programm",
              "state": "Status",
              "time": "Gerätezeit",
              "endTime": "Ende um",
              "timeLeft": "Ende in",
              "endsOn": "Ende wenn",
              "securitySettings": "Sicherheitseinstellungen",
              "turnOffMachine": "Gerät ausschalten",
              "turnOff": "Ausschalten",
              "passwordProtection": "Passwort-Schutz",
              "passwordHint": "Mit diesem Passwort schützen Sie Ihr Gerät davor, dass andere Geräte aus Ihrem Heimnetzwerk ohne Ihre Erlaubnis auf Ihr Gerät zugreifen können. Wenn Sie also Ihr Gerät lokal in Ihr Smart Home integrieren oder mit einem Webbrowser auf die Einstellungen Ihres Geräts zugreifen möchten, wird das Passwort abgefragt. Falls Sie das Passwort vergessen haben, können Sie dieses jederzeit mit den V-ZUG-Home Werkseinstellungen am Gerät zurücksetzen. Das Passwort gilt nur in Ihrem lokalen Heimnetzwerk. Der Fernzugriff aus dem Internet ist über Ihr V-ZUG Benutzerkonto gesichert.",
              "username": "Benutzername",
              "oldPassword": "Aktuelles Passwort",
              "password": "Neues Passwort",
              "confirmPassword": "Neues Passwort bestätigen",
              "notifications": "Push-Benachrichtigungen",
              "lastNotifications": "Letzte Ereignisse",
              "minutes": "Minuten",
              "finished": "Fertig!",
              "networkInterface": "Netzwerkinterface",
              "macAddress": "MAC-Adresse",
              "currentIP": "Aktuelle IP-Adresse",
              "networkConfiguration": "Netzwerkkonfiguration",
              "ipAddressAssignment": "Adressvergabe",
              "ipAddress": "IP-Adresse",
              "netmask": "Netzwerkmaske",
              "defaultGateway": "Default Gateway",
              "primaryDNSServer": "Primärer DNS-Server",
              "secondaryDNSServer": "Sekundärer DNS-Server",
              "dateAndTime": "Datum \\u0026 Zeiteinstellungen",
              "automaticSynchronization": "Automatische Synchronisation (NTP)",
              "timeServerSelection": "Zeitserver auswählen",
              "timeZoneSelection": "Zeitzone auswählen",
              "automaticSummerTime": "Sommer-/Winterzeitumstellung",
              "manualTimeConfiguration": "Datum und Zeit manuell konfigurieren",
              "validIpRequired": "Bitte eine gültige IP-Adresse eingeben.",
              "validNetmaskRequired": "Bitte eine gültige Netzwerkmaske eingeben.",
              "validDefaultGWRequired": "Bitte eine gültige IP-Adresse für den Default Gateway eingeben.",
              "validPrimaryDNSServerRequired": "Bitte eine gültige IP-Adresse für den primären DNS-Server angeben.",
              "validSecondaryDNSServerRequired": "Bitte eine gültige IP-Adresse für den sekundären DNS-Server angeben oder leer lassen.",
              "deviceIsRestartingPleaseWait": "Das Netzwerkinterface startet neu - bitte Seite in einer Minute neu laden.",
              "usernameRequired": "Bitte einen Benutzernamen eingeben.",
              "validUsernameRequired": "Benutzername darf nur alphanumerische Zeichen enthalten.",
              "validPasswordRequired": "Passwort muss mindestens 10 Zeichen enthalten.",
              "confirmPasswordWrong": "Passwörter sind nicht identisch.",
              "oldPasswordRequired": "Bitte aktuelles Passwort angeben.",
              "securitySettingsConfigured": "Sicherheitseinstellungen erfolgreich übernommen.",
              "oldPasswordIncorrect": "Das angegebene Passwort ist falsch.",
              "firmwareUpdate": "Software-Update",
              "fwAIDescription": "V-ZUG-Home",
              "swVersion": "Aktuelle Software Version",
              "hwVersion": "Aktuelle Hardware Version",
              "updateAvailable": "Es ist ein Software-Update verfügbar!",
              "softwareIsUpToDate": "Die Software ist auf dem neusten Stand.",
              "configure": "Konfigurieren",
              "configError": "Konfigurationsfehler",
              "configOk": "Konfiguration erfolgt",
              "hhgAccessError": "Gerätefehler",
              "invalidInput": "Fehlerhafte Eingabe",
              "validRange": "Gültiger Wertebereich",
              "enterValidValue": "Bitte gültigen Wert eingeben!",
              "changeSettingForbidden": "Benutzereinstellungen können nur verändert werden, wenn das Gerät nicht in Betrieb ist.",
              "dhcp": "DHCP",
              "staticIPConfig": "Statische IP-Adresse",
              "machineTurnOffSucessfully": "Gerät erfolgreich ausgeschaltet.",
              "machineTurnOffError": "Achtung: Gerät konnte nicht augeschaltet werden.",
              "update": "Update",
              "info": "Information",
              "waitForAIUpdate": "Bitte warten. Das V-ZUG-Home Interface wird aktualisiert. Dieser Vorgang kann bis zu 10 Minuten dauern. Nach dem Update werden Sie wieder mit dem Gerät verbunden.",
              "aiUpdateFailed": "Das V-ZUG-Home Interface konnte nicht wieder gefunden werden. Bitte suchen Sie das Gerät erneut mit der Desktop oder mobilen APP.",
              "waitForHHGUpdate": "Bitte warten. Die Gerätesoftware wird aktualisiert. Dieser Vorgang kann bis zu 20 Minuten dauern. Nach dem Update werden sie wieder mit dem Gerät verbunden.",
              "hhgUpdateFailed": "Die Verbindung zum Gerät konnte nicht wieder hergestellt werden. Bitte suchen Sie das Gerät erneut mit der Desktop oder mobilen APP.",
              "contact": "Kontakt",
              "canNotUpdateActiveMachine": "Das Gerät kann momentan nicht aktualisiert werden, weil es noch aktiv ist. Bitte versuchen Sie es später erneut.",
              "contact_link": "Kontakt",
              "licenses_link": "Lizenzbedingungen",
              "privacy_link": "Datenschutz",
              "updateSuccess": "Update erfolgreich",
              "updateFailed": "Update fehlgeschlagen – bitte erneut versuchen!",
              "checkUpdate": "Nach Updates suchen",
              "updateFinished": "Update beendet",
              "checkingUpdateAvailable": "Updates werden gesucht...",
              "updateStarted": "Update gestartet",
              "downloadingUpdate": "Update wird geladen...",
              "installingUpdate": "Update wird installiert...",
              "wrongZugHomeMode": "Dieser Befehl kann aufgrund der aktuellen Geräteeinstellungen nicht ausgeführt werden.",
              "currentlyNotAllowed": "Dieser Befehl kann momentan nicht ausgeführt werden.",
              "notAllowedWithProgramSelected": "Dieser Befehl ist nicht erlaubt, wenn ein Programm vorgewählt ist.",
              "notAllowedWithProgramDelayedStart": "Dieser Befehl ist nicht erlaubt, wenn am Gerät ein Startaufschub eingestellt ist.",
              "notAllowedWithProgramActive": "Dieser Befehl ist nicht erlaubt, wenn das Gerät in Betrieb ist.",
              "authenticationError": "Authentisierungsfehler: Bitte laden Sie die Seite neu und loggen sich ein.",
              "commandNotFound": "Dieser Befehl ist für dieses Gerät nicht verfügbar."
          }
          ',
                'isDigestAuthenticationEnabled' => 'false',
                'getUsername' => 'username',
                'getModelDescription' => 'AdoraWash V2000',
                'getAddressConfiguration' => 'DHCP',
                'getMacAddress' => 'FC:1B:FF:11:A3:A0',
                'getIP' => '192.168.0.102',
                'getNetmask' => '255.255.255.0',
                'getGateway' => '192.168.0.1',
                'getDNS1' => '62.2.17.61',
                'getDNS2' => '62.2.24.158',
                'getSerialNumber' => '11041 005016',
                'getFWVersion' => '{"fn":"11041 005016","SW":"1052633-R16","SD":"1052633-R16","HW":"1077219-R08","apiVersion":"1.7.0","phy":"WLAN","deviceUuid":"1156000"}',
                'getUpdateStatus' => '{"status":"idle","isAIUpdateAvailable":false,"isHHGUpdateAvailable":true,"isSynced":true,"components":[{"name":"AI","running":false,"available":false,"required":false,"progress":{"download":0,"installation":0}},{"name":"HHG","running":false,"available":true,"required":false,"progress":{"download":0,"installation":0}}]}',
                'getDeviceStatus' => '{"DeviceName":"AdoraWash V2000","Serial":"11041 005016","Inactive":"false","Program":"30°C Buntwäsche","Status":"Hauptwaschen\\nEnde um 12:51","ProgramEnd":{"End":"1h07","EndType":"2"},"deviceUuid":"1156000"}',
                'getLastPUSHNotifications' => '[{"date":"2023-07-30T12:55:00Z","message":"Programm 60°C Buntwäsche beendet."}
          ,{"date":"2023-07-26T15:24:59Z","message":"Programm 60°C Buntwäsche beendet."}
          ,{"date":"2023-07-26T13:24:37Z","message":"Programm 30°C Buntwäsche beendet."}]',
            ),
        );
        if (isset($simData[$endpoint]) && isset($simData[$endpoint][$command])) {
            return $simData[$endpoint][$command];
        }
        return null;
    }
}
