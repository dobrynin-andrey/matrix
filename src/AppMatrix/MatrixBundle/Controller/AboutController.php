<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class AboutController extends Controller
{

    public function aboutAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:about.html.twig');
    }

}