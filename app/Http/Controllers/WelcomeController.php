<?php

namespace App\Http\Controllers;

use App\CinemaInfo;
use App\DegreeSatisfaction;
use App\QualityStandard;
use App\ResponseRate;
use App\SlaCoincidenceRate;
use App\WorkOrder;

class WelcomeController extends Controller
{
    public function index(){
        return view('welcome');
    }
}
