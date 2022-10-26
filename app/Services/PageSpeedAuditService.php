<?php

namespace App\Services;

use Illuminate\Support\Str;
use Exception;
use App\Models\PageSpeedAudit;
use App\Models\PageSpeedAuditSource;
use App\Services\Helpers\Location;

class PageSpeedAuditService
{

    public static function normalizeLighthouseData($lighthouse)
    {
        // User Agent
        $userAgent = $lighthouse['userAgent'];

        // Overall score
        $score = $lighthouse['categories']['performance']['score'];

        // BEGIN: Screenshots
        $screenshots = [];
        $thumbnails = [];
        $screenshots['fullpage'] = null;
        if ( isset($lighthouse['audits']['full-page-screenshot']['details']['screenshot']['data']) ) {
            $screenshots['fullpage'] = $lighthouse['audits']['full-page-screenshot']['details']['screenshot']['data'];
        }

        $screenshots['thumbnails'] = [];
        if ( isset($lighthouse['audits']['screenshot-thumbnails']['details']['items']) ) {
            foreach ( $lighthouse['audits']['screenshot-thumbnails']['details']['items'] as $thumbnail ) {
                $thumbnails[] = $thumbnail['data'];
            }

            $screenshots['thumbnails'] = $thumbnails;
        }
        // END: Screenshots

        // BEGIN: Metrics
        $speedIndex = $lighthouse['audits']['speed-index'];;
        $firstContentfulPaint = $lighthouse['audits']['first-contentful-paint'];
        $largestContentfulPaint = $lighthouse['audits']['largest-contentful-paint'];
        $timeToInteractive = $lighthouse['audits']['interactive'];
        $totalBlockingTime = $lighthouse['audits']['total-blocking-time'];
        $cumulativeLayoutShift = $lighthouse['audits']['cumulative-layout-shift'];
        $breakdown = [
            'speedIndex' => [
                'title' => $speedIndex['title'],
                'description' => $speedIndex['description'],
                'score' => $speedIndex['score'],
                'displayValue' => $speedIndex['displayValue'],
                'numericValue' => $speedIndex['numericValue'],
            ],
            'firstContentfulPaint' => [
                'title' => $firstContentfulPaint['title'],
                'description' => $firstContentfulPaint['description'],
                'score' => $firstContentfulPaint['score'],
                'displayValue' => $firstContentfulPaint['displayValue'],
                'numericValue' => $firstContentfulPaint['numericValue'],
            ],
            'largestContentfulPaint' => [
                'title' => $largestContentfulPaint['title'],
                'description' => $largestContentfulPaint['description'],
                'score' => $largestContentfulPaint['score'],
                'displayValue' => $largestContentfulPaint['displayValue'],
                'numericValue' => $largestContentfulPaint['numericValue'],
            ],
            'timeToInteractive' => [
                'title' => $timeToInteractive['title'],
                'description' => $timeToInteractive['description'],
                'score' => $timeToInteractive['score'],
                'displayValue' => $timeToInteractive['displayValue'],
                'numericValue' => $timeToInteractive['numericValue'],
            ],
            'totalBlockingTime' => [
                'title' => $totalBlockingTime['title'],
                'description' => $totalBlockingTime['description'],
                'score' => $totalBlockingTime['score'],
                'displayValue' => $totalBlockingTime['displayValue'],
                'numericValue' => $totalBlockingTime['numericValue'],
            ],
            'cumulativeLayoutShift' => [
                'title' => $cumulativeLayoutShift['title'],
                'description' => $cumulativeLayoutShift['description'],
                'score' => $cumulativeLayoutShift['score'],
                'displayValue' => $cumulativeLayoutShift['displayValue'],
                'numericValue' => $cumulativeLayoutShift['numericValue'],
            ],
        ];

        return [
            'score' => $score,
            'screenshots' => $screenshots,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Parse Google Pagespeed Lighthouse and Save
     *
     * @param string $auditSource
     * @param string $auditType
     * @param string $url
     * @param string $deviceType
     * @param array $lighthouse
     * @return PageSpeedAudit|Exception
     */
    public static function parseAndSaveLighthouse(
        string $auditSource,
        string $auditType,
        string $url,
        string $deviceType,
        array $lighthouse,
        string $referrer = null
    ) : PageSpeedAudit|Exception
    {
        // User Agent
        $userAgent = $lighthouse['userAgent'];

        // Overall score
        $score = $lighthouse['categories']['performance']['score'];

        // BEGIN: Screenshots
        $screenshots = [];
        $thumbnails = [];
        $screenshots['fullpage'] = null;
        if ( isset($lighthouse['audits']['full-page-screenshot']['details']['screenshot']['data']) ) {
            $screenshots['fullpage'] = $lighthouse['audits']['full-page-screenshot']['details']['screenshot']['data'];
        }

        $screenshots['thumbnails'] = [];
        if ( isset($lighthouse['audits']['screenshot-thumbnails']['details']['items']) ) {
            foreach ( $lighthouse['audits']['screenshot-thumbnails']['details']['items'] as $thumbnail ) {
                $thumbnails[] = $thumbnail['data'];
            }

            $screenshots['thumbnails'] = $thumbnails;
        }
        // END: Screenshots

        // BEGIN: Metrics
        $speedIndex = $lighthouse['audits']['speed-index'];;
        $firstContentfulPaint = $lighthouse['audits']['first-contentful-paint'];
        $largestContentfulPaint = $lighthouse['audits']['largest-contentful-paint'];
        $timeToInteractive = $lighthouse['audits']['interactive'];
        $totalBlockingTime = $lighthouse['audits']['total-blocking-time'];
        $cumulativeLayoutShift = $lighthouse['audits']['cumulative-layout-shift'];
        $breakdown = [
            'speedIndex' => [
                'title' => $speedIndex['title'],
                'description' => $speedIndex['description'],
                'score' => $speedIndex['score'],
                'displayValue' => $speedIndex['displayValue'],
                'numericValue' => $speedIndex['numericValue'],
            ],
            'firstContentfulPaint' => [
                'title' => $firstContentfulPaint['title'],
                'description' => $firstContentfulPaint['description'],
                'score' => $firstContentfulPaint['score'],
                'displayValue' => $firstContentfulPaint['displayValue'],
                'numericValue' => $firstContentfulPaint['numericValue'],
            ],
            'largestContentfulPaint' => [
                'title' => $largestContentfulPaint['title'],
                'description' => $largestContentfulPaint['description'],
                'score' => $largestContentfulPaint['score'],
                'displayValue' => $largestContentfulPaint['displayValue'],
                'numericValue' => $largestContentfulPaint['numericValue'],
            ],
            'timeToInteractive' => [
                'title' => $timeToInteractive['title'],
                'description' => $timeToInteractive['description'],
                'score' => $timeToInteractive['score'],
                'displayValue' => $timeToInteractive['displayValue'],
                'numericValue' => $timeToInteractive['numericValue'],
            ],
            'totalBlockingTime' => [
                'title' => $totalBlockingTime['title'],
                'description' => $totalBlockingTime['description'],
                'score' => $totalBlockingTime['score'],
                'displayValue' => $totalBlockingTime['displayValue'],
                'numericValue' => $totalBlockingTime['numericValue'],
            ],
            'cumulativeLayoutShift' => [
                'title' => $cumulativeLayoutShift['title'],
                'description' => $cumulativeLayoutShift['description'],
                'score' => $cumulativeLayoutShift['score'],
                'displayValue' => $cumulativeLayoutShift['displayValue'],
                'numericValue' => $cumulativeLayoutShift['numericValue'],
            ],
        ];

        $normalized = [
            'score' => $score,
            'screenshots' => $screenshots,
            'breakdown' => $breakdown,
        ];

        // $source = PageSpeedAuditSource::where('code', 'google_pagespeed')->firstOrFail();
        // $pageSpeedAudit = PageSpeedAudit::insert([
        //     'audit_source' => $auditSource,
        //     'audit_type' => $auditType,
        //     'url' => $url,
        //     'device_type' => $deviceType,
        //     'data_raw' => json_encode($lighthouse),
        //     'data_normalized' => json_encode($normalized),

        // ]);

        $ip = Location::getClientIp();
        $referrer = ( !!$referrer ? parse_url($referrer) : [] );

        $pageSpeedAudit = new PageSpeedAudit;
        $pageSpeedAudit->audit_source = $auditSource;
        $pageSpeedAudit->audit_type = $auditType;
        $pageSpeedAudit->status = 'COMPLETE';
        $pageSpeedAudit->url = $url;
        $pageSpeedAudit->device_type = $deviceType;
        $pageSpeedAudit->score = $score;
        $pageSpeedAudit->data_raw = $lighthouse;
        $pageSpeedAudit->data_normalized = $normalized;
        $pageSpeedAudit->ip_address = $ip;
        $pageSpeedAudit->referrer = $referrer;
        $pageSpeedAudit->save();

        return $pageSpeedAudit;
    }

}
