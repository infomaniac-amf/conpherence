<?php

use Conpherence\Entities\Base\BaseEntity;
use Illuminate\Http\Response;

amf_set_classmapping_callback(function($object) {

    // if the object is a Doctrine entity, check the metadata for the classname,
    // as the instance might be a proxy object
    if($object instanceof BaseEntity) {
        $metadataFactory = $object->getEntityManager()->getMetadataFactory();
        return $metadataFactory->getMetadataFor(get_class($object))->getName();
    }

    //otherwise, return the normal class name
    return get_class($object);
});

Route::get('/', function()
{
	return View::make('master');
});

Route::filter('amf-response', function($route, $request, Response $response) {
    $response->header('Content-Type', 'application/x-amf');
});

Route::group(array('prefix' => 'amf', 'after' => 'amf-response'), function() {
    Route::controller('speakers', 'Conpherence\Controllers\SpeakerController');
    Route::controller('events', 'Conpherence\Controllers\EventController');
    Route::controller('countries', 'Conpherence\Controllers\CountriesController');
});

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