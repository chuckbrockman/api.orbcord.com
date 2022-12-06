<?php

namespace App\Console\Commands;

use App\Mail\SendGooglePageSpeedPerformanceImpactScore;
use App\Models\PageSpeedAudit;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmailPageSpeedLighthousePerformanceImpactScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pagespeed:email
                {pageSpeedAuditId : The ID of the page_speed_audit record}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Google Pagespeed Lighthouse Performance Impact Score';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Ge the page speed audit record
        $pageSpeedAudit = PageSpeedAudit::findOrFail($this->argument('pageSpeedAuditId'));

        // Check to make sure there's a an email in meta_data
        if ( !!!$pageSpeedAudit->meta_data['email'] ) {
            throw new \Exception('No email stored in the page speed audit meta data');
        }

        // Check to see if there's a report_filename in the page speed audit
        if ( !!!$pageSpeedAudit->report_filename ) {
            throw new \Exception('Google Page Speed Report File not set in database');
        }

        // Set path and file
        $reportFilename = 'lighthouse/' . $pageSpeedAudit->report_filename;

        // Check to make sure the file exists
        if ( !Storage::exists($reportFilename) ) {
            throw new \Exception('Google Page Speed Report File does not exists');
        }

        // Get the contents of the file
        $fileContents = Storage::disk('s3')->get($reportFilename);

        // Check to make sure it's JSON
        if ( !Str::isJson($fileContents) ) {
            throw new \Exception('Google Page Speed Report File contents is not JSON');
        }

        // Decode the file contents
        $data = json_decode($fileContents, true);
        $data = reset($data);

        // Send the email
        Mail::to(trim($pageSpeedAudit->meta_data['email']))
            ->send(new SendGooglePageSpeedPerformanceImpactScore($pageSpeedAudit, $data));

        return Command::SUCCESS;
    }
}
