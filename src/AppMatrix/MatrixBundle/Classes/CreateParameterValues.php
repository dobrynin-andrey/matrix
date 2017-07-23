<?php

namespace AppMatrix\MatrixBundle\Classes;

use AppMatrix\MatrixBundle\Entity\ParameterValues;

class CreateParameterValues extends ParameterValues {

    public function Add ($obg, $project_id, $district_id, $parameter_id, $parameter_value, $year) {

        //Получаем менеджер БД - Entity Manager
        $em = $obg->getDoctrine()->getManager();

        //Создаем экземпляр модели
        $parameterValues = new ParameterValues;
        //Задаем значение полей
        $parameterValues->setProject($project_id);
        $parameterValues->setDistrict($district_id);
        $parameterValues->setParameter($parameter_id);
        $parameterValues->setParameterValue($parameter_value);
        $parameterValues->setYear($year);
        $parameterValues->setCreated();
        $parameterValues->setUpdated();
        //Передаем менеджеру объект модели
        $em->persist($parameterValues);
        //Добавляем запись в таблицу
        $em->flush();


        return $parameterValues;
    }
}