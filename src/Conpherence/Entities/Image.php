<?php
namespace Conpherence\Entities;

use Conpherence\Entities\Base\BaseImage;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Image extends BaseImage
{
    /**
     * Return an associative array of class properties
     *
     * @return array
     */
    public function export()
    {
        return array('data' => $this->getData());
    }
}