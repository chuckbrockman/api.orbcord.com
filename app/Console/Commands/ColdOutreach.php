<?php

namespace App\Console\Commands;

use App\Models\PageSpeedAudit;
use App\Models\WebhookData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ColdOutreach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outreach:pagespeed
        {url : Fully qualified URL}
        {visitors? : Monthly visitors}
        {aov? : AOV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Google Pagespeed for LinkedIn cold outreach';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $visitors = $this->argument('visitors') ?: 10000;
        $aov = $this->argument('aov') ?: 50;

        $webhookData = new WebhookData;
        $webhookData->source = 'PAGESPEED';
        $webhookData->referrer = 'console - cold outreach';
        $webhookData->body = json_encode([
            'url' => $this->argument('url'),
            'visitors_per_month' => $visitors,
            'average_order_value' => $aov,
        ]);
        $webhookData->save();

        Artisan::queue('pagespeed:lighthouse ' . $webhookData->id);

        // $pagespeedAudit = PageSpeedAudit::query()
        //                     ->join('page_speed_audit_webhook_data', 'page_speed_audits.id', '=', 'page_speed_audit_webhook_data.page_speed_audit_id')
        //                     ->where('page_speed_audit_webhook_data.webhook_data_id', $webhookData->id)
        //                     ->latest()
        //                     ->firstOrFail();

        // $this->info(print_r($pagespeedAudit->data_normalized, true));

        return Command::SUCCESS;
    }
}
