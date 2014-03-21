<?php

use Conpherence\Entities\Event;
use Conpherence\Entities\Session;
use Conpherence\Entities\Speaker;

App::fatal(function($exception)
{
    print_r($exception);

});

App::error(function($exception)
{
    print_r($exception);

});

Route::get('/', function()
{
	return View::make('master');
});

Route::post('/greeting', function()
{
    $request = Request::instance();
    $response = Response::make(amf_encode(amf_decode($request->getContent())));
    $response->header('Content-Type', 'application/x-amf');

    $session = new Session();
    $session->setTitle("hello");
    $session->setDate(new DateTime());

    $speaker = new Speaker();

    $speaker->getEntityManager();
    $speaker->setName('Danny Kopping');
    $speaker->setCountry('South Africa');
    $speaker->setTwitterHandle('@dannykopping');

    $event = new Event();
    $event->setName("PHPSouthAfrica");
    $event->setDescription('...');
    $event->setHashtag('#PHPJoburg14');
    $event->setUrl('http://phpsouthafrica.com');

    $event->addSession($session);

    $session->setSpeaker($speaker);

    Doctrine::persist($event);
    Doctrine::flush($event);

	return $response;
});

/**
 * Load assets from outside the project webroot
 */
Route::get('/assets/{uri}', function($uri)
{
	$projectRoot = dirname(__DIR__);
    echo file_get_contents($projectRoot.DIRECTORY_SEPARATOR.$uri);

})->where('uri', '(.*)?');