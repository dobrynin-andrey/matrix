<?php

namespace AppMatrix\MatrixBundle\Classes;

use AppMatrix\MatrixBundle\Entity\District;

class CreateDistrict extends District{

    public function Add ($obg, $district_name, $district_type) {

        //Получаем менеджер БД - Entity Manager
        $em = $obg->getDoctrine()->getManager();

        //Создаем экземпляр модели
        $district = new District;
        //Задаем значение полей
        $district->setDistrictName($district_name);
        $district->setDistrictType($district_type);
        $district->setCreated();
        $district->setUpdated();
        //Передаем менеджеру объект модели
        $em->persist($district);
        //Добавляем запись в таблицу
        $em->flush();



        return $district;
    }
}