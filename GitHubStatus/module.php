<?php

declare(strict_types=1);

eval('declare(strict_types=1);namespace GitHubStatus {?>' . file_get_contents(__DIR__ . '/../libs/helper/DebugHelper.php') . '}');
eval('declare(strict_types=1);namespace GitHubStatus {?>' . file_get_contents(__DIR__ . '/../libs/helper/VariableProfileHelper.php') . '}');

/**
 * @addtogroup GitHubStatus
 * @{
 *
 * @file          module.php
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2025 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       3.10
 */

/**
 * GitHubStatus ist die Klasse für das IPS-Modul 'GitHub-Status'.
 * Erweitert IPSModule.
 *
 * @method bool SendDebug(string $Message, mixed $Data, int $Format)
 * @method void RegisterProfileIntegerEx(string $Name, string $Icon, string $Prefix, string $Suffix, array $Associations, int $MaxValue = -1, float $StepSize = 0)
 * @method void UnregisterProfile(string $Name)
 */
class GitHubStatus extends IPSModuleStrict
{
    use \GitHubStatus\VariableProfileHelper;
    use \GitHubStatus\DebugHelper;

    /**
     * Interne Funktion des SDK.
     */
    public function Create(): void
    {
        parent::Create();
        $this->RegisterTimer('UpdateGitHubStatus', 300 * 1000, 'GH_Update($_IPS[\'TARGET\']);');
    }

    /**
     * Interne Funktion des SDK.
     */
    public function Destroy(): void
    {
        if (!IPS_InstanceExists($this->InstanceID)) {
            $this->UnregisterProfile('Status.GitHub');
        }
        parent::Destroy();
    }

    /**
     * Interne Funktion des SDK.
     */
    public function ApplyChanges(): void
    {
        parent::ApplyChanges();

        $this->UnregisterProfile('Status.GitHub');
        $this->UnregisterProfile('Components.GitHub');
        $this->RegisterVariableInteger('Status', $this->Translate('indicator'), [
            'PRESENTATION'    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
            'ICON'            => 'Information',
            'COLOR'           => -1,
            'PREFIX'          => '',
            'SUFFIX'          => '',
            'USAGE_TYPE'      => 0,
            'PERCENTAGE'      => false,
            'MIN'             => 0,
            'MAX'             => 0,
            'DIGITS'          => 0,
            'INTERVALS_ACTIVE'=> true,
            'INTERVALS'       => json_encode(
                [
                    [
                        'IntervalMinValue'            => 0,
                        'IntervalMaxValue'            => 0,
                        'ConstantActive'              => true,
                        'ConstantValue'               => $this->Translate('Statuspage unreachable'),
                        'ConversionFactor'            => 0,
                        'PrefixActive'                => false,
                        'PrefixValue'                 => '',
                        'SuffixActive'                => false,
                        'SuffixValue'                 => '',
                        'DigitsActive'                => false,
                        'DigitsValue'                 => 0,
                        'IconActive'                  => false,
                        'IconValue'                   => '',
                        'ColorActive'                 => true,
                        'ColorValue'                  => 0xFF8000
                    ],                            [
                        'IntervalMinValue'            => 1,
                        'IntervalMaxValue'            => 1,
                        'ConstantActive'              => true,
                        'ConstantValue'               => $this->Translate('none'),
                        'ConversionFactor'            => 0,
                        'PrefixActive'                => false,
                        'PrefixValue'                 => '',
                        'SuffixActive'                => false,
                        'SuffixValue'                 => '',
                        'DigitsActive'                => false,
                        'DigitsValue'                 => 0,
                        'IconActive'                  => false,
                        'IconValue'                   => '',
                        'ColorActive'                 => true,
                        'ColorValue'                  => 0x00FF00
                    ],                            [
                        'IntervalMinValue'            => 2,
                        'IntervalMaxValue'            => 2,
                        'ConstantActive'              => true,
                        'ConstantValue'               => $this->Translate('minor'),
                        'ConversionFactor'            => 0,
                        'PrefixActive'                => false,
                        'PrefixValue'                 => '',
                        'SuffixActive'                => false,
                        'SuffixValue'                 => '',
                        'DigitsActive'                => false,
                        'DigitsValue'                 => 0,
                        'IconActive'                  => false,
                        'IconValue'                   => '',
                        'ColorActive'                 => true,
                        'ColorValue'                  => 0xFF8000
                    ],                            [
                        'IntervalMinValue'            => 3,
                        'IntervalMaxValue'            => 3,
                        'ConstantActive'              => true,
                        'ConstantValue'               => $this->Translate('major'),
                        'ConversionFactor'            => 0,
                        'PrefixActive'                => false,
                        'PrefixValue'                 => '',
                        'SuffixActive'                => false,
                        'SuffixValue'                 => '',
                        'DigitsActive'                => false,
                        'DigitsValue'                 => 0,
                        'IconActive'                  => false,
                        'IconValue'                   => '',
                        'ColorActive'                 => true,
                        'ColorValue'                  => 0xFF0000
                    ],                            [
                        'IntervalMinValue'            => 4,
                        'IntervalMaxValue'            => 4,
                        'ConstantActive'              => true,
                        'ConstantValue'               => $this->Translate('critical'),
                        'ConversionFactor'            => 0,
                        'PrefixActive'                => false,
                        'PrefixValue'                 => '',
                        'SuffixActive'                => false,
                        'SuffixValue'                 => '',
                        'DigitsActive'                => false,
                        'DigitsValue'                 => 0,
                        'IconActive'                  => false,
                        'IconValue'                   => '',
                        'ColorActive'                 => true,
                        'ColorValue'                  => 0xFF0000
                    ]
                ]
            )
        ], 1);
        $this->RegisterVariableInteger(
            'TimeStamp',
            $this->Translate('updated'),
            [
                'PRESENTATION'   => VARIABLE_PRESENTATION_DATE_TIME,
                'DATE'           => 1,
                'MONTH_TEXT'     => false,
                'DAY_OF_THE_WEEK'=> false,
                'TIME'           => 2
            ],
            2
        );
        $this->RegisterVariableString('LastMessage', $this->Translate('description'), [], 3);
        $this->Update();
    }

    /**
     * IPS-Instanz-Funktion 'GH_Update'.
     * Liest den aktuellen Status von GitHub und visualisiert Diesen.
     */
    public function Update(): void
    {
        $NewStatus = $this->GetNewStatus();
        if (!$NewStatus) {
            $this->SetValue('LastMessage', 'Statuspage unreachable');
            $this->SetValue('TimeStamp', time());
            $this->SetValue('Status', 0);
            trigger_error($this->Translate('Cannot load GitHub status.'), E_USER_NOTICE);
        }
        $this->SetValue('LastMessage', (string) $NewStatus['status']['description']);

        $Date = new DateTime((string) $NewStatus['page']['updated_at']);
        $TimeStamp = $Date->getTimestamp();
        $this->SetValue('TimeStamp', $TimeStamp);
        switch ((string) $NewStatus['status']['indicator']) {
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
        foreach ($NewStatus['components'] as $Component) {
            $Ident = $Component['id'];
            if ($Ident == '0l2p9nhqnxpd') {
                continue;
            }
            $this->SendDebug('Result', $Component, 0);
            $Name = $Component['name'];
            $Position = $Component['position'];
            $this->RegisterVariableInteger($Ident, $this->Translate($Name), [
                'PRESENTATION'    => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
                'ICON'            => 'Information',
                'COLOR'           => -1,
                'PREFIX'          => '',
                'SUFFIX'          => '',
                'USAGE_TYPE'      => 0,
                'PERCENTAGE'      => false,
                'MIN'             => 0,
                'MAX'             => 0,
                'DIGITS'          => 0,
                'INTERVALS_ACTIVE'=> true,
                'INTERVALS'       => json_encode(
                    [
                        [
                            'IntervalMinValue'            => 0,
                            'IntervalMaxValue'            => 0,
                            'ConstantActive'              => true,
                            'ConstantValue'               => $this->Translate('Statuspage unreachable'),
                            'ConversionFactor'            => 0,
                            'PrefixActive'                => false,
                            'PrefixValue'                 => '',
                            'SuffixActive'                => false,
                            'SuffixValue'                 => '',
                            'DigitsActive'                => false,
                            'DigitsValue'                 => 0,
                            'IconActive'                  => false,
                            'IconValue'                   => '',
                            'ColorActive'                 => true,
                            'ColorValue'                  => 0xFF8000
                        ],                            [
                            'IntervalMinValue'            => 1,
                            'IntervalMaxValue'            => 1,
                            'ConstantActive'              => true,
                            'ConstantValue'               => $this->Translate('operational'),
                            'ConversionFactor'            => 0,
                            'PrefixActive'                => false,
                            'PrefixValue'                 => '',
                            'SuffixActive'                => false,
                            'SuffixValue'                 => '',
                            'DigitsActive'                => false,
                            'DigitsValue'                 => 0,
                            'IconActive'                  => false,
                            'IconValue'                   => '',
                            'ColorActive'                 => true,
                            'ColorValue'                  => 0x00FF00
                        ],                            [
                            'IntervalMinValue'            => 2,
                            'IntervalMaxValue'            => 2,
                            'ConstantActive'              => true,
                            'ConstantValue'               => $this->Translate('degraded performance'),
                            'ConversionFactor'            => 0,
                            'PrefixActive'                => false,
                            'PrefixValue'                 => '',
                            'SuffixActive'                => false,
                            'SuffixValue'                 => '',
                            'DigitsActive'                => false,
                            'DigitsValue'                 => 0,
                            'IconActive'                  => false,
                            'IconValue'                   => '',
                            'ColorActive'                 => true,
                            'ColorValue'                  => 0xFF8000
                        ],                            [
                            'IntervalMinValue'            => 3,
                            'IntervalMaxValue'            => 3,
                            'ConstantActive'              => true,
                            'ConstantValue'               => $this->Translate('partial outage'),
                            'ConversionFactor'            => 0,
                            'PrefixActive'                => false,
                            'PrefixValue'                 => '',
                            'SuffixActive'                => false,
                            'SuffixValue'                 => '',
                            'DigitsActive'                => false,
                            'DigitsValue'                 => 0,
                            'IconActive'                  => false,
                            'IconValue'                   => '',
                            'ColorActive'                 => true,
                            'ColorValue'                  => 0xFF0000
                        ],                            [
                            'IntervalMinValue'            => 4,
                            'IntervalMaxValue'            => 4,
                            'ConstantActive'              => true,
                            'ConstantValue'               => $this->Translate('major outage'),
                            'ConversionFactor'            => 0,
                            'PrefixActive'                => false,
                            'PrefixValue'                 => '',
                            'SuffixActive'                => false,
                            'SuffixValue'                 => '',
                            'DigitsActive'                => false,
                            'DigitsValue'                 => 0,
                            'IconActive'                  => false,
                            'IconValue'                   => '',
                            'ColorActive'                 => true,
                            'ColorValue'                  => 0xFF0000
                        ]
                    ]
                )

            ], $Position + 3);

            switch ((string) $Component['status']) {
                case 'operational':
                    $this->SetValue($Ident, 1);
                    break;
                case 'degraded_performance':
                    $this->SetValue($Ident, 2);
                    break;
                case 'partial_outage':
                    $this->SetValue($Ident, 3);
                    break;
                case 'major_outage':
                    $this->SetValue($Ident, 4);
                    break;
            }
        }
    }

    /**
     * Liest den aktuellen Status von GitHub und liefert das Ergebnis.
     *
     * @throws Exception Wenn GitHub nicht erreichbar.
     *
     * @return object Ein Object mit den aktuellen Status.
     */
    private function GetNewStatus(): false|array
    {
        $link = 'https://www.githubstatus.com/api/v2/summary.json';
        $this->SendDebug('Fetch', $link, 0);
        $JsonString = @Sys_GetURLContentEx($link, ['Timeout'=> 5000]);
        $this->SendDebug('Result', $JsonString, 0);
        if (!$JsonString) {
            return false;
        }
        $Data = json_decode($JsonString, true);
        return $Data;
    }
}

/* @} */
