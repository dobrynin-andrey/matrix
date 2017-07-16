<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\Entity\District;
use AppMatrix\MatrixBundle\PHPExcel\importCSV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        $root = str_replace("app", "", $this->get('kernel')->getRootDir()); // Путь от корня
        $path =  $root . 'upload/import_csv/'; // Путь к дирректории импорта

        $phpCSV = new importCSV; // Создание объекта
        $files = $phpCSV->readDir($path); // Чтение и помещение в массив файлов дирректории

        $correctFiles = $phpCSV->detectCSV($files); // Проверка на корректность .csv

        $arrParse = $phpCSV->parseCSV($path, $correctFiles["CORRECT"][0]); // Парсинг корректных файлов


        /**
         * Предварительная очистка таблицы
         */
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('district', true /* whether to cascade */));


        /**
         * Объявление значений
         */

        $district_type = "Муниципальный район";
        $parameter_name = "Продукция сельского хозяйства (в фактически действовавших ценах), тысяча рублей";

        $dataTime = new \DateTime();

        foreach ($arrParse as $itemParse) {

            foreach ($itemParse['year'] as  $year) {

                //Получаем менеджер БД - Entity Manager
                $em = $this->getDoctrine()->getManager();

                //Создаем экземпляр модели
                $district = new District;
                //Задаем значение полей
                $district->setDistrictName($itemParse['district_name']);
                $district->setDistrictType($district_type);
                $district->setYear($year);
                $district->setParameterName($parameter_name);
                $district->setParameterValue($itemParse['parameter_value'][$year]);
                $district->setCreated($dataTime);
                $district->setUpdated($dataTime);

                //Передаем менеджеру объект модели
                $em->persist($district);
                //Добавляем запись в таблицу
                $em->flush();
            }
        }

        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig');
    }

    public function testAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:test.html.twig');
    }

}