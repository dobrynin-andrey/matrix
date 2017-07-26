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
                                $M1[$district['id']][$itemValue->getYear()]['zone-1'][$itemParameter['parameterId']->getId()][$district['parameterType'][2]['parameters'][$i]['parameterId']->getId()] = $yearValue / $thirdTypeValue;

                            }


                            // Для Результаты - Внутренние
                            for ($i = 0; $i < count($district['parameterType'][3]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $fourthTypeValue =  $district['parameterType'][3]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()]['zone-2'][$itemParameter['parameterId']->getId()][$district['parameterType'][3]['parameters'][$i]['parameterId']->getId()] = $yearValue / $fourthTypeValue;


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
                                $M1[$district['id']][$itemValue->getYear()]['zone-3'][$itemParameter['parameterId']->getId()][$district['parameterType'][2]['parameters'][$i]['parameterId']->getId()] = $yearValue / $thirdTypeValue;

                            }


                            // Для Результаты - Внутренние
                            for ($i = 0; $i < count($district['parameterType'][3]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $fourthTypeValue =  $district['parameterType'][3]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()]['zone-4'][$itemParameter['parameterId']->getId()][$district['parameterType'][3]['parameters'][$i]['parameterId']->getId()] = $yearValue / $fourthTypeValue;


                            }

                        }
                    }
                }

            }

        }


        /**
         * Этап 2. Деление одного года на другой
         */

        $resultM2 = [];
        foreach ($M1 as $dm => $districtM1) { // Перебор районов

            $maxArray = max($districtM1); // Берем массив с более ранним годом
            $minArray = min($districtM1); // Берем массив с более поздним годом

            foreach ($maxArray as $iZ => $itemZone) { // Перебираем ранний год

                foreach ($itemZone as $mA => $itemMaxArray) { // Перебираем элементы каждый зоны

                    foreach ($itemMaxArray as $iMA => $valueItemMaxArray) { // Берем значение кадого элемента зоны

                        $resultM2[$dm][$iZ][$mA][$iMA] = $valueItemMaxArray / $minArray[$iZ][$mA][$iMA]; // Пишем в один структурированный массив  $resultM2[район][зона][элемент][значение]
                    }
                }
            }
        }



        /**
         * Этап 3. Рассчитываются обобщающие коэффициенты внутренней и внешней результативности использования внутренних и внешних ресурсов (матрица М3 - t)
         */

        $coefficients = []; // Объявляем массив коэффициентов

        foreach ($resultM2 as $rm => $itemResultM2) { // Перебирвем районы


            // 1. Зона показателей внешней эффективности (левый-верхний прямоугольник). => Коэффициент мультипликативности

            // 2. Зона показателей внутренней эффективности (правый-верхний прямоугольник). => Коэффициент адаптивности

            // 3. Зона показателей внешней эффективности использования собственных ресурсов (левый-нижний прямоугольник). => Коэффициент синергетичности

            // 4. Зона показателей внутренней эффективности использования собственных ресурсов (правый-нижний прямоугольник). => Коэффициент интенсивности


            $kSum = 0; // Обнуляем значене суммы коэффициентов
            $itemCount = 0; // Обнуляем количество параметров зоны

            foreach ($itemResultM2 as $iZ => $itemZone) { // Перебираем заны

                foreach ($itemZone as $iP => $itemParameter) { // Перебираем параметры

                    $itemCount = $itemCount + count($itemParameter); // Считаем сколько всего параметров

                    foreach ($itemParameter as $iPV => $itemParameterValue) { // Перебираем значения параметра

                        $kSum = $kSum + $itemParameterValue; // Складываем все значения параметров

                    }

                }

                // Задаем имя каждого коэффициента
                switch ($iZ) {
                    case 'zone-1':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент мультипликативности';
                        break;
                    case 'zone-2':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент адаптивности';
                        break;
                    case 'zone-3':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент синергетичности';
                        break;
                    case 'zone-4':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент интенсивности';
                        break;
                }

                $coefficients[$rm][$iZ]['value'] = $kSum / $itemCount; // Пишем в массив коэффициентов

                $kSum = 0; // Обнуляем значене суммы коэффициентов
                $itemCount = 0; // Обнуляем количество параметров зоны

            }
        }

        $arResult['parameters'] = $parametersAll;
        $arResult['M1'] = $M1;
        $arResult['resultM2'] = $resultM2;
        $arResult['coefficients'] = $coefficients;


        return $this->render('AppMatrixMatrixBundle:Page:calculation.html.twig', [
            'arResult' => $arResult
        ]);
    }

}