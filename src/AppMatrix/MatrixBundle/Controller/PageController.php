<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig');
    }

    public function testAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:test.html.twig');
    }

}