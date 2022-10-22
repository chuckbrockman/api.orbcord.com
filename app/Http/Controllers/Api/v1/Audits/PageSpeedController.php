<?php

namespace App\Http\Controllers\Api\v1\Audits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GooglePagespeedInsights;
use App\Traits\ApiResponder;

class PageSpeedController extends Controller
{

    use ApiResponder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            'url' => 'required|url',
        ], [
            'url.required' => 'A valid URL is required. Ex: (http(s)://example.com',
            'url.url' => 'A valid URL is required. Ex: (http(s)://example.com',
        ]);

        if ( $validator->fails() ) {
            return $this->error(
                'Fix the following to complete a PageSpeed Audit:',
                400,
                $validator->errors()
            );
        }

        // Make sure URL ends with trailing "/"
        $url = rtrim(request()->input('url'), '/') . '/';



        $audit = GooglePagespeedInsights::run($url, 'PERFORMANCE');

        return $this->success($audit->data_nomralized);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
