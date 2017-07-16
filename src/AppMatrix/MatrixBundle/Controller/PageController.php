<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\PHPExcel\importCSV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        $root = str_replace("app", "", $this->get('kernel')->getRootDir());
        $path =  $root . 'upload/import_csv/';
        dump($path);
        $phpCSV = new importCSV;
        $files = $phpCSV->readDir($path);
        var_dump($files);
        dump($files);

        $correctFiles = $phpCSV->detectCSV($files);
        dump($correctFiles);

        $arrParse = $phpCSV->parseCSV($path, $correctFiles["CORRECT"][0]);

        print_r($arrParse);
//       foreach ($correctFiles["CORRECT"] as $file) {
//           dump($file);
//
//       }

       //var_dump($arrParse);

       /* // Открываем файл
        $xls = PHPExcel_IOFactory::load('/var/www/html/matrix/upload/import_csv/'.$files[0]);
// Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
// Получаем активный лист
        $sheet = $xls->getActiveSheet();


        echo "<table>";

        for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
            echo "<tr>";

            $nColumn = PHPExcel_Cell::columnIndexFromString(
                $sheet->getHighestColumn());

            for ($j = 0; $j < $nColumn; $j++) {
                $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                echo "<td>$value</td>";
            }

            echo "</tr>";
        }
        echo "</table>";*/


        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig');
    }

    public function testAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:test.html.twig');
    }

}