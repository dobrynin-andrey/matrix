<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\Classes\CreateProject;
use AppMatrix\MatrixBundle\Entity\FormProject;
use AppMatrix\MatrixBundle\Form\FormProjectType;
use AppMatrix\MatrixBundle\Entity\FormDeleteParameter;
use AppMatrix\MatrixBundle\Form\FormDeleteParameterType;

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


        // Вывод проектов
        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('AppMatrixMatrixBundle:Project')->findAll();



        /**
         * Удаление проектов
         */

        $enquiry = new FormDeleteParameter();

        $form_delete = $this->createForm(FormDeleteParameterType::class, $enquiry);

        if ($request->isMethod($request::METHOD_POST)) {
            $form_delete->handleRequest($request);

            if ($form_delete->isValid()) {

                $yes = $request->request->get('form_delete')['yes'];


                $parameterValueDelete = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
                    [
                        'project' => $yes
                    ]
                );

                // Берем уникальные значения параметров
                /** @var  $qb  \Doctrine\ORM\QueryBuilder */
                $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("p");

                $projectParameters = $qb->select("(p.parameter)")
                    ->where('p.project = '. $yes)
                    ->distinct(true)
                    ->getQuery()
                    ->getResult();


                $projectDelete = $em->getRepository('AppMatrixMatrixBundle:Project')->findBy(
                    [
                        'id' => $yes
                    ]
                );



                foreach ($parameterValueDelete as $itemParameterValueDelete) { // Помещаем в remove каждый id значения параметра
                    $em->remove($itemParameterValueDelete);
                }

                $arrParameters = [];

                foreach ($projectParameters as $itemParameter) {
                    array_push($arrParameters ,$itemParameter[1]);
                }

                $parameter = $em->getRepository('AppMatrixMatrixBundle:Parameter')->findBy(
                    ['id' => $arrParameters]
                );

                foreach ($parameter as $itemParameterDelete) { // Помещаем в remove каждый id параметра
                    $em->remove($itemParameterDelete);
                }


                $em->remove($projectDelete[0]);


                if (!empty($projectDelete)) {
                    $this->addFlash('delete', 'Проект: ' . $projectDelete[0]->getProjectName() .' - успешно удален!');
                }

                $em->flush();

                return $this->redirectToRoute('AppMatrixMatrixBundle_homepage');
            }
        }





        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig', array(
            'form' => $form->createView(),
            'projects' => $projects,
            'form_delete' => $form_delete->createView(),
        ));
    }

}