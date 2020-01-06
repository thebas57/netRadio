<?php

namespace auditeur\controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class BaseController
{

    protected $container = null;
    protected $views = null;

    /**
     * BaseController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Render function
     * @param ResponseInterface $response
     * @param $view
     * @param array $params
     */
    public function render(ResponseInterface $response, $view, $params = [])
    {
        return $this->view->render($response, $view, $params);
    }

    /**
     * Redirect function
     * @param ResponseInterface $response
     * @param $name name of slim route
     * @return ResponseInterface
     */
    public function redirect(ResponseInterface $response, $name)
    {
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor($name));
    }

    /**
     * Getter
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }
}
