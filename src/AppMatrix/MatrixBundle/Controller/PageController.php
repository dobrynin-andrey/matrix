<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\Classes\CreateParameterType;
use AppMatrix\MatrixBundle\Classes\CreateProject;
use AppMatrix\MatrixBundle\Classes\CreateDistrict;
use AppMatrix\MatrixBundle\Classes\CreateParameter;
use AppMatrix\MatrixBundle\Classes\CreateParameterValues;
use AppMatrix\MatrixBundle\Entity\FormDeleteParameter;
use AppMatrix\MatrixBundle\Entity\FormProject;
use AppMatrix\MatrixBundle\Entity\Parameter;
use AppMatrix\MatrixBundle\Entity\Project;
use AppMatrix\MatrixBundle\Entity\District;


use AppMatrix\MatrixBundle\Entity\Form;
use AppMatrix\MatrixBundle\Form\EnquiryType;
use AppMatrix\MatrixBundle\Form\FormDeleteParameterType;
use AppMatrix\MatrixBundle\Form\FormProjectType;
use Symfony\Component\HttpFoundation\Request;
use AppMatrix\MatrixBundle\PHPExcel\importCSV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class PageController extends Controller
{

    public function formAction(Project $project, Request $request) {

        // Флаг продолжения ипорта
        $continue = true;
        $continueDistrict = true;

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
                $file = $enquiry->getFile()->getClientOriginalName();
                $path = $enquiry->getFile()->getRealPath();


                // Создание объекта
                $phpCSV = new importCSV;
                // Проверка на корректность .csv
                $correctFiles = $phpCSV->detectCSV($file);

                if (!empty($correctFiles['CORRECT'])) {

                    // Парсинг корректных файлов
                    $arrParse = $phpCSV->parseCSV($path);

                    // Берем объект project_type из таблицы
                    $parameterTypeID = $this->get('doctrine.orm.entity_manager')->getRepository('AppMatrixMatrixBundle:ParameterType')->find($parameter_type);

                    // Проверка на наличие параметра с таким же именем

                    if (!empty($parameter_name)) {

                        $em = $this->getDoctrine()->getManager();
                        // Есть ли такой параметр в базе
                        $parameterDB = $em->getRepository('AppMatrixMatrixBundle:Parameter')->findBy(
                            [
                                'parameter_name' => $parameter_name,
                            ]
                        );
                        // Есть ли такой параметр у данного проекта
                        if (isset($parameterDB)) {
                            foreach ($parameterDB as $itemParam) {
                                $parametersValuesDB = $em
                                    ->getRepository('AppMatrixMatrixBundle:ParameterValues')
                                    ->findBy([
                                        "project" => $project->getId(),
                                        "parameter" => $itemParam->getId()
                                    ]);

                            }

                        }

                        if (!empty($parametersValuesDB)) {
                            $this->addFlash('error', 'Параметер с именем: "' . $parameter_name . '" - уже сеществует!');
                            $continue = false;
                        }
                    }


                    // Если такого параметра нет, то продолжаем импорт

                    if ($continue) {
                        // Запись в табилцу "parameter"
                        $parameter = new CreateParameter;
                        $addParameter = $parameter->Add($this,  $parameter_name, $parameterTypeID);


                        // Перебор распарсенных данных
                        foreach ($arrParse["result"] as $itemParse) {

                            // Запись в табилцу "district"
                            $district = new CreateDistrict;
                            $em = $this->getDoctrine()->getManager();


                            // Тип объекта
                            if (isset($itemParse["type"])) {
                                $district_type = $itemParse["type"];
                            } else {
                                $this->addFlash('error', 'Предупреждение! При загрузки параметра: "' . $parameter_name . '", у объекта - ' . $itemParse['district_name'] . ' - ну указан тип! Удалите параметр, исправьте ипмпортируемый файл, произведите загрузку повторно, иначе данный объект не будет участвовать в дальнейших расчетах, загруженного параметра!');
                                $continueDistrict = false;
                            }


                            // Если тип объекта не указан, то не импотируем его
                            if ($continueDistrict) {
                                // Берем загруженные районы
                                if (!empty($itemParse['district_name'])) {
                                    $districtDB = $em->getRepository('AppMatrixMatrixBundle:District')->findBy(
                                        [
                                            'district_name' => $itemParse['district_name'],
                                            'district_type' => $district_type
                                        ]
                                    );
                                } else {
                                    break;
                                }


                                $addDistrict = '';

                                if (!empty($districtDB)) {
                                    // Проверка на наличие уже существующих районов
                                    foreach ($districtDB as $itemDistrictDB) {
                                        if ($itemDistrictDB->getDistrictName() != $itemParse['district_name']) {
                                            $addDistrict = $district->Add($this, $itemParse['district_name'], $district_type);
                                        } else {
                                            $addDistrict = $itemDistrictDB;
                                        }

                                    }
                                } else {
                                    $addDistrict = $district->Add($this, $itemParse['district_name'], $district_type);

                                }

                                foreach ($itemParse['year'] as  $year) {
                                    $parameterValue = new CreateParameterValues;
                                    $addParameterValue = $parameterValue->Add($this, $project, $addDistrict, $addParameter, $itemParse['parameter_value'][$year], $year);

                                }

                            }
                            $continueDistrict = true;
                        }


                        if (!empty($addParameter)) {
                            $this->addFlash('success', 'Загрузка прошла успешно!');
                        } else {
                            $this->addFlash('error', 'Загрузка прошла неудачно!');
                        }
                    }


                } else {
                    $this->addFlash('error', 'Загруженный файл не является .csv!');
                }

                if (isset($arrParse["errors"]["values"])) {
                    foreach ($arrParse["errors"]["values"] as $e => $error) {
                        $this->addFlash('error', $error);
                    }
                }



                return $this->redirectToRoute('AppMatrixMatrixBundle_project_form', array('id' => $project->getId()));
            }
        }

        $em = $this->getDoctrine()->getManager();

        // Выводим текущий проект
        $projects = $em->getRepository('AppMatrixMatrixBundle:Project')->find($project->getId());

        // Берем уникальные значения параметров
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("p");

        $allIdParameters = $qb->select("(p.parameter)")
            ->where('p.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();

        // Справочник всех типов параметров
        $parametersTypeArray = $em->getRepository('AppMatrixMatrixBundle:ParameterType')->findAll();

        // Формируем массив с параметрами и их типами
        $parametersAll =[];
        foreach ($parametersTypeArray as $k => $itemType) {
            foreach ($allIdParameters as $itemId) {
                $parametersOne = $em->getRepository('AppMatrixMatrixBundle:Parameter')->find($itemId['1']);
                if ($itemType == $parametersOne->getParameterType()) {
                    $parametersAll[$k]["name"] = $itemType->getParameterType();
                    $parametersAll[$k]["value"][] = $parametersOne;
                }

            }

        }
        
        // Выводим районы текущего проекта
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("d");

        $districtsInParameterValue = $qb->select("(d.district)")
            ->where('d.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();

        $arrDistricts =[];
        foreach ($districtsInParameterValue as $itemDistrict) {
            array_push($arrDistricts ,$itemDistrict[1]);
        }
        $districts = $em->getRepository('AppMatrixMatrixBundle:District')->findBy(
            ['id' => $arrDistricts]
        );
        // Выводим районы текущего проекта END


        /**
         * Удаление параметров
         */

        $enquiry = new FormDeleteParameter();

        $form_delete = $this->createForm(FormDeleteParameterType::class, $enquiry);

        if ($request->isMethod($request::METHOD_POST)) {
            $form_delete->handleRequest($request);

            if ($form_delete->isValid()) {

                $yes = $request->request->get('form_delete')['yes'];

                $parameterValueDelete = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
                    [
                        'parameter' => $yes
                    ]
                );
                foreach ($parameterValueDelete as $itemParameterValueDelete) {
                    $em->remove($itemParameterValueDelete);
                }

                $parameterDelete = $em->getRepository('AppMatrixMatrixBundle:Parameter')->find($yes);
                if (!empty($parameterValueDelete) && !empty($parameterDelete)) {
                    $this->addFlash('delete', 'Параметр: ' . $parameterDelete->getParameterName() .' - успешно удален!');
                }

                $em->remove($parameterDelete);
                $em->flush();

                return $this->redirectToRoute('AppMatrixMatrixBundle_project_form', array('id' => $project->getId()));
            }
        }


        return $this->render('AppMatrixMatrixBundle:Page:form.html.twig', array(
            'form_upload' => $form->createView(),
            'projects'      => $projects,
            'parameters'      => $parametersAll,
            'parametersLength'      => count($allIdParameters),
            'districts'      => $districts,
            'form_delete' => $form_delete->createView(),
        ));

    }


}