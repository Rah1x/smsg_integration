<?php
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'api';
Route::group([
    'prefix' => 'v1',
    'namespace' => $namespace,
    'middleware' => ['api', 'throttle:100,3'],
],
function() use($namespace) {
    Route::get('ping', ['as'=>"{$namespace}.ping", 'uses'=>'ping@pong']); //testing connection, disable when not needed
    Route::get('message', ['as'=>"{$namespace}.get-messages", 'uses'=>'messages@get']);
    Route::post('message', ['as'=>"{$namespace}.post-message", 'uses'=>'messages@post']);
});


//-----------------------------------------------------------------------------------
/** default Route fallback for 404 */
Route::fallback(function() {
    return response()->json(['error' => 'not found!'], 404);
});