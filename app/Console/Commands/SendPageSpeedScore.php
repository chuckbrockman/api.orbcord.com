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
    protected $signature = 'pagespeed-score:send {pageSpeedAuditId}';

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
        $pageSpeedAudit = PageSpeedAudit::find($this->argument('pageSpeedAuditId'));
        $webhook = WebhookData::query()
                    ->join('page_speed_audit_webhook_data', 'page_speed_audit_webhook_data.webhook_data_id', '=', 'webhook_data.id')
                    ->where('page_speed_audit_webhook_data.page_speed_audit_id', $this->argument('pageSpeedAuditId'))
                    ->orderBy('page_speed_audit_webhook_data.webhook_data_id', 'DESC')
                    ->first();

        $body = json_decode($webhook->body, true);

        $ordersPerMonth = $body['orders_per_month'] ?: 1000;
        $averageOrderValue = $body['average_order_value'] ?: 50;


        $lcpDisplayValue =  $pageSpeedAudit->data_normalized['breakdown']['largestContentfulPaint']['displayValue'];
        $lcpNumericValue =  (float) $pageSpeedAudit->data_normalized['breakdown']['largestContentfulPaint']['numericValue'] / 1000;

        number_format($pageSpeedAudit->score * 100);

        (float) $conversionRate = 3;
        if ( $lcpNumericValue > 1 && $lcpNumericValue <= 2.4 ) {
            $conversionRate = 1.9;
        } elseif ( $lcpNumericValue > 2.4 && $lcpNumericValue <= 3.3) {
            $conversionRate = 1.5;
        } elseif ( $lcpNumericValue > 3.3 && $lcpNumericValue <= 4.2) {
            $conversionRate = 0.95;
        } elseif ( $lcpNumericValue > 4.2 && $lcpNumericValue <= 5) {
            $conversionRate = 0.6;
        } else {
            // $conversionRate = 0.6 - ((( $lcpNumericValue - 5 ) * 0.02) * 0.6);
            $conversionRate = 0.6 - ((( $lcpNumericValue - 5 ) * 0.0442) * 0.6);
        }

        // if ( $lcpNumericValue > 1 && $lcpNumericValue <= 2 ) {
        //     $conversionRate = 1.28;
        // } elseif ( $lcpNumericValue > 2 && $lcpNumericValue <= 3 ) {
        //     $conversionRate = 1.12;
        // } elseif ( $lcpNumericValue > 3 && $lcpNumericValue <= 4 ) {
        //     $conversionRate = 0.67;
        // } elseif ( $lcpNumericValue > 4 ) {
        //     // $conversionRate = 0.67 - ((( $lcpNumericValue - 5 ) * 0.003) * 0.67);
        //     $conversionRate = 0.67 - ((( $lcpNumericValue - 5 ) * 0.0442) * 0.67);
        // }

        $bestCaseRevenue = ($ordersPerMonth * $averageOrderValue) * .0305;
        $estimatedRevenue = ($ordersPerMonth * $averageOrderValue) * $conversionRate/100;
        $difference = ($bestCaseRevenue - $estimatedRevenue);

        $this->info($lcpDisplayValue);
        $this->info($lcpNumericValue);
        $this->info($conversionRate/100);
        $this->info($bestCaseRevenue);
        $this->info($estimatedRevenue);
        $this->info($difference);
die();
        Mail::to('chuck@chuckbrockman.com')
            ->send(new SendPerfomanceScoreResult($pageSpeedAudit));

        return Command::SUCCESS;
    }
}
