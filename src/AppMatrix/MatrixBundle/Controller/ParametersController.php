<?php

namespace AppMatrix\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppMatrix\MatrixBundle\Entity\Parameter;
use AppMatrix\MatrixBundle\Entity\ParameterValues;
use AppMatrix\MatrixBundle\Entity\District;
use Symfony\Component\HttpFoundation\Request;

class ParametersController extends Controller
{
    public function parametersAction(Parameter $parameters, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $parametersValues = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(["parameter" => $parameters->getId()]);

        $arResult = [];
        $arResult["labels"] = [];

        foreach ($parametersValues as $ipv => $itemPV) {

            // Массив для таблицы
            $districtValues = $em->getRepository('AppMatrixMatrixBundle:District')->findBy(["id" => $itemPV->getDistrict()->getId()]);

            $arResult["districts"][$districtValues[0]->getId()]["name"] = $districtValues[0]->getDistrictName();

            $arResult["districts"][$districtValues[0]->getId()]["id"] = $itemPV->getDistrict()->getId();

            $arResult["districts"][$districtValues[0]->getId()]["project"] = $itemPV->getProject()->getId();

            $arResult["districts"][$districtValues[0]->getId()]["values"][$itemPV->getYear()] = $itemPV->getParameterValue();


            // Массив для графика

            $arResult["years"][$itemPV->getYear()][] = $itemPV->getParameterValue();

            if (!in_array($districtValues[0]->getDistrictName(), $arResult["labels"])) {
                $arResult["labels"][] = $districtValues[0]->getDistrictName();
            }


        }

        return $this->render('AppMatrixMatrixBundle:Page:parameters.html.twig', array(
            'param' => $parameters,
            'parameters' => $arResult["districts"],
            'graph' => $arResult["years"],
            'graphLabels' => $arResult["labels"],
        ));
    }
}
