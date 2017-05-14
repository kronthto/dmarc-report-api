<?php

namespace Tests\Functional;

use Slim\Http\UploadedFile;
use Solaris\DmarcAggregateParser;

class SubmitActionTest extends BaseTestCase
{
    /** @var array|string[] */
    private $realfiles = [];
    /** @var array|\Psr\Http\Message\UploadedFileInterface[] */
    private $files;

    /** @var string */
    private $tmpPath;

    protected function setUp()
    {
        parent::setUp();

        $this->realfiles = [
            $this->createTestFile(),
            $this->createTestFile(),
        ];

        $this->files = array_map(function (string $file): UploadedFile {
            return new UploadedFile($file, basename($file.'.testing'));
        }, $this->realfiles);

        $this->tmpPath = $this->app->getContainer()->get('settings')['tmpReports'];
    }

    protected function tearDown()
    {
        foreach ($this->realfiles as $testfile) {
            @unlink($testfile);
        }

        foreach ($this->files as $testfile) {
            @unlink($this->tmpPath.$testfile->getClientFilename());
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
     * Test the case that the DMARC parser returns successful.
     *
     * It should return a success response and delete the temporary files.
     */
    public function testSuccessCase()
    {
        $request = $this->scaffoldRequest('POST', '/submit')
            ->withUploadedFiles(['reports' => $this->files]);

        /** @var DmarcAggregateParser|\PHPUnit_Framework_MockObject_MockObject $dmarcparserMock */
        $dmarcparserMock = $this->getMockBuilder(DmarcAggregateParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dmarcparserMock->method('parse')->willReturn(true);

        /** @var \Slim\Container $container */
        $container = $this->app->getContainer();
        $container['dmarcparser'] = $dmarcparserMock;

        $response = $this->processRequest($request);

        $this->assertEquals(200, $response->getStatusCode());

        foreach ($this->files as $file) {
            $this->assertFileNotExists($this->tmpPath.$file->getClientFilename());
        }
    }

    /**
     * Test the case that the DMARC parser fails.
     *
     * It should return a fail response, the errors and still delete the temporary files.
     */
    public function testErrorCase()
    {
        $request = $this->scaffoldRequest('POST', '/submit')
            ->withUploadedFiles(['reports' => $this->files]);

        /** @var DmarcAggregateParser|\PHPUnit_Framework_MockObject_MockObject $dmarcparserMock */
        $dmarcparserMock = $this->getMockBuilder(DmarcAggregateParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $errors = [
            'whoopsi',
        ];
        $dmarcparserMock->method('parse')->willReturn(false);
        $dmarcparserMock->method('get_errors')->willReturn($errors);

        /** @var \Slim\Container $container */
        $container = $this->app->getContainer();
        $container['dmarcparser'] = $dmarcparserMock;

        $response = $this->processRequest($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertSame($errors, json_decode($response->getBody())->data);

        foreach ($this->files as $file) {
            $this->assertFileNotExists($this->tmpPath.$file->getClientFilename());
        }
    }
}
