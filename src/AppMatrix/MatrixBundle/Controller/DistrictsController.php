<?php

namespace AppMatrix\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppMatrix\MatrixBundle\Entity\Parameter;
use AppMatrix\MatrixBundle\Entity\Project;
use AppMatrix\MatrixBundle\Entity\ParameterValues;
use AppMatrix\MatrixBundle\Entity\District;
use Symfony\Component\HttpFoundation\Request;

class DistrictsController extends Controller
{
    public function districtsAction(Project $project, District $district, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $parametersValues = $em
            ->getRepository('AppMatrixMatrixBundle:ParameterValues')
            ->findBy([
                "project" => $project->getId(),
                "district" => $district->getId()
            ]);


        foreach ($parametersValues as $iPV => $itemParamVal) {

            // Массив для графика
            $arResult["parameters_graph"]["item"][$itemParamVal->getYear()]["name"][] = $itemParamVal->getParameter()->getParameterName();
            $arResult["parameters_graph"]["item"][$itemParamVal->getYear()]["value"][] = $itemParamVal->getParameterValue();

            $arResult["parameters_graph"]["labels_id"][$itemParamVal->getParameter()->getId()] = $itemParamVal->getParameter()->getParameterName();


            // Массив для таблицы
            $arResult["parameters_table"][$itemParamVal->getParameter()->getId()]["id"] = $itemParamVal->getParameter()->getId();
            $arResult["parameters_table"][$itemParamVal->getParameter()->getId()]["name"] = $itemParamVal->getParameter()->getParameterName();
            $arResult["parameters_table"][$itemParamVal->getParameter()->getId()]["value"][$itemParamVal->getYear()] = $itemParamVal->getParameterValue();
        }

        // Задаем массив labels
        foreach ($arResult["parameters_graph"]["labels_id"] as $item) {
            $arResult["parameters_graph"]["labels"][] = $item;
        }
        unset($arResult["parameters_graph"]["labels_id"]);


        return $this->render('AppMatrixMatrixBundle:Page:districts.html.twig', array(
            'project'          => $project,
            'district'         => $district,
            'parameters_graph' =>$arResult["parameters_graph"],
            'parameters_table' =>$arResult["parameters_table"]
        ));
    }
}
