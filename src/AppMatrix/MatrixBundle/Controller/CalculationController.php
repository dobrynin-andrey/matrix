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


        // Выводим районы текущего проекта
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("d");

        $districtsInParameterValue = $qb->select("(d.district)")
            ->where('d.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();

        $arrDistricts =[];

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
        $parametersAll = [];

        foreach ($districtsInParameterValue as $d => $itemDistrict) {
            $parametersAll['districts'][$d]['id'] = $itemDistrict[1];
            foreach ($parametersTypeArray as $k => $itemType) {
                $p = 0;
                foreach ($allIdParameters as $n => $itemId) {
                    $parametersOne = $em->getRepository('AppMatrixMatrixBundle:Parameter')->find($itemId['1']);

                    if ($itemType == $parametersOne->getParameterType()) {

                        $parametersAll['districts'][$d]['parameterType'][$k]['id'] = $itemType->getId();
                        $parametersAll['districts'][$d]['parameterType'][$k]['nameType'] = $itemType->getParameterType();
                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$p]['parameterId'] = $parametersOne;

                        $parameterValues = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
                            [
                                'project' => $project,
                                'parameter' => $parametersOne,
                                'district' => $itemDistrict,
                            ]
                        );
                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$p]['parameterValue'] = $parameterValues;
                        $p++;
                    }
                }
            }
        }

        dump($parametersAll['districts'][0]);
        dump($parametersAll);



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



        return $this->render('AppMatrixMatrixBundle:Page:calculation.html.twig', [
            'arResult' => $parametersAll
        ]);
    }

}