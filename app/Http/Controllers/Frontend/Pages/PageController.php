<?php

namespace App\Http\Controllers\Frontend\Pages;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        /**
         * Cache as static page
         */
        if ( App::environment('production') ) {
            $this->middleware('page-cache');
        }
    }


}
