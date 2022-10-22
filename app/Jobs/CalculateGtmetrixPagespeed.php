<?php

namespace App\Jobs;

use App\Models\WebhookData;
use App\Services\Gtmetrix;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateGtmetrixPagespeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $webhookId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($webhookId)
    {
        $this->webhookId = $webhookId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // try {
            $webhook = WebhookData::findOrFail($this->webhookId);

            $data = json_decode($webhook->body, true);

            if ( !isset($data['url']) ) {
                throw new \Exception('Webhook data does not have a URL');
            }
            $url = $data['url'];

            $pageSpeedAudit = Gtmetrix::run($url, 'PERFORMANCE');
            $webhook->pageSpeedAudits()->attach($pageSpeedAudit->id);
        // } catch ( \Exception $e ) {

        // }
    }
}
