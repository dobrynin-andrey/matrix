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

class PageController extends Controller
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

                return $this->redirect($this->generateUrl('AppMatrixMatrixBundle_form') . "?project=". $addProject);
            }
        }



        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function formAction(Request $request) {

        /**
         * Форма отправки
         */

        $project_id = (int)$request->query->get('project');
        dump($project_id);

        $enquiry = new Form();

        $form = $this->createForm(EnquiryType::class, $enquiry);

        if ($request->isMethod($request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {


                dump($request);
                // Получаем значения параметров
                $parameter_name = $request->request->get('form_add')['parameter_name'];
                $parameter_type = $request->request->get('form_add')['parameter_type'];
                $district_type = $request->request->get('form_add')['district_type'];
                $project_id = $request->request->get('project');
                dump($project_id);
                $file = $request->files->get('form_add')['file']->getClientOriginalName();
                $path = $request->files->get('form_add')['file']->getRealPath();

                // Создание объекта
                $phpCSV = new importCSV;
                // Проверка на корректность .csv
                $correctFiles = $phpCSV->detectCSV($file);

                if (!empty($correctFiles['CORRECT'])) {

                    // Парсинг корректных файлов
                    $arrParse = $phpCSV->parseCSV($path);

                    /**
                     * Предварительная очистка таблицы
                     */
                    /*$em = $this->getDoctrine()->getManager();
                    $connection = $em->getConnection();
                    $platform   = $connection->getDatabasePlatform();
                    $connection->executeUpdate($platform->getTruncateTableSQL('district', true /* whether to cascade *//*));*/


                    // Перебор распарсенных данных
                    foreach ($arrParse as $itemParse) {

                        foreach ($itemParse['year'] as  $year) {

                            $district = new CreateDistrict;
                            $addDistrict = $district->Add($this, $itemParse['district_name'], $district_type);
                            dump($addDistrict);
                            dump((int)$addDistrict);

                            $parameter = new CreateParameter;
                            $addProject = $parameter->Add($this, (int)$project_id, (int)$addDistrict, $parameter_name, $itemParse['parameter_value'][$year], $parameter_type, $year);
                            dump($addProject);

                        }
                    }

                    $result = 'true';
                } else {
                    $result = 'false';
                }



                // Perform some action, such as sending an email

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page

                return $this->redirect($this->generateUrl('AppMatrixMatrixBundle_form') . "?project=" . $project_id . "result=". $result);
            }
        }

        return $this->render('AppMatrixMatrixBundle:Page:form.html.twig', array(
            'form' => $form->createView()
        ));

    }



    public function testAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:test.html.twig');
    }

}