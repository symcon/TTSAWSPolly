<?php

require __DIR__ . '/../libs/vendor/autoload.php';

class TTSAWSPolly extends IPSModule
{

  public function Create()
  {

    //Never delete this line!
    parent::Create();

    $this->RegisterPropertyString("AccessKey", "");
    $this->RegisterPropertyString("SecretKey", "");
    $this->RegisterPropertyString("Region", "eu-west-1");
    $this->RegisterPropertyString("VoiceId", "Marlene");
    $this->RegisterPropertyString("OutputFormat", "mp3");
    $this->RegisterPropertyString("SampleRate", "");
  }

  public function ApplyChanges()
  {

    //Never delete this line!
    parent::ApplyChanges();

    if ($this->ReadPropertyString("OutputFormat") == "pcm" && $this->ReadPropertyString("SampleRate") == "22050") {
      echo $this->Translate("PCM (WAV) is not compatible with 22050 Hz as sample rate");
    }
  }

  public function GetConfigurationForm()
  {

    $data = json_decode(file_get_contents(__DIR__ . '/form.json'));

    try {
      $result = $this->DescribeVoices();
      foreach ($result->get('Voices') as $voice) {
        $data->elements[3]->options[] = [
          "label" => $voice["Name"] . " (" . $voice["Gender"] . ", " . $voice["LanguageName"] . ")",
          "value" => $voice["Id"]
        ];
      }
    } catch (Exception $e) {
      //just ignore errors
    }

    return json_encode($data);
  }

  private function GetClient()
  {

    return new Aws\Polly\PollyClient([
      'version' => '2016-06-10',
      'region' => $this->ReadPropertyString("Region"),
      'credentials' => [
        'key'    => $this->ReadPropertyString("AccessKey"),
        'secret' => $this->ReadPropertyString("SecretKey"),
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
      'Text' => $Text,
      'OutputFormat' => $this->ReadPropertyString("OutputFormat"),
      'VoiceId' => $this->ReadPropertyString("VoiceId"),
    ];

    if ($this->ReadPropertyString("SampleRate") != "") {
      $data[] = [
        'SampleRate' => $this->ReadPropertyString("SampleRate")
      ];
    }

    return $this->GetClient()->synthesizeSpeech($data)->get('AudioStream')->getContents();
  }

  public function GetData(string $Text)
  {
    return base64_encode($this->SynthesizeSpeech($Text));
  }

  public function GetFilename(string $Text)
  {
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "awspolly_" . $this->InstanceID;

    if (!is_dir($dir)) {
      mkdir($dir);
    }

    switch ($this->ReadPropertyString("OutputFormat")) {
      case "mp3":
        $file = md5($Text) . ".mp3";
        break;
      case "pcm";
        $file = md5($Text) . ".wav";
        break;
      case "ogg_vorbis":
        $file = md5($Text) . ".ogg";
        break;
      default:
        throw new Exception("Unsupported output format " . $this->ReadPropertyString("OutputFormat"));
    }

    if (!file_exists($dir . DIRECTORY_SEPARATOR . $file)) {
      file_put_contents($dir . DIRECTORY_SEPARATOR . $file, $this->SynthesizeSpeech($Text));
    }

    return $dir . DIRECTORY_SEPARATOR . $file;
  }
}
