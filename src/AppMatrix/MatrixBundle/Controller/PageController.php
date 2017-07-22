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

class PageController extends Controller
{

    public function formAction(Project $project, Request $request) {

        /**
         * Форма отправки
         */


        $enquiry = new Form();
        $enquiry->setProjectId($project->getId());


        $form = $this->createForm(EnquiryType::class, $enquiry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->isValid()) {

                // Получаем значения параметров
                $parameter_name = $enquiry->getParameterName();
                $parameter_type = $enquiry->getParameterType();
                $district_type = $enquiry->getDistrictType();
                //$project_id = $enquiry->getProjectId();
                $file = $enquiry->getFile()->getClientOriginalName();
                $path = $enquiry->getFile()->getRealPath();


                //$entity = $this->get('doctrine.orm.entity_manager')->getRepository('AppMatrixMatrixBundle:Project')->find($project_id);

                // Создание объекта
                $phpCSV = new importCSV;
                // Проверка на корректность .csv
                $correctFiles = $phpCSV->detectCSV($file);

                if (!empty($correctFiles['CORRECT'])) {

                    // Парсинг корректных файлов
                    $arrParse = $phpCSV->parseCSV($path);

                    // Перебор распарсенных данных
                    foreach ($arrParse as $itemParse) {

                        // Запись в табилцу "district"
                        $district = new CreateDistrict;
                        $addDistrict = $district->Add($this, $itemParse['district_name'], $district_type);

                        foreach ($itemParse['year'] as  $year) {

                            // Запись в табилцу "parameter"
                            $parameter = new CreateParameter;
                            $addParameter = $parameter->Add($this, $project, $addDistrict, $parameter_name, $itemParse['parameter_value'][$year], $parameter_type, $year);

                        }
                    }
                    if (!empty($addParameter)){
                        $this->addFlash('success', 'Загрузка прошла успешно!');
                    } else {
                        $this->addFlash('unsuccess', 'Загрузка прошла неудачно!');
                    }
                } else {
                    $this->addFlash('error', 'Загруженный файл не является .csv!');
                }


                return $this->redirectToRoute('AppMatrixMatrixBundle_project_form', array('id' => $project->getId()));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('AppMatrixMatrixBundle:Project')->find($project->getId());

        if (!$projects) {
            throw $this->createNotFoundException('Не найден не один проект!');
        }
        $parameters = $em->getRepository('AppMatrixMatrixBundle:Parameter')->findBy(
            array('project' => $project->getId()),
            array('parameter_name' => 'ASC')
        );
//        if (!$parameters) {
//            throw $this->createNotFoundException('Не найден не один параметр!');
//        }
        dump($parameters);

     /*   $districts = $em->getRepository('AppMatrixMatrixBundle:District')->findBy(
            array('p' => 'Keyboard'),
            array('price' => 'ASC')
        );

        if (!$districts) {
            throw $this->createNotFoundException('Не найден не один район!');
        }

        dump($districts);
*/
        return $this->render('AppMatrixMatrixBundle:Page:form.html.twig', array(
            'form' => $form->createView(),
            'projects'      => $projects,
            'parameters'      => $parameters,
            //'districts'      => $districts,
        ));

    }

    public function testAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:test.html.twig');
    }

}