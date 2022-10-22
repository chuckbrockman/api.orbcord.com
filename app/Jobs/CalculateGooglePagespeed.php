<?php

namespace App\Jobs;

use Log;
use App\Models\WebhookData;
use App\Services\GooglePagespeedInsights;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

            // $strategies = ['MOBILE','DESKTOP'];
            $strategies = ['MOBILE'];
            foreach ( $strategies as $strategy ) {
                \Log::info('Strategy: ' . $strategy);

                $pageSpeedAudit = GooglePagespeedInsights::run($url, 'PERFORMANCE', $strategy);

                \Log::info('pageSpeedAudit id: ' . $pageSpeedAudit->id);

                if ( $webhook->has('pageSpeedAudits')->count() === 0 ) {
                    $webhook->pageSpeedAudits()->attach($pageSpeedAudit->id);
                }

                sleep(5);
            }


        // } catch ( \Exception $e ) {

        // }

        return;

    }
}