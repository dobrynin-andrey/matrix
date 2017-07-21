<?php

namespace AppMatrix\MatrixBundle\Classes;

use AppMatrix\MatrixBundle\Entity\Project;

class CreateProject extends Project{

    public function Add ($obg, $request) {

        //Получаем менеджер БД - Entity Manager
        $em = $obg->getDoctrine()->getManager();

        $dataTime = new \DateTime();

        //Создаем экземпляр модели
        $project = new Project;
        //Задаем значение полей
        $project->setProjectName($request);
        $project->setCreated($dataTime);
        $project->setUpdated($dataTime);
        //Передаем менеджеру объект модели
        $em->persist($project);
        //Добавляем запись в таблицу
        $em->flush();



        return $project;
    }
}