<?php

namespace AppMatrix\MatrixBundle\Classes;

use AppMatrix\MatrixBundle\Entity\ParameterType;

class CreateParameterType extends ParameterType {

    public function Add ($obg, $parameter_type) {

        //Получаем менеджер БД - Entity Manager
        $em = $obg->getDoctrine()->getManager();

        //Создаем экземпляр модели
        $parameterType = new ParameterType;
        //Задаем значение полей
        $parameterType->setParameterType($parameter_type);
        //Передаем менеджеру объект модели
        $em->persist($parameterType);
        //Добавляем запись в таблицу
        $em->flush();


        return $parameterType;
    }
}