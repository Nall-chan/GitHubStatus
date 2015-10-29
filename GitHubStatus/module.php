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
            $this->RegisterTimer("UpdateGitHubStatus", 5 * 60, 'GH_Update($_IPS[\'TARGET\']);');
        } catch (Exception $exc)
        {
            trigger_error($exc->getMessage(), $exc->getCode());
            return;
        }
        // Nach übernahme der Einstellungen oder IPS-Neustart einmal Update durchführen.
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
            throw new Exception("Cannot load GitHub Status.", E_USER_ERROR);

        $Data = json_decode($jsonstring);
        if ($Data == null)
        {
            throw new Exception("Cannot load GitHub Status.", E_USER_ERROR);
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

    protected function RegisterTimer($Name, $Interval, $Script)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            $id = 0;


        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception("Ident with name " . $Name . " is used for wrong object type", E_USER_ERROR);

            if (IPS_GetEvent($id)['EventType'] <> 1)
            {
                IPS_DeleteEvent($id);
                $id = 0;
            }
        }

        if ($id == 0)
        {
            $id = IPS_CreateEvent(1);
            IPS_SetParent($id, $this->InstanceID);
            IPS_SetIdent($id, $Name);
        }
        IPS_SetName($id, $Name);
        IPS_SetHidden($id, true);
        IPS_SetEventScript($id, $Script);
        if ($Interval > 0)
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);

            IPS_SetEventActive($id, true);
        } else
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, 1);

            IPS_SetEventActive($id, false);
        }
    }

    protected function UnregisterTimer($Name)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception('Timer not present', E_USER_NOTICE);
            IPS_DeleteEvent($id);
        }
    }

    protected function SetTimerInterval($Name, $Interval)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            throw new Exception('Timer not present', E_USER_ERROR);
        if (!IPS_EventExists($id))
            throw new Exception('Timer not present', E_USER_ERROR);

        $Event = IPS_GetEvent($id);

        if ($Interval < 1)
        {
            if ($Event['EventActive'])
                IPS_SetEventActive($id, false);
        }
        else
        {
            if ($Event['CyclicTimeValue'] <> $Interval)
                IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);
            if (!$Event['EventActive'])
                IPS_SetEventActive($id, true);
        }
    }

    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueInteger($id) <> $value)
            SetValueInteger($id, $value);
    }

    private function SetValueString($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueString($id) <> $value)
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