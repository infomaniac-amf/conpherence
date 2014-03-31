<?php
namespace Conpherence\Controllers;

use Conpherence\Entities\Speaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class CountriesController extends AMFController
{
    public function getIndex()
    {
        return $this->createAMFResponse($this->getAvailableCountriesByFlags());
    }

    private function getAvailableCountriesByFlags()
    {
        $flagsPath = Config::get('app.flags');
        $flags = File::files($flagsPath);

        $countries = [];
        foreach($flags as $flag) {
            preg_match('%([\w\-]+)\-icon.png%i', $flag, $matches);
            $countries[] = str_replace('-', ' ', $matches[1]);
        }

        return $countries;
    }
} 