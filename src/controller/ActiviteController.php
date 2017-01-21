<?php
namespace App\controller;

use App\model\LieuModel;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

use App\model\ActiviteModel;
use App\helper\helper_date;

use Symfony\Component\Validator\Constraints as Assert;

class ActiviteController implements ControllerProviderInterface
{
    private $activiteModel;

    public function index(Application $app)
    {
        return $this->showActivite($app);       // appel de la méthode show
    }

    public function showActivite(Application $app)
    {
        $this->activiteModel = new ActiviteModel($app);
        $activites = $this->activiteModel->getAllActivites();
        $nbAct = count($activites);

        for($i = 0; $i < $nbAct; $i++)
            $activites[$i]['dateCreation'] = helper_date::us2fr($activites[$i]['dateCreation']);

        return $app["twig"]->render('activite/v_show.html.twig', ['data' => $activites]);
    }

    public function addActivite(Application $app)
    {
        $this->LieuModel = new LieuModel($app);
        $listeLieux = $this->LieuModel->getAllLieux();

        $this->init_token($app);

        return $app["twig"]->render('activite/v_add.html.twig', ['data' => array(), 'listeLieux' => $listeLieux]);
    }

    public function addActiviteConfirm(Application $app)
    {
        if(isset($_POST['nom']) && isset($_POST['id_lieu']) && isset($_POST['type']) && isset($_POST['cout']))
        {
            $data = [
                'nom' => htmlspecialchars($_POST['nom']),
                'id_lieu' => $_POST['id_lieu'],
                'type' => $_POST['type'],
                'cout' => $_POST['cout'],
                'date' => helper_date::fr2us($_POST['date'])
            ];

            $constraint = new Assert\Collection([
                'nom' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un nom.']),
                    new Assert\Length(['min' => 2, 'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères.'])
                ],
                'id_lieu' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un lieu']),
                    new Assert\Type('digit')
                ],
                'type' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un type']),
                    new Assert\Length(['min' => 2])
                ],
                'cout' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un coût.']),
                    new Assert\GreaterThan(['value' => 0, 'message' => 'Le coût doit être un nombre supérieur à {{ compared_value }}.'])
                ],
                'date' => [new Assert\Date(['message' => 'Veuillez entrer une date valide.'])]
            ]);

            $errors = $app['validator']->validate($data, $constraint);
            $errorArray = [
                'nom' => '',
                'id_lieu' => '',
                'type' => '',
                'cout' => '',
                'date' => ''
            ];

            $data['date'] = $_POST['date'];

            if(count($errors) > 0)
            {
                foreach($errors as $error)
                {
                    $prop = substr($error->getPropertyPath(), 1, -1);
                    $msg = $error->getMessage();

                    if(empty($errorArray[$prop]))
                        $errorArray[$prop] = $msg;
                }
            }

            foreach($errorArray as $key => $e)
            {
                if(empty($e))
                    unset($errorArray[$key]);
            }

            if(isset($error))
            {
                $this->LieuModel = new LieuModel($app);
                $listeLieux = $this->LieuModel->getAllLieux();

                $this->init_token($app);

                return $app["twig"]->render('activite/v_add.html.twig', ['data' => $data, 'listeLieux' => $listeLieux, 'error' => $errorArray]);
            }
            else if($this->check_token($app))
            {
                $this->ActiviteModel = new ActiviteModel($app);
                $this->ActiviteModel->addActivite($data);

                return $app->redirect($app['url_generator']->generate('activite.showActivite'));
            }
        }
        else
            return "Erreur formulaire.";
    }

    public function deleteActivite(Application $app, $id)
    {
        $this->activiteModel = new ActiviteModel($app);
        $data = $this->activiteModel->getActivite($id);

        return $app["twig"]->render('activite/v_del.html.twig', ['data' => $data]);
    }

    public function deleteActiviteConfirm(Application $app)
    {
        $this->activiteModel = new ActiviteModel($app);

        if($_POST['deleteConfirm'] == 'Oui')
            $this->activiteModel->deleteActivite($_POST['id']);

        return $app->redirect($app['url_generator']->generate('activite.showActivite'));
    }

    public function editActivite(Application $app, $id)
    {
        $this->activiteModel = new ActiviteModel($app);
        $activite = $this->activiteModel->getActiviteNoJoin($id);
        $this->LieuModel = new LieuModel($app);
        $listeLieux = $this->LieuModel->getAllLieux();
        $data = [
            'id' => $activite['idActivite'],
            'nom' => $activite['nomActivite'],
            'id_lieu' => $activite['id_lieu'],
            'type' => $activite['type'],
            'cout' => $activite['coutInscription'],
            'date' => helper_date::us2fr($activite['dateCreation'])
        ];

        $this->init_token($app);

        return $app["twig"]->render('activite/v_edit.html.twig', ['data' => $data, 'listeLieux' => $listeLieux]);
    }

    public function editActiviteConfirm(Application $app)
    {
        if(isset($_POST['nom']) && isset($_POST['id_lieu']) && isset($_POST['type']) && isset($_POST['cout']))
        {
            $data = [
                'id' => $_POST['id'],
                'nom' => htmlspecialchars($_POST['nom']),
                'id_lieu' => $_POST['id_lieu'],
                'type' => $_POST['type'],
                'cout' => $_POST['cout'],
                'date' => helper_date::fr2us($_POST['date'])
            ];
            $constraint = new Assert\Collection([
                'id' => [new Assert\NotBlank(), new Assert\Type('digit')],
                'nom' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un nom.']),
                    new Assert\Length(['min' => 2, 'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères.'])
                ],
                'id_lieu' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un lieu']),
                    new Assert\Type('digit')
                ],
                'type' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un type']),
                    new Assert\Length(['min' => 2])
                ],
                'cout' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un coût.']),
                    new Assert\GreaterThan(['value' => 0, 'message' => 'Le coût doit être un nombre supérieur à {{ compared_value }}.'])
                ],
                'date' => [new Assert\Date(['message' => 'Veuillez entrer une date valide.'])]
            ]);

            $errors = $app['validator']->validate($data, $constraint);
            $errorArray = [
                'id' => '',
                'nom' => '',
                'id_lieu' => '',
                'type' => '',
                'cout' => '',
                'date' => ''
            ];

            $data['date'] = $_POST['date'];

            if(count($errors) > 0)
            {
                foreach($errors as $error)
                {
                    $prop = substr($error->getPropertyPath(), 1, -1);
                    $msg = $error->getMessage();

                    if(empty($errorArray[$prop]))
                        $errorArray[$prop] = $msg;
                }
            }

            foreach($errorArray as $key => $e)
            {
                if(empty($e))
                    unset($errorArray[$key]);
            }

            if(isset($error))
            {
                $this->LieuModel = new LieuModel($app);
                $listeLieux = $this->LieuModel->getAllLieux();

                $this->init_token($app);

                return $app["twig"]->render('activite/v_edit.html.twig', ['data' => $data, 'listeLieux' => $listeLieux, 'error' => $errorArray]);
            }
            else if($this->check_token($app))
            {
                $this->ActiviteModel = new ActiviteModel($app);
                $this->ActiviteModel->editActivite($data);

                echo '<pre>';
                print_r($data);
                echo "'".$data['date']."'";
                echo '</pre>';
                return $app->redirect($app['url_generator']->generate('activite.showActivite'));
            }
        }
        else
            return "Erreur formulaire.";
    }

    private function init_token(Application $app)
    {
        $token = uniqid(rand(), true);

        $app['session']->set('token', $token);
        $app['session']->set('token_time', time());
    }

    private function check_token(Application $app)
    {
        if($app['session']->get('token') && $app['session']->get('token_time') && isset($_POST['token']))
        {
            if($app['session']->get('token') == $_POST['token'])
            {
                if($app['session']->get('token') == $_POST['token'])
                {
                    $timestamp_old = time() - (15 * 60);

                    if ($app['session']->get('token_time') >= $timestamp_old)
                        return true;
                }
            }
        }

        return false;
    }

    public function connect(Application $app)
    {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app)
        {
            $route = $request->get("_route");

            if($app['session']->get('right') != 'adminRight' &&
                ($route == 'activite.addActivite' || $route == 'activite.editActivite' || $route == 'activite.deleteActivite'))
                return $app->redirect($app["url_generator"]->generate("rightError"));
        });

        $controllers->get('/', 'App\Controller\ActiviteController::index')->bind('activite.index');
        $controllers->get('/show', 'App\Controller\ActiviteController::showActivite')->bind('activite.showActivite');

        $controllers->get('/add', 'App\Controller\ActiviteController::addActivite')->bind('activite.addActivite');
        $controllers->post('/add', 'App\Controller\ActiviteController::addActiviteConfirm')->bind('activite.addActiviteConfirm');

        $controllers->delete('/delete', 'App\Controller\ActiviteController::deleteActivite')->bind('activite.deleteActivite');
        $controllers->match('/delete/{id}', 'App\Controller\ActiviteController::deleteActivite')->bind('activite.deleteActivite');
        $controllers->post('/delete', 'App\Controller\ActiviteController::deleteActiviteConfirm')->bind('activite.deleteActiviteConfirm');

        $controllers->put('/edit/', 'App\Controller\ActiviteController::editActivite')->bind('activite.editActivite');
        $controllers->match('/edit/{id}', 'App\Controller\ActiviteController::editActivite')->bind('activite.editActivite');
        $controllers->post('/edit/', 'App\Controller\ActiviteController::editActiviteConfirm')->bind('activite.editActiviteConfirm');

        return $controllers;
    }
}
?>
