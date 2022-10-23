<?php

namespace App\Services;

use App\Models\PageSpeedAudit;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


class GtmetrixService
{

    public static function run(
        string $url,
        string $category ='PERFORMANCE',
        string $strategy ='MOBILE'
    )
    {
        $testUrl = self::createTest($url);

        $test = self::getTest($testUrl);
    }

    public static function createTest($url)
    {
        // Create the GTMetrix test
        $create = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                        ->withHeaders([
                            'Content-Type' => 'application/vnd.api+json'
                        ])
                        ->post('https://gtmetrix.com/api/2.0/tests', [
                            'data' => [
                                'type' => 'test',
                                'attributes' => [
                                    'url' => $url,
                                    'location' => 4,
                                    'browser' =>  3,
                                ]
                            ]
        ]);

        $createResult = json_decode($create->body(), true);

        $pageSpeedAudit = new PageSpeedAudit;
        $pageSpeedAudit->status = 'QUEUED';
        $pageSpeedAudit->url = $url;
        $pageSpeedAudit->data_raw = $createResult;
        $pageSpeedAudit->save();

        $testUrl = $createResult['links']['self'];

        \Log::info(print_r($createResult, true));

        $test = self::getTest($testUrl, $pageSpeedAudit);
    }

    public static function getTest(
        string $url,
        \App\Models\PageSpeedAudit $pageSpeedAudit
    )
    {
        // Get the test
        $test = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                    ->get($url);
        $testResult = json_decode($test->body(), true);
        $testType =  $testResult['data']['type'];

        \Log::info(print_r($testResult, true));

        // If the test is not a report (still queued for processing), wait for it to complete before proceeding
        while( $testType !== 'report' ) {
            $test = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                    ->get($url);
            $testResult = json_decode($test->body(), true);
            $testType =  $testResult['data']['type'];

            if ( $testType == 'report' ) {
                // Save data
                $pageSpeedAudit->status = 'COMPLETE';
                $pageSpeedAudit->data_raw = $test->body();
                $pageSpeedAudit->save();

                $lighthouseUrl = $testResult['data']['links']['lighthouse'];

                return $testResult;
            }

            \Log::info('while');
            \Log::info(print_r($testResult, true));

            sleep(5);
        }
    }

    public static function getReport($url)
    {
        $report = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                    ->get($url);

        return json_decode($report->body(), true);
    }

    public static function getLighthouse($url)
    {
        // Get the lighthouse data
        $response = Http::withBasicAuth(config('_gtmetrix.api.key'), '')
                ->get($url);


        // Convert JSON results to array
        return json_decode($response->body(), true);
    }

}
