<?php

namespace App\Console\Commands;


use Spatie\Lighthouse\Enums\Category;
use Spatie\Lighthouse\Enums\FormFactor;
use Spatie\Lighthouse\Lighthouse;
use App\Jobs\CalculateLightspeedScore;
use App\Models\PageSpeedAudit;
use App\Models\WebhookData;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PageSpeedLighthouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pagespeed:lighthouse
            {webhookDataId : The ID of the webhook_data record}
            {sendEmail? : Send an email with the score}
            {--queue : Whether the job should be queued}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Google Pagespeed Lighthouse Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('--START PAGESPEED LIGHTHOUSE--');

        $webhook = WebhookData::findOrFail($this->argument('webhookDataId'));

        Log::info('Webhook Data ID: ' . $webhook->id);

        // Get form data from webhook
        $data = json_decode($webhook->body, true);

        // If there's no URL in the form data, throw an error
        if ( !isset($data['url']) ) {
            throw new \Exception('Webhook data does not have a URL');
        }

        // Set the
        $url = $data['url'];

        Log::info($url);

        // Get Lighthouse results
        $result = Lighthouse::url($url)
                    ->categories(Category::Performance)
                    ->formFactor(FormFactor::Mobile)
                    ->throttleCpu(0)
                    // ->throttleNetwork()
                    ->run();

        // Save report audit results as file since it's a large amount of data
        $reportFilename = (string) Str::orderedUuid() . '.json';
        // Storage::put('lighthouse/' . $reportFilename, json_encode($result->rawResults('report')));

        // Upload to Backblaze B2
        Log::info('Uploading file to B2: ' . $reportFilename);
        Storage::disk('s3')->put(
            config('_pagespeed.bucket') . '/' . $reportFilename,
            json_encode($result->rawResults('report'))
        );

        // $audits = $result->audits();
        // $audits['finalUrl'] = $result->rawResults('lhr.finalUrl');

        // Normalize the data to reduce db storage
        $dataNormalized = [
            'score' => $result->rawResults('lhr.categories.performance.score'),
            'breakdown' => [
                'speedIndex' => $result->audit('speed-index'),
                'firstContentfulPaint' => $result->audit('first-contentful-paint'),
                'largestContentfulPaint' => $result->audit('largest-contentful-paint'),
                'timeToInteractive' => $result->audit('interactive'),
                'totalBlockingTime' => $result->audit('total-blocking-time'),
                'cumulativeLayoutShift' => $result->audit('cumulative-layout-shift'),
            ],
            'screenshots' => [
                'final' => $result->rawResults('lhr.audits.final-screenshot.details'),
                'fullpage' => $result->rawResults('lhr.audits.full-page-screenshot.details.screenshot'),
                'thumbnails' => collect($result->rawResults('lhr.audits.screenshot-thumbnails.details'))->pluck('items')->all(),
            ],
        ];

        // $this->info(print_r($result->rawResults('lhr.audits.full-page-screenshot.details.screenshot'), true));
        // $this->info(print_r($result->audit('first-contentful-paint'), true));
        // $this->info(print_r($result->rawResults('lhr.userAgent'), true));
        // $this->info(print_r($result->rawResults('lhr.finalUrl'), true));
        // $this->info(print_r($result->rawResults('lhr.id'), true));
        // $this->info(print_r(is_array($result->audits()), true));
        // \Log::info(print_r($audits, true));

        // Set the meta data from the webhook data
        $visitorsPerMonth = (int) ( isset($data['visitors_per_month']) && !!$data['visitors_per_month'] ? preg_replace('/[^\d.]/', '', $data['visitors_per_month']) :  1000 );
        $averageOrderValue = (int) ( isset($data['average_order_value']) && !!$data['average_order_value'] ? preg_replace('/[^\d.]/', '', $data['average_order_value']) : 100 );
        $email = ( isset($data['email']) && !!$data['email'] ? $data['email'] : null );

        // Add meta data from the webhook
        $metaData = [
            'url' => $url,
            'email' => $email,
            'visitorsPerMonth' => $visitorsPerMonth,
            'averageOrderValue' => $averageOrderValue,
        ];


        Log::info('Saving Page Speed Audit to DB');
        // Create a page speed audit entry
        $pageSpeedAudit = new PageSpeedAudit;
        $pageSpeedAudit->audit_source = 'GOOGLE_PAGESPEED';
        $pageSpeedAudit->audit_type = 'PERFORMANCE';
        $pageSpeedAudit->device_type = 'MOBILE';
        $pageSpeedAudit->status = 'COMPLETE';
        $pageSpeedAudit->url = $url;
        $pageSpeedAudit->report_filename = $reportFilename;
        $pageSpeedAudit->meta_data = $metaData;
        $pageSpeedAudit->score = $result->scores()['performance'];
        $pageSpeedAudit->data_normalized = $dataNormalized;
        $webhook->pageSpeedAudits()->save($pageSpeedAudit);

        $pageSpeedAudit->refresh();

        // Log::info('Associating Page Speed Audit with the Webhook');
        // Associate the page speed audit record with the webhook
        // $webhook->pageSpeedAudits()->attach($pageSpeedAudit->id);

        // Send the email if requested
        if ( !!$this->argument('sendEmail') ) {
            Log::info('Send email requested');

            if ( !!$email ) {
                sleep(10);
                Log::info('Sending Email');
                Artisan::call('pagespeed:email ' . $pageSpeedAudit->id);
            } else {
                Log::info('No email set');
            }
        }

        Log::info('--END PAGESPEED LIGHTHOUSE--');

        return Command::SUCCESS;
    }
}
