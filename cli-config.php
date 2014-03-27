<?php
use Conpherence\Entities\Base\BaseEntity;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/bootstrap/start.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = BaseEntity::getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);