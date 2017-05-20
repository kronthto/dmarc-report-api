<?php

namespace App;

use Slim\Http\Request;
use Slim\Http\Response;

class SubmitAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        /** @var array|\Slim\Http\UploadedFile[] $reportFiles */
        $reportFiles = $request->getUploadedFiles()['reports'];

        $dmarcFiles = [];

        $tmpReportsDir = $this->ci->get('settings')['tmpReports'];

        foreach ($reportFiles as $reportFile) {
            $fullPath = $tmpReportsDir.basename($reportFile->getClientFilename());

            $reportFile->moveTo($fullPath);
            $dmarcFiles[] = $fullPath;

            $this->fixEncodingIssues($fullPath);
        }

        /** @var \Solaris\DmarcAggregateParser $dmarcParser */
        $dmarcParser = $this->ci->get('dmarcparser');

        $parseResult = $dmarcParser->parse($dmarcFiles);

        foreach ($dmarcFiles as $dmarcFile) {
            unlink($dmarcFile);
        }

        if (!$parseResult) {
            return $response->withJson([
                'status' => 'fail',
                'data' => $dmarcParser->get_errors(),
            ], 400);
        }

        return $response->withJson(['status' => 'success']);
    }

    /**
     * Determines whether given data is b64 encoded.
     *
     * @param $data
     *
     * @return bool
     *
     * @see http://stackoverflow.com/a/34982057/7362396
     */
    public static function is_base64_encoded($data)
    {
        return preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data) === 1;
    }

    /**
     * Checks for and fixes certain encoding issues that can happen to the reports in the mails.
     *
     * @param string $fullPath
     */
    public static function fixEncodingIssues(string $fullPath)
    {
        $content = file_get_contents($fullPath);
        $contentNewlinesStripped = preg_replace('/\s+/', '', $content);

        if (static::is_base64_encoded($contentNewlinesStripped)) {
            file_put_contents($fullPath, base64_decode($contentNewlinesStripped));
        }
    }
}
