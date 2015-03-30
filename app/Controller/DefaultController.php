<?php

namespace Controller;

use Controller\Base\BaseController;
use Silex\Application;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController
{
    /**
     * @param Application $app
     * @return mixed
     *
     * Receive json array of trade message
     */
    public function indexAction(Application $app)
    {


        return $app['twig']->render('Default/index.html.twig', array());
    }
}