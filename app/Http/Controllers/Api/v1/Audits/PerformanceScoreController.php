<?php

namespace App\Http\Controllers\Api\v1\Audits;

use App\Jobs\CalculateGooglePagespeed;
use App\Jobs\CalculateGtmetrixPagespeed;
use App\Models\WebhookData;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PerformanceScoreController extends Controller
{
    use ApiResponder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'hi';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $body = file_get_contents("php://input");

        $webhookData = new WebhookData;
        $webhookData->source = 'PAGESPEED';
        $webhookData->referrer = request()->headers->get('referer');
        $webhookData->body = $body;
        $webhookData->save();

        // Start tests
        Artisan::queue('pagespeed:lighthouse ' . $webhookData->id . ' true --queue');
        // CalculateGooglePagespeed::dispatch($webhookData->id);
        // CalculateGtmetrixPagespeed::dispatch($webhookData->id);

        return $this->success([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
