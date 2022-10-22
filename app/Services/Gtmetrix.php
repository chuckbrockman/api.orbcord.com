<?php

namespace App\Services;

use App\Models\PageSpeedAudit;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


class Gtmetrix
{


    public static function run(
        string $url,
        string $category ='PERFORMANCE',
        string $strategy ='MOBILE'
    )
    {
        set_time_limit(0);

        // $response = Http::withBasicAuth(config('_gtmetrix.api.key'), '')
        //         ->get('https://gtmetrix.com/api/2.0/browsers');

        // $response = Http::withBasicAuth(config('_gtmetrix.api.key'), '')
        //         ->get('https://gtmetrix.com/api/2.0/tests/thzdjDzA');

        // $response = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
        //         ->get('https://gtmetrix.com/api/2.0/reports/g91YKpMj');

        // $response = Http::withBasicAuth(config('_gtmetrix.api.key'), '')
        //         ->get('https://gtmetrix.com/api/2.0/reports/JBMu2gzc/resources/lighthouse.json');


        // $response = Http::withBasicAuth(config('_gtmetrix.api.key'), '')
        //         ->get('https://gtmetrix.com/reports/tcecleaning.com/YBLxO7W0/');

        // dd(json_decode($response->body(), true));


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
        $pageSpeedAudit->audit_source = 'gtmetrix';
        $pageSpeedAudit->audit_type = 'PERFORMANCE';
        $pageSpeedAudit->device_type = 'MOBILE';
        $pageSpeedAudit->status = 'QUEUED';
        $pageSpeedAudit->url = $url;
        $pageSpeedAudit->data_raw = $createResult;
        $pageSpeedAudit->save();

        $testUrl = $createResult['links']['self'];

        \Log::info(print_r($createResult, true));




        // Get the test
        $test = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                    ->get($testUrl);
        $testResult = json_decode($test->body(), true);
        $testType =  $testResult['data']['type'];

        \Log::info(print_r($testResult, true));

        // If the test is not a report (still queued for processing), wait for it to complete before proceeding
        while( $testType !== 'report' ) {
            $test = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                    ->get($testUrl);
            $testResult = json_decode($test->body(), true);
            $testType =  $testResult['data']['type'];

            \Log::info('while');
            \Log::info(print_r($testResult, true));

            sleep(5);
        }



        $reportUrl = $testResult['data']['links']['report_url'];
        $lighthouseUrl = $testResult['data']['links']['lighthouse'];

        // Get the report for the test
        $report = Http::withBasicAuth(config('_pagespeed.gtmetrix.key'), '')
                    ->get($reportUrl);
        $reportResult = json_decode($report->body(), true);

        \Log::info(print_r($reportResult, true));

        // Get the lighthouse data
        $response = Http::withBasicAuth(config('_gtmetrix.api.key'), '')
                ->get($lighthouseUrl);


        // Convert JSON results to array
        $lighthouse = json_decode($response->body(), true);

        $normalized = PageSpeedAuditService::normalizeLighthouseData($lighthouse);

        $score = $lighthouse['categories']['performance']['score'];
        $normalized['_gtmetrixScore'] = $lighthouse['_gtmetrixScore'];

        $pageSpeedAudit->status = 'COMPLETE';
        $pageSpeedAudit->data_raw = $test->body();
        $pageSpeedAudit->save();
        $pageSpeedAudit->score = $score;
        $pageSpeedAudit->data_normalized = $normalized;
        $pageSpeedAudit->save();

        return $pageSpeedAudit->refresh();
    }

}
