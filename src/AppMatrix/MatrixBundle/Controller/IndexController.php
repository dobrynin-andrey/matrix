<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\Classes\CreateProject;
use AppMatrix\MatrixBundle\Entity\FormProject;
use AppMatrix\MatrixBundle\Form\FormProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function indexAction(Request $request)
    {

        $enquiry = new FormProject();

        $form = $this->createForm(FormProjectType::class, $enquiry);

        if ($request->isMethod($request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $project_name = $request->request->get('form_homepage')['project_name'];

                $project = new CreateProject;
                $addProject = $project->Add($this, $project_name);

                return $this->redirectToRoute('AppMatrixMatrixBundle_project_form', array('id' => $addProject->getId()));
                //return $this->redirect($this->generateUrl('AppMatrixMatrixBundle_form') . "?project=". $addProject);
            }
        }


        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('AppMatrixMatrixBundle:Project')->findAll();


        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig', array(
            'form' => $form->createView(),
            'projects' => $projects
        ));
    }

}