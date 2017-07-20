<?php

namespace AppMatrix\MatrixBundle\Classes;

use AppMatrix\MatrixBundle\Entity\Parameter;

class CreateParameter {

    public function Add ($obg, $project_id, $district_id, $parameter_name, $parameter_value, $parameter_type, $year) {

        //Получаем менеджер БД - Entity Manager
        $em = $obg->getDoctrine()->getManager();

        $dataTime = new \DateTime();

        //Создаем экземпляр модели
        $parameter = new Parameter;
        //Задаем значение полей
        $parameter->setProject($project_id);
        $parameter->setDistrict($district_id);
        $parameter->setParameterName($parameter_name);
        $parameter->setParameterValue($parameter_value);
        $parameter->setParameterType($parameter_type);
        $parameter->setYear($year);
        $parameter->setCreated($dataTime);
        $parameter->setUpdated($dataTime);
        //Передаем менеджеру объект модели
        $em->persist($parameter);
        //Добавляем запись в таблицу
        $em->flush();

        $idParameter =$parameter->getId();

        return $idParameter;
    }
}