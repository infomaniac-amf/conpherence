<?php
namespace Conpherence\Entities\Base;

use Atrauzzi\LaravelDoctrine\Support\Facades\Doctrine;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\App;
use Infomaniac\AMF\ISerializable;

abstract class BaseEntity implements ISerializable
{
    /**
     * @return EntityManager
     */
    public static function getEntityManager()
    {
        $entityManager = App::make('Doctrine\ORM\EntityManager');
        return $entityManager;
    }

    public static function getRepository()
    {
        return static::getEntityManager()->getRepository(get_called_class());
    }

    /**
     * Import data from an external source into this class
     *
     * @param $data mixed
     */
    public function import($data)
    {
        if(empty($data)) {
            return;
        }

        foreach($data as $key => $value) {
            if(!property_exists($this, $key)) {
                continue;
            }

            $this->$key = $value;
        }
    }
}