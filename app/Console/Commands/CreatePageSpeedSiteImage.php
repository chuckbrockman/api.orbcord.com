<?php

namespace App\Console\Commands;

use App\Models\PageSpeedAudit;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;


class CreatePageSpeedSiteImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outreach:image
            {pageSpeedAuditId : The ID of the page_speed_audit record}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Ge the page speed audit record
        $pageSpeedAudit = PageSpeedAudit::findOrFail($this->argument('pageSpeedAuditId'));

        $lcpNumericValue =  (float) $pageSpeedAudit->data_normalized['breakdown']['largestContentfulPaint']['numericValue'] / 1000;

        $visitorsPerMonth = $pageSpeedAudit->meta_data['visitorsPerMonth'] ?: 100000;
        $averageOrderValue = $pageSpeedAudit->meta_data['averageOrderValue'] ?: 50;
        $onePercentRevenue = ($visitorsPerMonth * $averageOrderValue) * 0.01;

        // Create blank canvas
        $canvas = Image::canvas(1920, 1080);

        // Create image from fullpage screenshot
        $fullpageScreenshot = $pageSpeedAudit->data_normalized['screenshots']['fullpage'];
        $image = Image::make($fullpageScreenshot['data']);
        // $image->rectangle(0, $image->height()-100, $image->width(), $image->height(), function ($draw) {
        //     $draw->background('rgba(255, 255, 255, 0.85)');
        // });

        // dd(($canvas->width()-$image->width()));

        // Draw a rectangle to hold text
        $rectangle = Image::canvas(($canvas->width()-$image->width()), 430);
        // $rectangle->rectangle(0, 0, ($canvas->width()-$image->width()), 300, function ($draw) {
        //     $draw->background('rgba(255, 255, 255, 0.5)');
        //     $draw->border(2, '#000');
        // });

        $fontSize = 36;



        // LCP
        $rectangle->text(number_format($lcpNumericValue, 2) . ' Seconds', $rectangle->width()/2, 36, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Bold.ttf'));
            $font->size(48);
            $font->color([0, 0, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // URL
        $rectangle->text('to load', $rectangle->width()/2, 90, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Regular.ttf'));
            $font->size(36);
            $font->color([0, 0, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // URL
        $rectangle->text($pageSpeedAudit->url, $rectangle->width()/2, 138, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Regular.ttf'));
            $font->size(36);
            $font->color([0, 0, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // Monthly Visitors
        $rectangle->text('If you have ' . number_format($visitorsPerMonth, 0, ',') . ' vistors a month', $rectangle->width()/2, 232, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Regular.ttf'));
            $font->size(36);
            $font->color([0, 0, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // Average Order Value
        $rectangle->text('an Average Order Value of $' . number_format($averageOrderValue, 0, ','), $rectangle->width()/2, 280, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Regular.ttf'));
            $font->size(36);
            $font->color([0, 0, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // 1 second text
        $rectangle->text('A 1 second decrease in page load time could be worth', $rectangle->width()/2, 328, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Regular.ttf'));
            $font->size(36);
            $font->color([0, 0, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // 1 second dollar amount
        $rectangle->text('$' . number_format($onePercentRevenue, 0, ','), $rectangle->width()/2, 390, function($font) {
            $font->file(storage_path('fonts/Montserrat/static/Montserrat-Bold.ttf'));
            $font->size(60);
            $font->color([0, 128, 0, 1]);
            $font->align('center');
            $font->valign('middle');
        });

        // Merge with canvas
        $canvas->insert($image);
        $canvas->insert($rectangle, 'center-right');

        // Save images
        $canvas->save(storage_path('app/lighthouse/foo.jpg'), 80);

        // \Storage::disk('dropbox')->put('Orbcord Inc./Performance Impact Score/' . rtrim(implode('_', parse_url($pageSpeedAudit->url)), '/_') . '_' . time() . '.jpg', $canvas->encode('jpg')->stream());


        return Command::SUCCESS;
    }
}
