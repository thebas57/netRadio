<?php

namespace animateur\middlewares;

use animateur\controllers\BaseController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class EstConnectUser
 * Acces au page seulement pour les admins
 * @package animateur\middlewares
 */
class EstConnectAdmin extends BaseController
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
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if ($user['droit'] == 0) {
                return $this->redirect($response, 'accueil');
            } else {
                $response = $next($request, $response);
                return $response;
            }
        } else {
            return $this->redirect($response, 'connexion');
        }
    }
}