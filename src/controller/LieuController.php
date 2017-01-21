<?php
namespace App\controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use App\model\LieuModel;

class LieuController implements ControllerProviderInterface
{
    private $lieuModel;

    public function index(Application $app)
    {
        return $this->showLieux($app);       // appel de la méthode show
    }

    public function showLieux(Application $app)
    {
        $this->lieuModel = new LieuModel($app);
        $lieux = $this->lieuModel->getAllLieux();

        return $app["twig"]->render('lieu/v_show.html.twig', ['data' => $lieux]);
    }

    public function connect(Application $app)
    {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
            $route = $request->get("_route");

            if ($app['session']->get('right') != 'adminRight' && ($route == 'lieu.showLieux' || $route == 'lieu.index'))
                return $app->redirect($app["url_generator"]->generate("rightError"));
        });

        $controllers->get('/', 'App\Controller\LieuController::index')->bind('lieu.index');
        $controllers->get('/show', 'App\Controller\LieuController::showLieux')->bind('lieu.showLieux');

        return $controllers;
    }
}
?>