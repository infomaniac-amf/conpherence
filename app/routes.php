<?php

App::fatal(function(Exception $exception)
{
    Log::error($exception);
    print_r($exception);
});

App::error(function(Exception $exception)
{
    Log::error($exception);
    print_r($exception->getTraceAsString());
});

Route::get('/', function()
{
	return View::make('master');
});

Route::filter('amf-response', function($route, $request, $response) {
    $response->header('Content-Type', 'application/x-amf');
});

Route::group(array('prefix' => 'amf', 'after' => 'amf-response'), function() {
    Route::controller('speakers', 'Conpherence\Controllers\SpeakerController');
});