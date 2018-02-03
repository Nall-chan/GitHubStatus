<?

/**
 * @addtogroup githubstatus
 * @{
 *
 * @package       GitHubStatus
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2017 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.03
 */

/**
 * GitHubStatus ist die Klasse für das IPS-Modul 'GitHub-Status'.
 * Erweitert IPSModule 
 */
class GitHubStatus extends IPSModule
{

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterTimer("UpdateGitHubStatus", 300000, 'GH_Update($_IPS[\'TARGET\']);');
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Destroy()
    {
        if (IPS_InstanceExists($this->InstanceID))
            return;
        $this->UnregisterProfil("Status.GitHub");
        parent::Destroy();
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->RegisterProfileIntegerEx("Status.GitHub", "Information", "", "", Array(
            Array(1, "keine", "", 0x00FF00),
            Array(2, "geringe ", "", 0xFF8000),
            Array(3, "starke", "", 0xFF0000)
        ));

        $this->RegisterVariableInteger("Status", "Beeinträchtigungen", "Status.GitHub", 1);
        $this->RegisterVariableInteger("TimeStamp", "Aktualisierung", "~UnixTimestamp", 2);
        $this->RegisterVariableString("LastMessage", "Letzte Meldung", "", 3);

        $this->Update();
    }

    /**
     * IPS-Instanz-Funktion 'GH_Update'.
     * Liest den aktuellen Status von GitHub und visualisiert Diesen.
     * 
     * @access public
     * @return boolean True bei Erfolg, sonst false.
     */
    public function Update()
    {
        try {
            $NewStatus = $this->GetStatus();
        }
        catch (Exception $exc) {
            trigger_error($exc->getMessage(), E_USER_NOTICE);
            $this->SetValueString("LastMessage", "GitHub unreachable");
            $this->SetValueInteger("TimeStamp", time());
            $this->SetValueInteger("Status", 3);
            $this->SetHidden("LastMessage", false);
            return false;
        }

        $this->SetValueString("LastMessage", (string) $NewStatus->body);

        $Date = new DateTime((string) $NewStatus->created_on);
        $TimeStamp = $Date->getTimestamp();

        $this->SetValueInteger("TimeStamp", $TimeStamp);

        switch ((string) $NewStatus->status) {
            case 'good':
                $this->SetValueInteger("Status", 1);
                $this->SetHidden("LastMessage", true);
                break;
            case 'minor':
                $this->SetValueInteger("Status", 2);
                $this->SetHidden("LastMessage", false);
                break;
            case 'major':
                $this->SetValueInteger("Status", 3);
                $this->SetHidden("LastMessage", false);
                break;
        }
    }

################## private

    /**
     * Liest den aktuellen Status von GitHub und liefert das Ergebnis.
     * 
     * @return object Ein Object mit den aktuellen Status.
     * @throws Exception Wenn GitHub nicht erreichbar.
     */
    private function GetStatus()
    {
        $link = "status.github.com/api/last-message.json";

        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 5
            )
                )
        );
        $jsonstring = @file_get_contents('https://' . $link, false, $ctx);
        if ($jsonstring === false)
            $jsonstring = @file_get_contents('http://' . $link, false, $ctx);
        if ($jsonstring === false)
            throw new Exception("Cannot load GitHub status.");

        $Data = json_decode($jsonstring);
        if ($Data == null) {
            throw new Exception("Cannot decode GitHub status.");
        }

        return $Data;
    }

    ################## DUMMYS / WOARKAROUNDS - private

    /**
     * Steuert die Sichtbarkeit von einem Objekt.
     *  
     * @param string $Ident Der Ident des Objektes.
     * @param bool $isHidden True zum verstecken, false zum anzeigen.
     */
    private function SetHidden(string $Ident, bool $isHidden)
    {
        if (IPS_GetObject($this->GetIDForIdent($Ident))['ObjectIsHidden'] <> $isHidden) {
            IPS_SetHidden($this->GetIDForIdent($Ident), $isHidden);
        }
    }

    /**
     * Setzt eine Integer-Variable
     * 
     * @param string $Ident Der Ident der Integer-Variable
     * @param int $value Der neue Wert der Integer-Variable
     */
    private function SetValueInteger(string $Ident, int $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueInteger($id, $value);
    }

    /**
     * Setzt eine String-Variable
     * 
     * @param string $Ident Der Ident der String-Variable
     * @param string $value Der neue Wert der String-Variable
     */
    private function SetValueString(string $Ident, string $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueString($id, $value);
    }

################## DUMMYS / WOARKAROUNDS - protected

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ integer
     *
     * @access protected
     * @param string $Name Name des Profils.
     * @param string $Icon Name des Icon.
     * @param string $Prefix Prefix für die Darstellung.
     * @param string $Suffix Suffix für die Darstellung.
     * @param int $MinValue Minimaler Wert.
     * @param int $MaxValue Maximaler wert.
     * @param int $StepSize Schrittweite
     */
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {

        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 1);
        }
        else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1)
                throw new Exception("Variable profile type does not match for profile " . $Name);
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ integer mit Assoziationen
     *
     * @access protected
     * @param string $Name Name des Profils.
     * @param string $Icon Name des Icon.
     * @param string $Prefix Prefix für die Darstellung.
     * @param string $Suffix Suffix für die Darstellung.
     * @param array $Associations Assoziationen der Werte als Array.
     */
    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (sizeof($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        }
        else {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[sizeof($Associations) - 1][0];
        }

        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);

        foreach ($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }
    }

    /**
     * Löscht ein Variablenprofile, sofern es nicht außerhalb dieser Instanz noch verwendet wird.
     * @param string $Profil Name des zu löschenden Profils.
     */
    protected function UnregisterProfil(string $Profil)
    {
        if (!IPS_VariableProfileExists($Profil))
            return;
        foreach (IPS_GetVariableList() as $VarID) {
            if (IPS_GetParent($VarID) == $this->InstanceID)
                continue;
            if (IPS_GetVariable($VarID)['VariableCustomProfile'] == $Profil)
                return;
        }
        IPS_DeleteVariableProfile($Profil);
    }

}

/** @} */