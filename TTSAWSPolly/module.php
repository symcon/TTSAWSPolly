<?php

declare(strict_types=1);

require __DIR__ . '/../libs/vendor/autoload.php';

class TTSAWSPolly extends IPSModule
{
    public function Create()
    {

    //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString('AccessKey', '');
        $this->RegisterPropertyString('SecretKey', '');
        $this->RegisterPropertyString('Region', 'eu-west-1');
        $this->RegisterPropertyString('VoiceId', 'Marlene');
        $this->RegisterPropertyString('OutputFormat', 'mp3');
        $this->RegisterPropertyString('SampleRate', '');
    }

    public function ApplyChanges()
    {

    //Never delete this line!
        parent::ApplyChanges();

        if ($this->ReadPropertyString('OutputFormat') == 'pcm' && $this->ReadPropertyString('SampleRate') == '22050') {
            echo $this->Translate('PCM (WAV) is not compatible with 22050 Hz as sample rate');
        }
    }

    public function GetConfigurationForm()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/form.json'), true);

        try {
            $result = $this->DescribeVoices();
            foreach ($result->get('Voices') as $voice) {
                $data['elements'][3]['options'][] = [
                    'label' => $voice['Name'] . ' (' . $voice['Gender'] . ', ' . $voice['LanguageName'] . ')',
                    'value' => $voice['Id']
                ];
            }
        } catch (Aws\Exception\AwsException $e) {
            //hide some options
            array_pop($data['elements']);
            array_pop($data['elements']);
            array_pop($data['elements']);

            //show an PopupAlert with the error if credentials are set
            if ($this->ReadPropertyString('AccessKey') != '' || $this->ReadPropertyString('SecretKey') != '') {
                $data['elements'][] = [
                    'type'  => 'PopupAlert',
                    'popup' => [
                        'items' => [
                            [
                                'type'    => 'Label',
                                'caption' => $e->getAwsErrorMessage()
                            ]
                        ]
                    ]
                ];
            }
        }

        return json_encode($data);
    }

    private function GetClient()
    {
        return new Aws\Polly\PollyClient([
            'version'     => '2016-06-10',
            'region'      => $this->ReadPropertyString('Region'),
            'credentials' => [
                'key'    => $this->ReadPropertyString('AccessKey'),
                'secret' => $this->ReadPropertyString('SecretKey'),
            ]
        ]);
    }

    private function DescribeVoices()
    {
        return $this->GetClient()->describeVoices();
    }

    private function SynthesizeSpeech($Text)
    {
        $data = [
            'Text'         => $Text,
            'OutputFormat' => $this->ReadPropertyString('OutputFormat'),
            'VoiceId'      => $this->ReadPropertyString('VoiceId'),
        ];

        if ($this->ReadPropertyString('SampleRate') != '') {
            $data['SampleRate'] = $this->ReadPropertyString('SampleRate');
        }

        return $this->GetClient()->synthesizeSpeech($data)->get('AudioStream')->getContents();
    }

    private function AddWAVHeader($Data)
    {
        $sampleRate = 16000;
        if ($this->ReadPropertyString('SampleRate') != '') {
            $sampleRate = intval($this->ReadPropertyString('SampleRate'));
        }

        $channels = 1;
        $bits = 16;

        //add RIFF header: https://gist.github.com/Jon-Schneider/8b7c53d27a7a13346a643d$
        $header = 'RIFF';
        $header .= pack('l', strlen($Data) + 32);
        $header .= 'WAVE';
        $header .= 'fmt ';
        $header .= pack('l', 16); //PCM = 16
        $header .= pack('s', 1);  //PCM = 1
        $header .= pack('s', $channels);                              //Channels
        $header .= pack('l', $sampleRate);                            //Sample Rate
        $header .= pack('l', 2 * $sampleRate);                        //Byte Rate
        $header .= pack('s', intval($channels * (($bits + 7) / 8)));  //Sample Alignment
        $header .= pack('s', $bits);                                  //Bit Depth
        $header .= 'data';
        $header .= pack('l', strlen($Data));
        return $header . $Data;
    }

    public function GenerateData(string $Text)
    {
        $data = $this->SynthesizeSpeech($Text);

        if ($this->ReadPropertyString('OutputFormat') == 'pcm') {
            $data = $this->AddWAVHeader($data);
        }

        return base64_encode($data);
    }

    public function GenerateFile(string $Text)
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'awspolly_' . $this->InstanceID;

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $data = $this->SynthesizeSpeech($Text);
        $info = $this->ReadPropertyString('VoiceID') . '_' . $this->ReadPropertyString('SampleRate') . '_';

        switch ($this->ReadPropertyString('OutputFormat')) {
      case 'mp3':
        $file = $info . md5($Text) . '.mp3';
        break;
      case 'pcm':
        $file = $info . md5($Text) . '.wav';
        $data = $this->AddWAVHeader($data);
        break;
      case 'ogg_vorbis':
        $file = $info . md5($Text) . '.ogg';
        break;
      default:
        throw new Exception('Unsupported output format ' . $this->ReadPropertyString('OutputFormat'));
    }

        if (!file_exists($dir . DIRECTORY_SEPARATOR . $file)) {
            file_put_contents($dir . DIRECTORY_SEPARATOR . $file, $data);
        }

        return $dir . DIRECTORY_SEPARATOR . $file;
    }
}
