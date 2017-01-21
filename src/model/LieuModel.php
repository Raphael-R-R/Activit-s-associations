<?php
namespace App\model;

use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

// Utiliser QueryBuilder

class LieuModel
{

    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllLieux()
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->select('id_lieu, nom_lieu')
            ->from('lieu')
            ->addOrderBy('id_lieu');

        return $queryBuilder->execute()->fetchAll();
    }
}
?>