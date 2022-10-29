<?php

namespace App\Jobs;

use Log;
use App\Console\Commands\SendPageSpeedScore;
use App\Models\WebhookData;
use App\Services\GooglePagespeedInsights;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class CalculateGooglePagespeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var int
     */
    private $webhookDataId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($webhookDataId)
    {
        $this->webhookDataId = $webhookDataId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // try {

            // \Log::info($this->webhookDataId);

            $webhook = WebhookData::findOrFail($this->webhookDataId);

            $data = json_decode($webhook->body, true);

            if ( !isset($data['url']) ) {
                throw new \Exception('Webhook data does not have a URL');
            }
            $url = $data['url'];

            \Log::info('--START--');

            \Log::info('WebhoodData id: ' . $webhook->id);
            $pageSpeedAudit = GooglePagespeedInsights::run($url);

            \Log::info('Page Speed Audit id: ' . $pageSpeedAudit->id);
            $webhook->pageSpeedAudits()->attach($pageSpeedAudit->id);

            \Log::info('Send Email ');
            Artisan::call('pagespeed-score:send ' . $webhook->id);

            \Log::info('--END--');

        // } catch ( \Exception $e ) {

        // }

        return;

    }
}
