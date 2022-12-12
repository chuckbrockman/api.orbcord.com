<?php

namespace App\Jobs;

use Spatie\Lighthouse\Enums\Category;
use Spatie\Lighthouse\Lighthouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateLightspeedScore implements ShouldQueue
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

            $webhook = WebhookData::findOrFail($this->webhookDataId);

            $data = json_decode($webhook->body, true);

            if ( !isset($data['url']) ) {
                throw new \Exception('Webhook data does not have a URL');
            }
            $url = $data['url'];

            \Log::info('--START--');

            $result = Lighthouse::url($url);

            \Log::info(print_r($result, true));

            \Log::info('--END--');


    }
}
