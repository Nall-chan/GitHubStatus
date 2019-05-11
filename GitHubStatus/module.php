<?php

declare(strict_types=1);
/**
 * @addtogroup githubstatus
 * @{
 *
 * @file          module.php
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       2.10
 */

/**
 * GitHubStatus ist die Klasse für das IPS-Modul 'GitHub-Status'.
 * Erweitert IPSModule.
 */
class GitHubStatus extends IPSModule
{
    /**
     * Interne Funktion des SDK.
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterTimer('UpdateGitHubStatus', 300 * 1000, 'GH_Update($_IPS[\'TARGET\']);');
    }

    /**
     * Interne Funktion des SDK.
     */
    public function Destroy()
    {
        if (!IPS_InstanceExists($this->InstanceID)) {
            $this->UnregisterProfil('Status.GitHub');
        }
        parent::Destroy();
    }

    /**
     * Interne Funktion des SDK.
     */
    public function ApplyChanges()
    {
        parent::ApplyChanges();

        $this->RegisterProfileIntegerEx('Status.GitHub', 'Information', '', '', [
            [0, $this->Translate('Statuspage unreachable'), '', 0xFF8000],
            [1, $this->Translate('none'), '', 0x00FF00],
            [2, $this->Translate('minor'), '', 0xFF8000],
            [3, $this->Translate('major'), '', 0xFF0000],
            [4, $this->Translate('critical'), '', 0xFF0000]
        ]);

        $this->RegisterVariableInteger('Status', $this->Translate('indicator'), 'Status.GitHub', 1);
        $this->RegisterVariableInteger('TimeStamp', $this->Translate('updated'), '~UnixTimestamp', 2);
        $this->RegisterVariableString('LastMessage', $this->Translate('description'), '', 3);
        $this->Update();
    }

    /**
     * IPS-Instanz-Funktion 'GH_Update'.
     * Liest den aktuellen Status von GitHub und visualisiert Diesen.
     *
     * @return bool True bei Erfolg, sonst false.
     */
    public function Update()
    {
        try {
            $NewStatus = $this->GetNewStatus();
        } catch (Exception $exc) {
            $this->SendDebug('error', $exc->getMessage(), 0);
            trigger_error($exc->getMessage(), E_USER_NOTICE);
            $this->SetValue('LastMessage', 'Statuspage unreachable');
            $this->SetValue('TimeStamp', time());
            $this->SetValue('Status', 3);
            return false;
        }
        $this->SetValue('LastMessage', (string) $NewStatus->status->description);

        $Date = new DateTime((string) $NewStatus->page->updated_at);
        $TimeStamp = $Date->getTimestamp();

        $this->SetValue('TimeStamp', $TimeStamp);

        switch ((string) $NewStatus->status->indicator) {
            case 'none':
                $this->SetValue('Status', 1);
                break;
            case 'minor':
                $this->SetValue('Status', 2);
                break;
            case 'major':
                $this->SetValue('Status', 3);
                break;
            case 'critical':
                $this->SetValue('Status', 4);
                break;
        }
    }

    //################# private

    /**
     * Liest den aktuellen Status von GitHub und liefert das Ergebnis.
     *
     * @throws Exception Wenn GitHub nicht erreichbar.
     *
     * @return object Ein Object mit den aktuellen Status.
     */
    private function GetNewStatus()
    {
        $link = 'kctbh9vrtdwd.statuspage.io/api/v2/status.json';

        $ctx = stream_context_create(
                [
                    'http' => [
                        'timeout' => 5
                    ]
        ]);
        $jsonstring = @file_get_contents('https://' . $link, false, $ctx);
        if ($jsonstring === false) {
            $jsonstring = @file_get_contents('http://' . $link, false, $ctx);
        }
        if ($jsonstring === false) {
            throw new Exception('Cannot load GitHub status.');
        }
        $this->SendDebug('fetch', $jsonstring, 0);
        $Data = json_decode($jsonstring);
        if ($Data == null) {
            throw new Exception('Cannot decode GitHub status.');
        }

        return $Data;
    }

    //################# DUMMYS / WOARKAROUNDS - protected

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ integer.
     *
     * @param string $Name     Name des Profils.
     * @param string $Icon     Name des Icon.
     * @param string $Prefix   Prefix für die Darstellung.
     * @param string $Suffix   Suffix für die Darstellung.
     * @param int    $MinValue Minimaler Wert.
     * @param int    $MaxValue Maximaler wert.
     * @param int    $StepSize Schrittweite
     */
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {
        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 1);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1) {
                throw new Exception('Variable profile type does not match for profile ' . $Name);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ integer mit Assoziationen.
     *
     * @param string $Name         Name des Profils.
     * @param string $Icon         Name des Icon.
     * @param string $Prefix       Prefix für die Darstellung.
     * @param string $Suffix       Suffix für die Darstellung.
     * @param array  $Associations Assoziationen der Werte als Array.
     */
    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (count($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        } else {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[count($Associations) - 1][0];
        }

        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);

        foreach ($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }
    }

    /**
     * Löscht ein Variablenprofile, sofern es nicht außerhalb dieser Instanz noch verwendet wird.
     *
     * @param string $Profil Name des zu löschenden Profils.
     */
    protected function UnregisterProfil(string $Profil)
    {
        if (!IPS_VariableProfileExists($Profil)) {
            return;
        }
        foreach (IPS_GetVariableList() as $VarID) {
            if (IPS_GetParent($VarID) == $this->InstanceID) {
                continue;
            }
            if (IPS_GetVariable($VarID)['VariableCustomProfile'] == $Profil) {
                return;
            }
        }
        IPS_DeleteVariableProfile($Profil);
    }
}

/* @} */
