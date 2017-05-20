<?php

namespace Tests\Unit;

use App\SubmitAction;
use ZipArchive;

class SubmitActionTest extends \PHPUnit_Framework_TestCase
{
    const SAMPLE_ZIP = 'UEsDBAoAAAAAAOSxtEqpMMX+BwAAAAcAAAAIAAAAYmx1Yi50eHRjb250ZW50UEsBAj8ACgAAAAAA5LG0Sqkwxf4HAAAABwAAAAgAJAAAAAAAAAAgAAAAAAAAAGJsdWIudHh0CgAgAAAAAAABABgAwzT2xaXR0gFsq8BHpNHSAWyrwEek0dIBUEsFBgAAAAABAAEAWgAAAC0AAAAAAA==';
    const SAMPLE_ZIP_B64_FAIL = 'UEsDBAoAAAAAAJCwtEo/MR9DDAAAAAwAAAAIAAAAYmx1Yi50eHRzb21lIGNvbnRlbnRQ
SwECPwAKAAAAAACQsLRKPzEfQwwAAAAMAAAACAAkAAAAAAAAACAAAAAAAAAAYmx1Yi50eHQKACAAAAAAAAEAGAAguNNKpNHSAWyrwEek0dIBbKvAR6TR0gFQSwUGAAAAAAEAAQBaAAAAMgAAAAAA';

    /** @var array|string[] */
    private $files = [];

    protected function setUp()
    {
        parent::setUp();

        $validZip = $this->createTestFile();
        file_put_contents($validZip, base64_decode(static::SAMPLE_ZIP));

        $crippledB64Zip = $this->createTestFile();
        file_put_contents($crippledB64Zip, static::SAMPLE_ZIP_B64_FAIL);

        $this->files = [
            'validZip' => $validZip,
            'crippledB64Zip' => $crippledB64Zip,
        ];
    }

    protected function tearDown()
    {
        foreach ($this->files as $testfile) {
            @unlink($testfile);
        }

        parent::tearDown();
    }

    /**
     * Creates a file for testing at the system temp dir.
     *
     * @return string
     */
    protected function createTestFile(): string
    {
        return tempnam(sys_get_temp_dir(), 'dmarc_testing');
    }

    /**
     * Check that the base64 check works as intended.
     */
    public function testIs_base64_encoded()
    {
        $this->assertTrue(SubmitAction::is_base64_encoded('aGV5IHRoZXJl'));
        $this->assertFalse(SubmitAction::is_base64_encoded('hey there'));
    }

    /**
     * It should "repair" files that are encoded b64 with line breaks.
     */
    public function testFixesB64EncodingIssues()
    {
        $zip = $this->files['crippledB64Zip'];

        SubmitAction::fixEncodingIssues($zip);

        $this->assertNotEquals(static::SAMPLE_ZIP_B64_FAIL, file_get_contents($zip));

        $za = new ZipArchive();
        $za->open($zip);

        $this->assertSame(1, $za->numFiles);
    }

    /**
     * It should not touch valid files.
     */
    public function testDoesNotModifyAValidZip()
    {
        $zip = $this->files['validZip'];

        SubmitAction::fixEncodingIssues($zip);

        $this->assertSame(base64_decode(static::SAMPLE_ZIP), file_get_contents($zip));
    }
}
