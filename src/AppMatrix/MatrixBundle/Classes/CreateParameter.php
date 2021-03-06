<?php

namespace AppMatrix\MatrixBundle\Classes;

use AppMatrix\MatrixBundle\Entity\Parameter;

class CreateParameter extends Parameter{

    public function Add ($obg, $parameter_name,  $parameter_type) {

        //Получаем менеджер БД - Entity Manager
        $em = $obg->getDoctrine()->getManager();


        //Создаем экземпляр модели
        $parameter = new Parameter;
        //Задаем значение полей
        $parameter->setParameterName($parameter_name);
        $parameter->setParameterType($parameter_type);
        $parameter->setCreated();
        $parameter->setUpdated();
        //Передаем менеджеру объект модели
        $em->persist($parameter);
        //Добавляем запись в таблицу
        $em->flush();


        return $parameter;
    }
}