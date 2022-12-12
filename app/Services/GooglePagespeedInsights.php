<?php

namespace App\Services;

use Illuminate\Support\Str;
use Exception;
use GuzzleHttp\Client;
use Spatie\Lighthouse\Enums\Category;
use Spatie\Lighthouse\Lighthouse;
use App\Models\PageSpeedAudit;
use App\Models\PageSpeedAuditSource;
use App\Models\WebhookData;
use App\Services\PageSpeedAuditService;

class GooglePagespeedInsights
{

    public $category = [
        'ACCESSIBILITY',
        'BEST_PRACTICES',
        'PERFORMANCE',
        'PWA',
        'SEO',
    ];

    public $strategy = [
        'DESKTOP',
        'MOBILE',
    ];

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $category
     * @param string $strategy
     * @return mixed PageSpeedAudit|Exception
     */
    public static function run(
        string $url,
        string $category ='PERFORMANCE',
        string $strategy ='MOBILE'
    ) : PageSpeedAudit|Exception
    {

        set_time_limit(0);

        \Log::info('Starting Google Pagespeed Insights');

        $result = Lighthouse::url($url)
                        ->categories(Category::Performance)
                        ->run();

        \Log::info('Google Pagespeed Insights API success');

        // $lighthouse = $result->rawResults();
        // $normalized = PageSpeedAuditService::normalizeLighthouseData($lighthouse);


        $audits = $result->audits();
        $audits['finalUrl'] = $result->rawResults('lhr.finalUrl');

        $breakdown = [
            'speedIndex' => $result->audit('speed-index'),
            'firstContentfulPaint' => $result->audit('first-contentful-paint'),
            'largestContentfulPaint' => $result->audit('largest-contentful-paint'),
            'timeToInteractive' => $result->audit('interactive'),
            'totalBlockingTime' => $result->audit('total-blocking-time'),
            'cumulativeLayoutShift' => $result->audit('cumulative-layout-shift'),
        ];
        $audits['screenshots'] = [
            'final' => $result->rawResults('lhr.audits.final-screenshot.details'),
            'fullpage' => $result->rawResults('lhr.audits.full-page-screenshot.details.screenshot'),
            'thumbnails' => collect($result->rawResults('lhr.audits.screenshot-thumbnails.details'))->pluck('items')->all(),
        ];

        $pageSpeedAudit = new PageSpeedAudit;
        $pageSpeedAudit->audit_source = 'GOOGLE_PAGESPEED';
        $pageSpeedAudit->audit_type = 'PERFORMANCE';
        $pageSpeedAudit->device_type = 'MOBILE';
        $pageSpeedAudit->status = 'COMPLETE';
        $pageSpeedAudit->url = $url;
        $pageSpeedAudit->data_raw = $result->rawResults();
        $pageSpeedAudit->score = $result->scores()['performance'];
        $pageSpeedAudit->data_normalized = $result->scores();
        $pageSpeedAudit->save();

        \Log::info('Save Page Speed Audit to DB');

        return $pageSpeedAudit->refresh();







        $client = new Client([
            'base_uri' => 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed',
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'url' => $url,
                'category' => $category,
                'strategy' => $strategy,
                'key' => config('_pagespeed.google.key'),
            ],
        ]);
        $response = $client->request('GET');

        // $pageSpeedAudit = PageSpeedAudit::query()
        //                     ->where('audit_source', 'google_pagespeed')
        //                     ->where('audit_type', $category)
        //                     ->where('device_type', $strategy)
        //                     ->where('url', $url)
        //                     ->where('created_at', '>=', now()->subHour(1))
        //                     ->orderBy('created_at', 'desc')
        //                     // ->limit(1)
        //                     ->first();

        // if ( $pageSpeedAudit ) {
        //     return $pageSpeedAudit;
        // }

        // API body response
        $body = $response->getBody();

        // Convert JSON results to array
        $result = json_decode($body->getContents(), true);

        // Get Pagespeed Lighthouse from results
        $lighthouse = $result['lighthouseResult'];


        $normalized = PageSpeedAuditService::normalizeLighthouseData($lighthouse);
        $score = $lighthouse['categories']['performance']['score'];

        \Log::info('Google Pagespeed Insights API success');

        $pageSpeedAudit = new PageSpeedAudit;
        $pageSpeedAudit->audit_source = 'GOOGLE_PAGESPEED';
        $pageSpeedAudit->audit_type = $category;
        $pageSpeedAudit->device_type = $strategy;
        $pageSpeedAudit->status = 'COMPLETE';
        $pageSpeedAudit->url = $url;
        $pageSpeedAudit->data_raw = $result;
        $pageSpeedAudit->score = $score;
        $pageSpeedAudit->data_normalized = $normalized;
        $pageSpeedAudit->save();

        \Log::info('Save Page Speed Audit to DB');

        return $pageSpeedAudit->refresh();
    }

}
