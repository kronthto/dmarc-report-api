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
}
