<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BeforeApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // $apiKey = $request->route('apiKey');

        // // No site api key, no access
        // if ( !!!$apiKey ) {
        //     Log::info('API key not provided');
        //     abort(401, 'API key is required.');
        // }

        // $referrer = request()->server('HTTP_ORIGIN');
        // if ( !!!$referrer || $referrer == 'null' ) {
        //     $referrer = request()->server('HTTP_REFERER');
        // }
        // $fullReferrer = $referrer;
        // $referrer = preg_replace("(^https?://)", "", $referrer );

        // // Get the site by the api key
        // $project = cache()->remember($apiKey, \Carbon\Carbon::now()->addMinutes(5), function () use($apiKey) {
        //     return  DB::table('projects')
        //         ->where('api_key', $apiKey)
        //         ->first();
        // });

        // // If no site, or it's not active, no go
        // if ( !!!$project || !!!$project->is_active ) {
        //     Log::info('site not found or not active');
        //     abort(401, 'Site is not valid: ' . $referrer);
        // }

        // $referringSite = cache()->remember($apiKey . $referrer, \Carbon\Carbon::now()->addMinutes(5), function () use($project, $referrer) {
        //     return  DB::table('sites')
        //         ->where('project_id', $project->id)
        //         ->where('domain', $referrer)
        //         ->first();
        // });

        // if ( !!!$referringSite ) {
        //     abort(401, $referrer . ' is not valid for site');
        // }

        // $request->merge([
        //     'project' => $project,
        // ]);


        // $parsedUrl = parse_url($fullReferrer);

        // return $next($request)
        //     ->header('Access-Control-Allow-Origin', $parsedUrl['scheme'] .'://'.$referringSite->domain)
        //     ->header('Access-Control-Allow-Methods', 'GET, POST');
    }
}
