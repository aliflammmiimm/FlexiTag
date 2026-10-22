<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        return view('landing');
    }
}
