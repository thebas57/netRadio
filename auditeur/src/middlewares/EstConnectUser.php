<?php

namespace auditeur\middlewares;

use auditeur\controllers\BaseController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class EstConnectUser
 * Si l'user n'est pas co, on empeche tout acces et on redirige vers page home
 * @package auditeur\middlewares
 */
class EstConnectUser extends BaseController
{

    /**
     * méthode invoquée lors de l'utilisation du middleware
     * @param $request
     * @param $response
     * @param $next
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {

        // Controle si user non connecte
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            return $this->redirect($response, 'Accueil');
        }

        $response = $next($request, $response);

        return $response;
    }
}