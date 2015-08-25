<?

class GitHubStatus extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->RegisterProfileIntegerEx("Status.GitHub", "Information", "", "", Array(
            Array(1, "keine", "", 0x00FF00),
            Array(2, "geringe", "", 0xFF8000),
            Array(3, "starke", "", 0xFF0000)
        ));


        $this->RegisterVariableInteger("Status", "Status", "Status.GitHub", 1);
        $this->RegisterVariableInteger("TimeStamp", "Aktualisierung", "~UnixTimestamp", 2);
        $this->RegisterVariableString("LastMessage", "Letzte Meldung", "", 3);

        // 15 Minuten Timer
        $this->RegisterTimer("UpdateGitHubStatus", 5 * 60, 'GH_Update($_IPS[\'TARGET\']);');
        // Nach übernahme der Einstellungen oder IPS-Neustart einmal Update durchführen.
        $this->Update();
    }

    private function GetStatus()
    {
        $link = "https://status.github.com/api/last-message.json";

        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 3
            )
                )
        );
        $jsonstring = file_get_contents($link, 0, $ctx);

        $Data = json_decode($jsonstring);
        if ($Data == null)
        {
            throw new Exception("Cannot load GitHub Status.");
        }

        return $Data;
    }

    public function Update()
    {
        $NewStatus = $this->GetStatus();
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
        }
        else
        {
            IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, 1);

            IPS_SetEventActive($id, false);
        }
    }

    protected function SetTimerInterval($Name, $Interval)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            throw new Exception('Timer not present');
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
        }
        else
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
        }
        else
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