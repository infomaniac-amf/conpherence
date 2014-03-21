<?php
namespace Conpherence\Entities\Base;

use Atrauzzi\LaravelDoctrine\Support\Facades\Doctrine;
use Illuminate\Support\Facades\App;

class BaseEntity
{
    public function getEntityManager()
    {
        $em = App::make('Doctrine\ORM\EntityManager');
        return $em;
    }
}