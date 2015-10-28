<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Services\Provider;

class PageController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Welcome Controller
      |--------------------------------------------------------------------------
      |
      | This controller renders the "marketing page" for the application and
      | is configured to only allow guests. Like most of the other sample
      | controllers, you are free to modify or remove it as you desire.
      |
     */


    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function welcome() {
        $providers = Provider::statistics();
        //dd($providers);
        return view('page.welcome')->with('providers', $providers);
    }
    
    /**
     * Show the about page
     *
     * @return Response
     */
    public function about()
    {
        return view('page.about');
    }    
}