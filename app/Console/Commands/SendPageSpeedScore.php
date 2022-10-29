<?php

namespace App\Console\Commands;

use App\Mail\SendPerfomanceScoreResult;
use App\Models\PageSpeedAudit;
use App\Models\WebhookData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPageSpeedScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pagespeed-score:send {webhookDataId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Page Speed Score';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $webhook = WebhookData::query()
                    ->select('webhook_data.*', 'page_speed_audit_webhook_data.page_speed_audit_id')
                    ->join('page_speed_audit_webhook_data', 'page_speed_audit_webhook_data.webhook_data_id', '=', 'webhook_data.id')
                    ->where('page_speed_audit_webhook_data.webhook_data_id', $this->argument('webhookDataId'))
                    ->orderBy('page_speed_audit_webhook_data.webhook_data_id', 'DESC')
                    ->first();

        $pageSpeedAudit = PageSpeedAudit::findOrFail($webhook->page_speed_audit_id);


        // $this->info(print_r($pageSpeedAudit, true));die();

        $body = json_decode($webhook->body, true);


        if ( !isset($body['email']) || !!!$body['email'] ) {

        }


        Mail::to(trim($body['email']))
            ->send(new SendPerfomanceScoreResult($pageSpeedAudit, $body));

        return Command::SUCCESS;
    }
}
