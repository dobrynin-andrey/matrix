<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppMatrix\MatrixBundle\Entity\Project;
use AppMatrix\MatrixBundle\Entity\Parameter;
use AppMatrix\MatrixBundle\Entity\ParameterType;
use AppMatrix\MatrixBundle\Entity\ParameterValues;
use Symfony\Component\HttpFoundation\Request;

class CalculationController extends Controller
{

    public function calculationAction(Project $project, Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        /**
         * Первый вариант формирования массива (много запросов у бд)
         */


//        // Выводим районы текущего проекта
//        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
//        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("d");
//
//        $districtsInParameterValue = $qb->select("(d.district)")
//            ->where('d.project = '. $project->getId())
//            ->distinct(true)
//            ->getQuery()
//            ->getResult();
//
//        $arrDistricts =[];
//
//        // Берем уникальные значения параметров
//        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
//        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("p");
//
//        $allIdParameters = $qb->select("(p.parameter)")
//            ->where('p.project = '. $project->getId())
//            ->distinct(true)
//            ->getQuery()
//            ->getResult();
//
//        // Справочник всех типов параметров
//        $parametersTypeArray = $em->getRepository('AppMatrixMatrixBundle:ParameterType')->findAll();
//
//        // Формируем массив с параметрами и их типами
//        $parametersAll = [];
//
//        foreach ($districtsInParameterValue as $d => $itemDistrict) {
//            $parametersAll['districts'][$d]['id'] = $itemDistrict[1];
//            foreach ($parametersTypeArray as $k => $itemType) {
//                $p = 0;
//                foreach ($allIdParameters as $n => $itemId) {
//                    $parametersOne = $em->getRepository('AppMatrixMatrixBundle:Parameter')->find($itemId['1']);
//
//                    if ($itemType == $parametersOne->getParameterType()) {
//
//                        $parametersAll['districts'][$d]['parameterType'][$k]['id'] = $itemType->getId();
//                        $parametersAll['districts'][$d]['parameterType'][$k]['nameType'] = $itemType->getParameterType();
//                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$p]['parameterId'] = $parametersOne;
//
//                        $parameterValues = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
//                            [
//                                'project' => $project,
//                                'parameter' => $parametersOne,
//                                'district' => $itemDistrict,
//                            ]
//                        );
//                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$p]['parameterValue'] = $parameterValues;
//                        $p++;
//                    }
//                }
//            }
//        }
//
//        dump($parametersAll['districts'][0]);
//        dump($parametersAll);



        /**
         * Второй вариант формирования массива
         */


        // Выводим районы текущего проекта
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("d");

        $districtsInParameterValue = $qb->select("(d.district)")
            ->where('d.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();

        $arrDistricts =[];

        // Справочник всех типов параметров
        $parametersTypeArray = $em->getRepository('AppMatrixMatrixBundle:ParameterType')->findAll();


        // Берем уникальные значения параметров
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("p");


        $allIdParameters = $qb->select("(p.parameter)")
            ->where('p.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();

        $allParametersBD = [];

        foreach ($allIdParameters as $itemIdParameter) {
            // Справочник всех параметров проекта
            $parametersBD = $em->getRepository('AppMatrixMatrixBundle:Parameter')->findBy(
                [
                    'id' => $itemIdParameter[1]
                ]
            );
            array_push($allParametersBD,$parametersBD[0]);

        }

        $parameterValues = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
            [
                'project' => $project
            ]
        );

        // Формируем массив с параметрами и их типами
        $parametersAll = [];

        foreach ($districtsInParameterValue as $d => $itemDistrict) {
            $parametersAll['districts'][$d]['id'] = $itemDistrict[1];
            foreach ($parametersTypeArray as $k => $itemType) {
                $i = 0;
                foreach ($allParametersBD as $itemId) {

                    if ($itemType->getId() == $itemId->getParameterType()->getId()) {
                        $parametersAll['districts'][$d]['parameterType'][$k]['id'] = $itemType->getId();
                        $parametersAll['districts'][$d]['parameterType'][$k]['nameType'] = $itemType->getParameterType();
                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$i]['parameterId'] = $itemId;
                        foreach ($parameterValues as $itemValue) {
                            if ($itemValue->getDistrict()->getId() == $itemDistrict[1] &&
                            $itemValue->getParameter()->getId() == $itemId->getId()) {
                                $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$i]['parameterValues'][] = $itemValue;
                               ;
                            }
                        }
                        $i++;
                    }
                }
            }
        }

        /**
         *  Построение и рассчет матриц. Этап 1
         */

        // Перебор районов (districts)

        $M1 = [];

        //dump($parametersAll['districts']);
        foreach ($parametersAll['districts'] as $d => $district) {
//            if ($d == 1) {
//                break;
//            }
            foreach ($district['parameterType'] as $t => $parameterType) {

                /**
                 *  Матрица М1
                 */

                // Вычисляем по первому параметру
                if ($parameterType['id'] == 1) {
                    foreach ($parameterType['parameters'] as $p => $itemParameter) { // Перебираем массив параметров
                        foreach ($itemParameter['parameterValues'] as $v => $itemValue) { // Перебираем значение каждого параметра
                            // Для Результаты - Внешние
                            for ($i = 0; $i < count($district['parameterType'][2]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $thirdTypeValue =  $district['parameterType'][2]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()][$itemParameter['parameterId']->getId()][$district['parameterType'][2]['parameters'][$i]['parameterId']->getId()] = $yearValue / $thirdTypeValue;

                            }


                            // Для Результаты - Внутренние
                            for ($i = 0; $i < count($district['parameterType'][3]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $fourthTypeValue =  $district['parameterType'][3]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()][$itemParameter['parameterId']->getId()][$district['parameterType'][3]['parameters'][$i]['parameterId']->getId()] = $yearValue / $fourthTypeValue;


                            }

                        }
                    }
                }

                // Вычисляем по второму параметру
                if ($parameterType['id'] == 2) {
                    foreach ($parameterType['parameters'] as $p => $itemParameter) { // Перебираем массив параметров
                        foreach ($itemParameter['parameterValues'] as $v => $itemValue) { // Перебираем значение каждого параметра
                            // Для Результаты - Внешние
                            for ($i = 0; $i < count($district['parameterType'][2]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $thirdTypeValue =  $district['parameterType'][2]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()][$itemParameter['parameterId']->getId()][$district['parameterType'][2]['parameters'][$i]['parameterId']->getId()] = $yearValue / $thirdTypeValue;

                            }


                            // Для Результаты - Внутренние
                            for ($i = 0; $i < count($district['parameterType'][3]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $fourthTypeValue =  $district['parameterType'][3]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()][$itemParameter['parameterId']->getId()][$district['parameterType'][3]['parameters'][$i]['parameterId']->getId()] = $yearValue / $fourthTypeValue;


                            }

                        }
                    }
                }

            }

        }


        /**
         * Этап 2. Деление одного года на другой
         */

        foreach ($M1 as $dm => $districtM1) { // Перебор районов

            $maxArray = max($districtM1);
            $minArray = min($districtM1);

            foreach ($maxArray as $mA => $itemMaxArray) {

                foreach ($itemMaxArray as $iMA => $valueItemMaxArray) {

                    $resultM2[$dm][$mA][$iMA] = $valueItemMaxArray / $minArray[$mA][$iMA];

                }
            }
        }


        return $this->render('AppMatrixMatrixBundle:Page:calculation.html.twig', [
            //'arResult' => $parametersAll
        ]);
    }

}