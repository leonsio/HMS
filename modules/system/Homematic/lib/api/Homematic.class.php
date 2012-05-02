<?php
namespace modules\system\Homematic\lib\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
define('API_PREFIX', 'Interface');

class Homematic extends Base
{
    /**
     * Prefix für die Anfrage an der JSON API
     * @var string
     */
    protected $API_PREFIX = 'Interface';
    /**
     * Konstruktor der Klasse
     * @param Session $session
     */
    public function __construct(Session $session=null)
    {
        // Session setzen
        $this->session=$session;
        // Falls Session nicht übergeben wurde, die Instanz initalisieren
        if(!($session instanceof Session))
        {
            $this->session = Session::init();
        }
        // Base Construktor initieren
        parent::initBase();
    }

    /**
     * Aktiviert ein Link-Parameterset
     */
    public function activateLinkParamset ($interface, $address, $peerAddress, $longPress)
    {
        return $this->__do_call();
    }

    /**
     * Lernt ein Gerät anhand seiner Seriennummer an
     */
    public function addDevice ($interface, $serialNumber)
    {
        return $this->__do_call();
    }

    /**
     * Erstellt eine direkte Verknüpfung
     */
    public function addLink ($interface, $sender, $receiver, $name, $description)
    {
        return $this->__do_call();
    }

    /**
     * Ändert den AES-Schlüssel
     */
    public function changeKey ($interface, $passphrase)
    {
        return $this->__do_call();
    }

    /**
     * Löscht die auf der HomeMatic Zentrale gespeicherten Konfigurationsdaten
     * für ein Gerät
     */
    public function clearConfigCache ($interface, $address)
    {
        return $this->__do_call();
    }

    /**
     * Löscht ein Gerät
     */
    public function deleteDevice ($interface, $address, $flags)
    {
        return $this->__do_call();
    }

    /**
     * Bestimmt den Wert eines Patameters
     */
    public function determineParameter ($interface, $address, $paramsetKey, 
            $parameterId)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Beschreibung eines Geräts
     */
    public function getDeviceDescription ($interface, $address)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Restzeit, für die der Anlernmodus noch aktiv ist
     */
    public function getInstallMode ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Seriennummer des letzten Gerätes, welches nicht angelernt
     * werden konnte
     */
    public function getKeyMissmatchDevice ($interface, $reset)
    {
        return $this->__do_call();
    }

    /**
     * Liefert den Namen und die Beschreibung einer direkten Verknüpfung
     */
    public function getLinkInfo ($interface, $senderAddress, $receiverAddress)
    {
        return $this->__do_call();
    }

    /**
     * Liefert alle Kommukationspartner eines Geräts
     */
    public function getLinkPeers ($interface, $address)
    {
        return $this->__do_call();
    }

    /**
     * Liefert für ein Gerät oder einen Kanal alle dirketen Verknüpfungen
     */
    public function getLinks ($interface, $address, $flags)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die aktuelle Stufe der Fehlerprotokollierung
     */
    public function getLogLevel ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Liefert den Wert eines Parameters aus dem Parameterset "MASTER"
     */
    public function getMasterValue ($interface, $address, $valueKey)
    {
        return $this->__do_call();
    }

    /**
     * Liefert ein komplettes Parameterset
     */
    public function getParamset ($interface, $address, $paramsetKey)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Beschreibung eines Parametersets
     */
    public function getParamsetDescription ($interface, $address, $paramsetType)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Id eines Parametersets
     */
    public function getParamsetId ($interface, $address, $paramsetType)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Anzahl der aktiven Servicemeldungen
     */
    public function getServiceMessageCount ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Liefert den Wert eines Parameters aus dem Parameterset "Values"
     */
    public function getValue ($interface, $address, $valueKey)
    {
        return $this->__do_call();
    }

    /**
     * Meldet eine Logikschicht bei einer Schnittstelle an
     */
    public function init ($interface, $url, $interfaceId)
    {
        return $this->__do_call();
    }

    /**
     * Prüft, ob der Dienst der betreffenden Schnittstelle läuft
     */
    public function isPresent ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Listet die verfügbaren BidCoS-RF Interfaces auf
     */
    public function listBidcosInterfaces ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Liefert eine Liste aller angelernten Geräte
     */
    public function listDevices ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Liefert eine Liste der verfügbaren Schnittstellen
     */
    public function listInterfaces ()
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Gerätebeschreibungen aller Teams
     */
    public function listTeams ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Schreibt ein komplettes Parameterset für ein Gerät
     */
    public function putParamset ($interface, $address, $paramsetKey, $set)
    {
        return $this->__do_call();
    }

    /**
     * Löscht eine direkte Verknüpfung
     */
    public function removeLink ($interface, $sender, $receiver)
    {
        return $this->__do_call();
    }

    /**
     * Teilt der Schnittstelle mit, wie häufig die Logikschicht einen Wert
     * verwendet
     */
    public function reportValueUsage ($interface, $address, $valueId, 
            $refCounter)
    {
        return $this->__do_call();
    }

    /**
     * Überträgt alle Konfigurationsdaten erneut an ein Gerät
     */
    public function restoreConfigToDevice ($interface, $address)
    {
        return $this->__do_call();
    }

    /**
     * Liefert die Empfangsfeldstärken der angeschlossenen Geräte
     */
    public function rssiInfo ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Sucht auf dem Bus nach neuen Geräte
     */
    public function searchDevices ($interface)
    {
        return $this->__do_call();
    }

    /**
     * Ordnet ein Geräte einem BidCoS-RF Interface zu
     */
    public function setBidcosInterface ($interface, $deviceId, $interfaceId, 
            $roaming)
    {
        return $this->__do_call();
    }

    /**
     * Aktiviert oder dekativiert den Anlernmodus
     */
    public function setInstallMode ($interface, $on)
    {
        return $this->__do_call();
    }

    /**
     * Legt den Namen und die Beschreibung einer direkten Verknüpfung fest
     */
    public function setLinkInfo ($interface, $sender, $receiver, $name, 
            $description)
    {
        return $this->__do_call();
    }

    /**
     * Legt die Stufe der Fehlerprotokollierung fest
     */
    public function setLogLevel ($interface, $level)
    {
        return $this->__do_call();
    }

    /**
     * Fügt einem Team einen Kanal hinzu
     */
    public function setTeam ($interface, $channelAddress, $teamAddress)
    {
        return $this->__do_call();
    }

    /**
     * Ändert den temporären AES-Schlüssel
     */
    public function setTempKey ($interface, $passphrase)
    {
        return $this->__do_call();
    }

    /**
     * Setzt einen einzelnen Wert im Parameterset "Values"
     */
    public function setValue ($interface, $address, $valueKey, $type, $value)
    {
        return $this->__do_call();
    }

    /**
     * Aktualisiert die Firmware der angegebenen Geräte
     */
    public function updateFirmware ($interface, $device)
    {
        return $this->__do_call();
    }

}

?>