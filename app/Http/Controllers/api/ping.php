<?php
namespace App\Http\Controllers\api;

#/ Core
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ping extends Controller
{
    function __construct(Request $request)
    {
        //die('1'); //test connection
        parent::__construct();
    }

    ///////////////////////////////////////////////////////////////// PUBLIC Methods below

    public function pong()
    {
        return response()
        ->json(['ping'=>'pong'], 200)
        ->withHeaders([
            'Content-Type'=>'application/json',
        ]);
    }
}
?>