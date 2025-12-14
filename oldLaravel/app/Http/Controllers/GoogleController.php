<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\EstimateKeywordTraffic;


class GoogleController extends Controller{
    public function estimateVolume(){
        $estimateKeywordTraffic = new EstimateKeywordTraffic();

    }
}