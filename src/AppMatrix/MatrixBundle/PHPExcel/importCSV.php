<?php

namespace AppMatrix\MatrixBundle\PHPExcel;

use PHPExcel;
use Symfony\Component\HttpFoundation\Request;

class importCSV extends PHPExcel {

    public function readDir ($path) {
        $files = scandir($path);
        $files = array_slice($files, 2);
        return $files;
    }

    public function detectCSV ($file) {
        $chk_ext = explode(".",$file);
        if (strtolower(end($chk_ext)) == "csv") {
            $arFiles["CORRECT"][] = $file;
        } else {
            $arFiles["INCORRECT"][] = $file;
        }
        return $arFiles;
    }

    public function parseCSV ($file) {
        $openFile = fopen($file, "r"); // Прочитать файл
        $arMD = array();  // Объявить массив значений
        $i = 0; // Счетчик строк
        $arCoding = array("UTF-8", "ASCII", "Windows-1251", false);
        while ($data = fgetcsv($openFile, 1000, ";")) {
            foreach ($data as $k => $itemData) {

                /**
                 * Проверка на кодировку
                 */
                $coding = mb_detect_encoding($itemData);
                if (!in_array($coding, $arCoding)) {
                    $this->addFlash('coding', "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . $i . " в ячейке №: " . $k);
                    $arrErrors["coding"][] = "Кодировка не соответствует: UTF-8, ASCII или Windows-1251 - в строке №: " . $i . " в ячейке №: " . $k;
                }

                /**
                 * Перебор значений
                 */
                if ($itemData != '') {
                    if ($i == 0) {
                        $arMD["head_year"][] = mb_convert_encoding($itemData, 'UTF-8', 'Windows-1251' );
                    }
                }
                if ($itemData != '') {
                    if ($i > 0) {
                        if ($k == 0) {
                            $arMD[$i-1]["district_name"] = mb_convert_encoding($itemData, 'UTF-8', 'Windows-1251' );
                        }
                        if ($k > 0) {
                            $arMD[$i-1]["year"][] = mb_convert_encoding($arMD["head_year"][$k], 'UTF-8', 'Windows-1251' );
                            $arMD[$i-1]["parameter_value"][$arMD["head_year"][$k]] = floatval(str_replace(',', '.', str_replace(' ', '', strval(mb_convert_encoding( $itemData, 'UTF-8', 'Windows-1251' )))));
                        }


                    }
                }

            }

            $i++;

            /**
             * Прекращаем работу, если хоть одна ячейка не соответсвует кодировки!
             */

            if (!empty($arrErrors["coding"])) {
                echo "Ошибка кодировки!";
                dump($arrErrors);
                $arMD = array();
                break;
            }

        }

        array_splice($arMD, 0, 1);
        return $arMD;

    }


}

