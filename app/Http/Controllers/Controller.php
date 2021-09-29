<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $GET=[], $POST=[];

    function __construct()
    {
        #/ identify and set incoming requests
        if(\Request::isMethod('post'))
        {
            $req_all = \Request::instance()->request->all();

            $this->POST = $req_all; //Request::input(); will pollute POST with GET
            $this->GET = $_GET; //laravel is ignoring GET if there is POST
        }

        if(\Request::isMethod('get'))
        {
            $this->GET = \Request::input();
        }
    }
}
