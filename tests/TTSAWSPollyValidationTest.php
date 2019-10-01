<?php

declare(strict_types=1);
include_once __DIR__ . '/stubs/Validator.php';
class TTSAWSSPollyValidationTest extends TestCaseSymconValidation
{
    public function testValidateTTSAWSPolly(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }
    public function testValidateTTSAWSPollyModule(): void
    {
        $this->validateModule(__DIR__ . '/../TTSAWSPolly');
    }
}