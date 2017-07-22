<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\Classes\CreateProject;
use AppMatrix\MatrixBundle\Classes\CreateDistrict;
use AppMatrix\MatrixBundle\Classes\CreateParameter;
use AppMatrix\MatrixBundle\Entity\FormProject;
use AppMatrix\MatrixBundle\Entity\Parameter;
use AppMatrix\MatrixBundle\Entity\Project;
use AppMatrix\MatrixBundle\Entity\District;


use AppMatrix\MatrixBundle\Entity\Form;
use AppMatrix\MatrixBundle\Form\EnquiryType;
use AppMatrix\MatrixBundle\Form\FormProjectType;
use Symfony\Component\HttpFoundation\Request;
use AppMatrix\MatrixBundle\PHPExcel\importCSV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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



        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

}