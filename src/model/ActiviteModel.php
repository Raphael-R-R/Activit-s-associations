<?php
namespace App\Model;

use App\helper\helper_date;
use MongoDB\Driver\Query;
use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

// Utiliser QueryBuilder

class ActiviteModel
{

    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getActiviteNoJoin($id)
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->select('idActivite, nomActivite, dateCreation, coutInscription, type, id_lieu')
            ->from('activite')
            ->where('idActivite = '.$id);

        return $queryBuilder->execute()->fetch();
    }

    public function getActivite($id)
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->select('idActivite, nomActivite, dateCreation, coutInscription, type, lieu.nom_lieu')
            ->from('activite')
            ->innerJoin('activite', '', 'lieu', 'activite.id_lieu=lieu.id_lieu')
            ->where('idActivite = '.$id);

        return $queryBuilder->execute()->fetch();
    }

    public function getAllActivites()
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->select('idActivite, nomActivite, dateCreation, coutInscription, type, lieu.nom_lieu')
            ->from('activite')
            ->innerJoin('activite', '', 'lieu', 'activite.id_lieu=lieu.id_lieu')
            ->addOrderBy('idActivite');

        return $queryBuilder->execute()->fetchAll();
    }

    public function addActivite($data)
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->insert('activite')
            ->values([
                'nomActivite' => '?',
                'dateCreation' => '?',
                'coutInscription' => '?',
                'type' => '?',
                'id_lieu' => '?',
            ])
            ->setParameter(0, $data['nom'])
            ->setParameter(1, helper_date::fr2us($data['date']))
            ->setParameter(2, $data['cout'])
            ->setParameter(3, $data['type'])
            ->setParameter(4, $data['id_lieu']);

        return $queryBuilder->execute();
    }

    public function deleteActivite($id)
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->delete('activite')
            ->where('idActivite = '.$id);

        return $queryBuilder->execute();
    }

    public function editActivite($data)
    {
        $queryBuilder = new QueryBuilder($this->db);

        $queryBuilder->update('activite')
            ->set('activite.nomActivite', $queryBuilder->expr()->literal($data['nom']))
            ->set('activite.coutInscription', $data['cout'])
            ->set('activite.type', $queryBuilder->expr()->literal($data['type']))
            ->set('activite.id_lieu', $data['id_lieu'])
            ->set('activite.dateCreation', $queryBuilder->expr()->literal(helper_date::fr2us($data['date'])))
            ->where('idActivite = '.$data['id']);

        return $queryBuilder->execute();
    }
}
?>