<?

class GitHubStatus extends IPSModule
{

    public function Create()
    {
        parent::Create();
    }

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

        // 15 Minuten Timer
        try
        {
            $this->RegisterTimer("UpdateGitHubStatus", 300000, 'GH_Update($_IPS[\'TARGET\']);');
        } catch (Exception $exc)
        {
            trigger_error($exc->getMessage(), $exc->getCode());
            return;
        }
        $this->Update();
    }

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
            throw new Exception("Cannot load GitHub Status.", E_USER_NOTICE);

        $Data = json_decode($jsonstring);
        if ($Data == null)
        {
            throw new Exception("Cannot load GitHub Status.", E_USER_NOTICE);
        }

        return $Data;
    }

    public function Update()
    {
        try
        {
            $NewStatus = $this->GetStatus();            
        } catch (Exception $exc)
        {
            trigger_error($exc->getMessage(),$exc->getCode());
            return false;
        }

        $this->SetValueString("LastMessage", (string) $NewStatus->body);

        $Date = new DateTime((string) $NewStatus->created_on);
        $TimeStamp = $Date->getTimestamp();

        $this->SetValueInteger("TimeStamp", $TimeStamp);

        switch ((string) $NewStatus->status)
        {
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

    protected function SetHidden($Ident, $isHidden)
    {
        if (IPS_GetObject($this->GetIDForIdent($Ident))['ObjectIsHidden'] <> $isHidden)
        {
            IPS_SetHidden($this->GetIDForIdent($Ident), $isHidden);
        }
    }

    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
            SetValueInteger($id, $value);
    }

    private function SetValueString($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
            SetValueString($id, $value);
    }

################## DUMMYS / WOARKAROUNDS - protected
    //Remove on next Symcon update

    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {

        if (!IPS_VariableProfileExists($Name))
        {
            IPS_CreateVariableProfile($Name, 1);
        } else
        {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1)
                throw new Exception("Variable profile type does not match for profile " . $Name);
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }

    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (sizeof($Associations) === 0)
        {
            $MinValue = 0;
            $MaxValue = 0;
        } else
        {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[sizeof($Associations) - 1][0];
        }

        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);

        foreach ($Associations as $Association)
        {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }
    }

}

?>