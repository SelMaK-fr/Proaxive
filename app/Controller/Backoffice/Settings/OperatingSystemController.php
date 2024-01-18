<?php
declare(strict_types=1);
namespace App\Controller\Backoffice\Settings;

use App\AbstractController;
use App\Repository\OperatingSystemRepository;
use App\Type\OperatingSystemType;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

class OperatingSystemController extends AbstractController
{

    public function index(Request $request, Response $response): Response
    {
        $operatingSystem = $this->getRepository(OperatingSystemRepository::class)->all();
        $form = $this->createForm(OperatingSystemType::class);
        $form->setAction($this->routeParser->urlFor('settings_os_create'));
        $form->handleRequest();
        //
        $breadcrumbs = $this->app->getContainer()->get('breadcrumbs');
        $breadcrumbs->addCrumb('Accueil', $this->routeParser->urlFor('dash_home'));
        $breadcrumbs->addCrumb('Paramètres', false);
        $breadcrumbs->addCrumb("Systèmes d'exploitation", false);
        $breadcrumbs->render();
        //
        return $this->render($response, 'backoffice/settings/operating_system/index.html.twig', [
            'currentMenu' => 'settings',
            'operatingSystem' => $operatingSystem,
            'form' => $form,
            'setting_current' => 'operatingSystem',
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function actionForm(Request $request, Response $response, array $args): Response
    {
        if($request->getMethod() === 'POST') {
            $id = (int)$request->getQueryParams()['id'];
            if($id != 0) {
                $data = $request->getParsedBody();
            } else {
                $data = $request->getParsedBody()['form_operating_system'];
            }
            $validator = $this->validator->validate($data, [
                'os_name' => [
                    'rules' => v::length(5, 80)
                ]
            ]);
            if($validator->count() === 0) {
                $data['os_full'] = $data['os_name'].' - '.$data['os_architecture'].' ('. $data['os_release'] .')';
                if($id != 0) {
                    $this->getRepository(OperatingSystemRepository::class)->update($data, $id);
                    $this->session->getFlash()->add('panel-info', "Action effectuée avec succès.");
                } else {
                    $this->getRepository(OperatingSystemRepository::class)->add($data);
                    $this->session->getFlash()->add('panel-info', sprintf('Le système (%s) a bien été créé.', $data['os_full']));
                }
                return $response->withStatus(302)->withHeader('Location', $this->routeParser->urlFor('settings_os'));
            }
        }
        return new \Slim\Psr7\Response();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Envms\FluentPDO\Exception
     */
    public function delete(Request $request, Response $response): Response
    {
        if($request->getMethod() === 'DELETE'){
            $data = $request->getParsedBody();
            if($data){
                unset($data['_METHOD']);
                $this->getRepository(OperatingSystemRepository::class)->delete((int)$data['id']);
                $this->session->getFlash()->add('panel-info', "OS supprimé.");
                return $response->withStatus(302)->withHeader('Location', $this->routeParser->urlFor('settings_os'));
            }
        }
        return new \Slim\Psr7\Response();
    }
}