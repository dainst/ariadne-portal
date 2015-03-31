<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\Vocabulary;
use Illuminate\Http\Request;
use App\Services\Utils;

class VocabularyController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Display a listing of the vocabularies.
     *
     * @return Response
     */
    public function index() {
        $vocabularies= Vocabulary::all();
        $providers = Utils::getProviders();
        return view('provider_data.vocabularies')->with('vocabularies', $vocabularies)->with('providers', $providers);
    }

    /**
     * Display the specified vocabulary.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $vocabulary = Vocabulary::get($id);
        //dd($metaSchema);
        return view('provider_data.vocabulary')->with('vocabulary', $vocabulary);
    }

}
