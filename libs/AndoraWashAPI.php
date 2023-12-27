<?php

declare(strict_types=1);

trait AndoraWashAPI {
    private function request(string $ip, string $endpoint, string $command, bool $jsonDecode = false, string $params = '') {
        $isSimulated = $this->ReadPropertyBoolean('Simulated');
        if ($isSimulated) {
            return $this->simulateRequest($endpoint, $command, $jsonDecode, $params);
        }
        $milliseconds = floor(microtime(true) * 1000);
        $response = file_get_contents("http://$ip/$endpoint?command=$command$params&_=$milliseconds");
        if ($jsonDecode) {
            return json_decode($response);
        }
    }

    public function getSupportedLanguages(string $ip): array {
        return $this->request($ip, 'hh', 'getSupportedLanguages', true);
    }

    public function getLanguage(string $ip): string {
        return $this->request($ip, 'hh', 'getLanguage');
    }

    public function getTranslations(string $ip, string $lang): object {
        return $this->request($ip, 'ai', 'getProperty', true, "&value=$lang");
    }

    public function getZHMode(string $ip): object {
        return $this->request($ip, 'hh', 'getZHMode', true);
    }

    public function getModelDescription(string $ip): string {
        return $this->request($ip, 'ai', 'getModelDescription');
    }

    public function getDeviceStatus(string $ip): object | null {
        return $this->request($ip, 'ai', 'getDeviceStatus', true);
    }

    public function getLastPUSHNotifications(string $ip): array {
        return $this->request($ip, 'ai', 'getLastPUSHNotifications', true);
    }

    private function simulateRequest(string $endpoint, string $command, bool $jsonDecode, string $params) {
        $data = '';
        switch ($command) {
            case 'getSupportedLanguages':
                $data = '[{"lan":"de","desc":"Deutsch"},{"lan":"fr","desc":"Français"},{"lan":"it","desc":"Italiano"},{"lan":"rm","desc":"Rumantsch"},{"lan":"en","desc":"English"},{"lan":"es","desc":"Español"},{"lan":"ru","desc":"Русский"},{"lan":"tr","desc":"Türkçe"},{"lan":"zh","desc":"中文"},{"lan":"ca","desc":"Català"},{"lan":"da","desc":"Dansk"},{"lan":"nl","desc":"Nederlands"},{"lan":"nb","desc":"Norsk"},{"lan":"sv","desc":"Svenska"},{"lan":"vi","desc":"T.Việt"},{"lan":"th","desc":"ภาษาไท"}]';
                break;
            case 'getLanguage':
                $data = 'de';
                break;
            case 'getTranslations':
                $data = '{
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
                            "dateAndTime": "Datum \u0026 Zeiteinstellungen",
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
                        ';
                break;
            case 'getZHMode':
                $data = '{"value":2}';
                break;
            case 'getModelDescription':
                $data = 'AndoraWash V2000';
                break;
            case 'getDeviceStatus':
                $data = '{"DeviceName":"AdoraWash V2000","Serial":"11041 005016","Inactive":"false","Program":"30°C Buntwäsche","Status":"Hauptwaschen\nEnde um 12:51","ProgramEnd":{"End":"1h07","EndType":"2"},"deviceUuid":"1156000"}';
                break;
            case 'getLastPUSHNotifications':
                $data = '[{"date":"2023-07-30T12:55:00Z","message":"Programm 60°C Buntwäsche beendet."}
                ,{"date":"2023-07-26T15:24:59Z","message":"Programm 60°C Buntwäsche beendet."}
                ,{"date":"2023-07-26T13:24:37Z","message":"Programm 30°C Buntwäsche beendet."}]';
                break;
        }
        return ($jsonDecode ? json_decode($data) : $data);
    }
}
