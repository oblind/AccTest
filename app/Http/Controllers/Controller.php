<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //use AuthorizesRequests;
    public function __construct()
    {
        $this->middleware('auth');
    }
}
