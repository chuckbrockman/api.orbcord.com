<?php

namespace App\Http\Controllers\Api\Cfcontest;

use App\Mail\CfcontestCpra;
use App\Traits\ApiResponder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;

class CpraController extends Controller
{

    use ApiResponder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $uuid = (string) Str::orderedUuid();;

        $monolog = new Logger(env('APP_NAME'));
        $monolog->pushHandler(new RotatingFileHandler(storage_path('logs/cfcontests/' . $uuid . '.log')), 5, Logger::INFO);
        $monolog->info('---BEGIN: SUBMISSION----');
        $monolog->info('Headers: ', $request->header());
        $monolog->info('Data: ', $request->input());
        $monolog->info('---END: SUBMISSION----');

        try{
            $now = now();

            $validator = Validator::make(request()->all(), [
                'fname' => 'required',
                'lname' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'state' => 'required',
                // 'consumer' => 'required',
                'request' => 'required',
                // 'signature' => 'required',
                // 'date' => 'required|date_equals:' . $now->format('m/d/Y'),

            ], [
                'fname.required' => 'First name is required',
                'lname.required' => 'Last name is required',
                'email.required' => 'A valid email address is required',
                'email.email' => 'A valid email address is required',
                'phone.required' => 'Phone number is required',
                'state.required' => 'State is required',
                // 'consumer.required' => 'Requestor is required',
                'request.required' => 'Nature of Request is required',
                // 'signature.required' => 'Full name is required',
                // 'date.required' => 'You must date your submission',
                // 'date.date_equals' => 'You must date your submission with today\'s date',
            ]);

            if ( $validator->fails() ) {
                return $this->error(
                    'Please fix the following:',
                    400,
                    $validator->errors()
                );
            }

            $body = file_get_contents("php://input");

            $referrer = request()->headers->get('referer');

            $to = 'cbrockman@vardapartners.com';

            if ( $request->has('override_email') ) {
                $to = $request->input('override_email');
            }

            $id = \DB::connection('mysql_data')
                ->table('cfcontests_form_data')
                ->insertGetId([
                    'uuid' => $uuid,
                    'headers' => json_encode($request->header()),
                    'form_data' => json_encode($request->input()),
                    'created_at' => now(),
                ]);

            Mail::to($to)
                ->send(new CfcontestCpra($uuid, $referrer));

            return $this->success(null, 'Your request has been processed successfully');
        } catch ( \Exception $e ) {
            return $this->error(
                'There was an error processing your request',
                400,
                $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
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
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(404);
    }
}
