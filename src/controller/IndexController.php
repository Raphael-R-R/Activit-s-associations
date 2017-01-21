<?php
namespace App\controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // version 2.0 avant sans Api

use Symfony\Component\HttpFoundation\Request;

use App\model\IndexModel;

class IndexController implements ControllerProviderInterface
{
    private $indexModel;

    public function index(Application $app)
    {
        return $app['twig']->render('v_index.html.twig');
    }

    public function info()
    {
        return phpinfo();
    }

    public function login(Application $app)
    {
        return $app["twig"]->render('v_login.html.twig');
    }

    public function loginConfirm(Application $app, Request $req)
    {
        $app['session']->clear();
        $user['login']=$app->escape($req->get('login'));
        $user['password']=$app->escape($req->get('password'));

        $this->indexModel = new IndexModel($app);
        $data = $this->indexModel->checkLogin($user['login'], $user['password']);

        if($data != NULL)
        {
            $app['session']->set('right', $data['right']);
            $app['session']->set('login', $data['login']);
            $app['session']->set('logged', 1);
            return $app->redirect($app["url_generator"]->generate("index"));
        }
        else
        {
            $error = 'Mot de passe ou login incorrect';
            return $app["twig"]->render('v_login.html.twig', array('error' => $error));
        }
    }

    public function logout(Application $app)
    {
        $app['session']->clear();
        $app['session']->getFlashBag()->add('msg', 'Vous êtes déconnecté');

        return $app->redirect($app["url_generator"]->generate("index"));
    }

    public function rightError(Application $app)
    {
        return $app["twig"]->render('v_right_error.html.twig', array());
    }

    public function connect(Application $app)
    {
        // créer un nouveau controleur basé sur la route par défaut
        $index = $app['controllers_factory'];

        $index->match("/", 'App\Controller\IndexController::index');
        $index->match("/index", 'App\Controller\IndexController::index');
        $index->match("/", 'App\Controller\IndexController::index')->bind('index');
        $index->match("/index", 'App\Controller\IndexController::index')->bind('index');

        $index->match("/info", 'App\Controller\IndexController::info')->bind('phpinfo');
        $index->match("/info", 'App\Controller\IndexController::info');

        $index->get("/connexion", 'App\Controller\IndexController::login')->bind('login');
        $index->post("/connexion", 'App\Controller\IndexController::loginConfirm')->bind('loginConfirm');
        $index->get("/deconnexion", 'App\Controller\IndexController::logout')->bind('logout');

        $index->match("/erreurDroit", 'App\Controller\IndexController::rightError');
        $index->match("/erreurDroit", 'App\Controller\IndexController::rightError')->bind('rightError');

        return $index;
    }
}

?>