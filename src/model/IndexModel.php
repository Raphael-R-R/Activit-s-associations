<?php
namespace App\model;

use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class IndexModel
{

    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    /* Login : admin
     * Mot de passe : admin
	 *
	 * Pour n'y a pas de table contenant les identifiants d'utilisateurs
	 * pour cette implémentation. On se contente donc ici d'un simple if.
     */

    public function checkLogin($login, $password)
    {
        if($login == 'admin' && $password == 'admin')
            return array('login' => 'admin', 'password' => 'admin', 'right' => 'adminRight');

        return NULL;
    }
}
?>
