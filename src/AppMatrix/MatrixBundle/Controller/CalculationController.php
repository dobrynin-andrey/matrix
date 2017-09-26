<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppMatrix\MatrixBundle\Entity\Project;
use AppMatrix\MatrixBundle\Entity\Parameter;
use AppMatrix\MatrixBundle\Entity\ParameterType;
use AppMatrix\MatrixBundle\Entity\ParameterValues;
use Symfony\Component\HttpFoundation\Request;
use Buzz\Browser;
use CURLFile;

class CalculationController extends Controller
{

    public function calculationAction(Project $project, Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        /**
         * Первый вариант формирования массива (много запросов у бд)
         */


//        // Выводим районы текущего проекта
//        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
//        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("d");
//
//        $districtsInParameterValue = $qb->select("(d.district)")
//            ->where('d.project = '. $project->getId())
//            ->distinct(true)
//            ->getQuery()
//            ->getResult();
//
//        $arrDistricts =[];
//
//        // Берем уникальные значения параметров
//        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
//        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("p");
//
//        $allIdParameters = $qb->select("(p.parameter)")
//            ->where('p.project = '. $project->getId())
//            ->distinct(true)
//            ->getQuery()
//            ->getResult();
//
//        // Справочник всех типов параметров
//        $parametersTypeArray = $em->getRepository('AppMatrixMatrixBundle:ParameterType')->findAll();
//
//        // Формируем массив с параметрами и их типами
//        $parametersAll = [];
//
//        foreach ($districtsInParameterValue as $d => $itemDistrict) {
//            $parametersAll['districts'][$d]['id'] = $itemDistrict[1];
//            foreach ($parametersTypeArray as $k => $itemType) {
//                $p = 0;
//                foreach ($allIdParameters as $n => $itemId) {
//                    $parametersOne = $em->getRepository('AppMatrixMatrixBundle:Parameter')->find($itemId['1']);
//
//                    if ($itemType == $parametersOne->getParameterType()) {
//
//                        $parametersAll['districts'][$d]['parameterType'][$k]['id'] = $itemType->getId();
//                        $parametersAll['districts'][$d]['parameterType'][$k]['nameType'] = $itemType->getParameterType();
//                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$p]['parameterId'] = $parametersOne;
//
//                        $parameterValues = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
//                            [
//                                'project' => $project,
//                                'parameter' => $parametersOne,
//                                'district' => $itemDistrict,
//                            ]
//                        );
//                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$p]['parameterValue'] = $parameterValues;
//                        $p++;
//                    }
//                }
//            }
//        }
//
//        dump($parametersAll['districts'][0]);
//        dump($parametersAll);



        /**
         * Второй вариант формирования массива
         */


        // Выводим районы текущего проекта
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("d");

        $districtsInParameterValue = $qb->select("(d.district)")
            ->where('d.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();


        // Функция фильтрации объектов по клику
        function FilterDistrict ($this_is, $query, $districtsInParameterValue) {

            $arFiltersId = [];
            $arDistrictItems = [];
            $arDiffResult = [];

            // Проверка параметра
            if ($query == "mr") {
                $districtType = "Муниципальный район";
            } elseif ($query == "go") {
                $districtType = "Городской округ";
            } else {
                goto end;
            }

            // Выборка объектов запрошенного типа
            $em = $this_is->getDoctrine()->getManager();
            $districtFilter =  $em->getRepository('AppMatrixMatrixBundle:District')->findBy(
                [
                    'district_type' => $districtType
                ]);

            // Получение массива запрошенных объектов
            foreach ($districtFilter as $dF) {
                $arFiltersId[] = $dF->getId();
            }
            // Перебор массива полученного на предыдущем запросе, для извабления от вложенности массивов
            end:
            foreach ($districtsInParameterValue as $itemD) {
                $arDistrictItems[] = $itemD[1];
            }

            // Сравнение двух массивов для получения нужных id
            if ($arFiltersId) {
                $arDiffResult["result"] = array_intersect($arDistrictItems, $arFiltersId);
                $arDiffResult["error"] = false;
                if (empty($arDiffResult["result"])) {
                    $arDiffResult["result"] = $arDistrictItems;
                    $arDiffResult["error"] = true;
                }
            } else {
                $arDiffResult["result"] = $arDistrictItems;
                $arDiffResult["error"] = false;
            }

            return $arDiffResult;

        }

        $resultFunc["error"] = [];
        // Применение функции фильтрации
        $resultFunc = FilterDistrict($this, $request->query->get('filter'), $districtsInParameterValue);

        $districtsInParameterValue = $resultFunc["result"];

        if ($resultFunc["error"]) {
            $this->addFlash('error_district', 'Объекты типа: Городской округ - отсутствуют!');
        }



        $arrDistricts =[];

        // Справочник всех типов параметров
        $parametersTypeArray = $em->getRepository('AppMatrixMatrixBundle:ParameterType')->findAll();


        // Берем уникальные значения параметров
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $em->getRepository("AppMatrixMatrixBundle:ParameterValues")->createQueryBuilder("p");


        $allIdParameters = $qb->select("(p.parameter)")
            ->where('p.project = '. $project->getId())
            ->distinct(true)
            ->getQuery()
            ->getResult();

        $allParametersBD = [];

        foreach ($allIdParameters as $itemIdParameter) {
            // Справочник всех параметров проекта
            $parametersBD = $em->getRepository('AppMatrixMatrixBundle:Parameter')->findBy(
                [
                    'id' => $itemIdParameter[1]
                ]
            );
            array_push($allParametersBD,$parametersBD[0]);

        }

        $parameterValues = $em->getRepository('AppMatrixMatrixBundle:ParameterValues')->findBy(
            [
                'project' => $project
            ]
        );

        // Формируем массив с параметрами и их типами
        $parametersAll = [];

        foreach ($districtsInParameterValue as $d => $itemDistrict) {
            $parametersAll['districts'][$d]['id'] = $em->getRepository('AppMatrixMatrixBundle:District')->findBy(
                [
                    'id' => $itemDistrict
                ])[0];

            $parametersAll['districts'][$d]['id'] = $itemDistrict;
            foreach ($parametersTypeArray as $k => $itemType) {
                $i = 0;
                foreach ($allParametersBD as $itemId) {

                    if ($itemType->getId() == $itemId->getParameterType()->getId()) {
                        $parametersAll['districts'][$d]['parameterType'][$k]['id'] = $itemType->getId();
                        $parametersAll['districts'][$d]['parameterType'][$k]['nameType'] = $itemType->getParameterType();
                        $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$i]['parameterId'] = $itemId;
                        foreach ($parameterValues as $itemValue) {
                            if ($itemValue->getDistrict()->getId() == $itemDistrict &&
                            $itemValue->getParameter()->getId() == $itemId->getId()) {
                                $parametersAll['districts'][$d]['parameterType'][$k]['parameters'][$i]['parameterValues'][] = $itemValue;
                            }
                        }
                        $i++;
                    }
                }
            }
        }


        /**
         *  Убираем из массива элементы у которых нет значений у годов
         */

        foreach ($parametersAll['districts'] as $d => $district) {
            foreach ($district['parameterType'] as $t => $parameterType) {
                foreach ($parameterType['parameters'] as $p => $itemParameter) { // Перебираем массив параметров
                    if (isset($itemParameter['parameterValues'])) {
                        foreach ($itemParameter['parameterValues'] as $v => $itemValue) { // Перебираем значение каждого параметра
                            if ($itemValue->getParameterValue() == -1) {

                                $paramName = $itemValue->getParameter()->getParameterName();
                                $districtName = $itemValue->getDistrict()->getDistrictName();
                                $this->addFlash('error_district', 'Объект: ' . $districtName . ' не учавствует в расчетах, из-за отуствия значения в параметре - ' . $paramName . '!');
                                unset($parametersAll['districts'][$d]);
                                continue;
                            }
                        }
                    } else {
                        unset($parametersAll['districts'][$d]);
                        continue;
                    }
                }
            }
        }

        /**
         *  Построение и рассчет матриц. Этап 1
         */

        // Перебор районов (districts)

        $M1 = [];

        foreach ($parametersAll['districts'] as $d => $district) {
            foreach ($district['parameterType'] as $t => $parameterType) {

                /**
                 *  Матрица М1
                 */

                // Вычисляем по первому параметру
                if ($parameterType['id'] == 1) {
                    foreach ($parameterType['parameters'] as $p => $itemParameter) { // Перебираем массив параметров
                        foreach ($itemParameter['parameterValues'] as $v => $itemValue) { // Перебираем значение каждого параметра
                            // Для Результаты - Внешние
                            for ($i = 0; $i < count($district['parameterType'][2]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $thirdTypeValue =  $district['parameterType'][2]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()]['zone-1'][$itemParameter['parameterId']->getId()][$district['parameterType'][2]['parameters'][$i]['parameterId']->getId()] = $yearValue / $thirdTypeValue;

                            }


                            // Для Результаты - Внутренние
                            for ($i = 0; $i < count($district['parameterType'][3]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра
                                $fourthTypeValue = $district['parameterType'][3]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()]['zone-2'][$itemParameter['parameterId']->getId()][$district['parameterType'][3]['parameters'][$i]['parameterId']->getId()] = $yearValue / $fourthTypeValue;

                            }
                        }
                    }
                }

                // Вычисляем по второму параметру
                if ($parameterType['id'] == 2) {
                    foreach ($parameterType['parameters'] as $p => $itemParameter) { // Перебираем массив параметров
                        foreach ($itemParameter['parameterValues'] as $v => $itemValue) { // Перебираем значение каждого параметра
                            // Для Результаты - Внешние
                            for ($i = 0; $i < count($district['parameterType'][2]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $thirdTypeValue = $district['parameterType'][2]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра

                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()]['zone-3'][$itemParameter['parameterId']->getId()][$district['parameterType'][2]['parameters'][$i]['parameterId']->getId()] = $yearValue / $thirdTypeValue;

                            }


                            // Для Результаты - Внутренние
                            for ($i = 0; $i < count($district['parameterType'][3]['parameters']); $i++) { // Перебираем значения второго типа

                                $yearValue = $itemValue->getParameterValue(); // Значение текущего параметра

                                $fourthTypeValue = $district['parameterType'][3]['parameters'][$i]['parameterValues'][$v]->getParameterValue(); // Значение параметра
                                //Запись в M1
                                $M1[$district['id']][$itemValue->getYear()]['zone-4'][$itemParameter['parameterId']->getId()][$district['parameterType'][3]['parameters'][$i]['parameterId']->getId()] = $yearValue / $fourthTypeValue;


                            }

                        }
                    }
                }

            }

        }

        /**
         * Этап 2. Деление одного года на другой
         */

        $resultM2 = [];
        foreach ($M1 as $dm => $districtM1) { // Перебор районов

            $maxArray = max($districtM1); // Берем массив с более ранним годом
            $minArray = min($districtM1); // Берем массив с более поздним годом

            foreach ($maxArray as $iZ => $itemZone) { // Перебираем ранний год

                foreach ($itemZone as $mA => $itemMaxArray) { // Перебираем элементы каждый зоны

                    foreach ($itemMaxArray as $iMA => $valueItemMaxArray) { // Берем значение кадого элемента зоны

                        $resultM2[$dm][$iZ][$mA][$iMA] = $valueItemMaxArray / $minArray[$iZ][$mA][$iMA]; // Пишем в один структурированный массив  $resultM2[район][зона][элемент][значение]
                    }
                }
            }
        }



        /**
         * Этап 3. Рассчитываются обобщающие коэффициенты внутренней и внешней результативности использования внутренних и внешних ресурсов (матрица М3 - t)
         */

        /**
         *  Также сразу применим:
         *  Этап 4. Сопоставительный анализ объектов как «точек роста».
         *
         *  Расчет интегрированного показателя приоритетности «полюса роста»
         *
         *  (Добавим в массив значений "Показатель приоритетности точки роста (ПТР)"
        )
         */

        foreach ($districtsInParameterValue as $itemDistrict) { // Формируем массив ID районов из ранее найденного массива
            array_push($arrDistricts, $itemDistrict);
        }

        $districts = $em->getRepository('AppMatrixMatrixBundle:District')->findBy( // Находим объекты из БД
            ['id' => $arrDistricts]
        );


        $coefficients = []; // Объявляем массив коэффициентов
        $coefficientsJSON = []; // Объявляем массив коэффициентов для JSON

        foreach ($resultM2 as $rm => $itemResultM2) { // Перебирвем районы

            /*
             * Добавим название района в массив коэффиентов
             */

            foreach ($districts as $itemDistrict) { // Перебираем найденные объекты
                if ($rm === $itemDistrict->getId()) {  // Сравниваем Id из текущего массива
                    $coefficients[$rm]['name'] = $itemDistrict->getDistrictName(); // Присваеваем имя текущему элементу массива
                    $coefficients[$rm]['id'] = $itemDistrict->getId(); // Присваеваем id текущему элементу массива
                }

            }


            // 1. Зона показателей внешней эффективности (левый-верхний прямоугольник). => Коэффициент мультипликативности

            // 2. Зона показателей внутренней эффективности (правый-верхний прямоугольник). => Коэффициент адаптивности

            // 3. Зона показателей внешней эффективности использования собственных ресурсов (левый-нижний прямоугольник). => Коэффициент синергетичности

            // 4. Зона показателей внутренней эффективности использования собственных ресурсов (правый-нижний прямоугольник). => Коэффициент интенсивности


            $kSum = 0; // Обнуляем значене суммы коэффициентов
            $itemCount = 0; // Обнуляем количество параметров зоны
            $coefficientsSum = 0; // Обнуляем сумму всех зон

            foreach ($itemResultM2 as $iZ => $itemZone) { // Перебираем заны

                foreach ($itemZone as $iP => $itemParameter) { // Перебираем параметры

                    $itemCount = $itemCount + count($itemParameter); // Считаем сколько всего параметров

                    foreach ($itemParameter as $iPV => $itemParameterValue) { // Перебираем значения параметра

                        $kSum = $kSum + $itemParameterValue; // Складываем все значения параметров

                    }

                }

                // Задаем имя каждого коэффициента
                switch ($iZ) {
                    case 'zone-1':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент мультипликативности';
                        break;
                    case 'zone-2':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент адаптивности';
                        break;
                    case 'zone-3':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент синергетичности';
                        break;
                    case 'zone-4':
                        $coefficients[$rm][$iZ]['name'] = 'Коэффициент интенсивности';
                        break;
                }

                $coefficients[$rm][$iZ]['value'] = $kSum / $itemCount; // Пишем в массив коэффициентов
                $coefficientsSum = $coefficientsSum + $coefficients[$rm][$iZ]['value'];

                $kSum = 0; // Обнуляем значене суммы коэффициентов
                $itemCount = 0; // Обнуляем количество параметров зоны

            }
            $coefficients[$rm]['PTR'] = $coefficientsSum / 4; // Пишем в массив коэффициентов значение Показатель приоритетности точки роста (ПТР)"
            $coefficientsJSON['PTR'][] = $coefficientsSum / 4; // Запишем данные для JSON
            $coefficientsSum = 0; // Обнуляем сумму всех зон

        }


        /**
         *  Параметрические признаки «точек роста»
         */

        /*
            1. Точка роста с внутренним источником
            2. Точка роста с внешним источником
            3. Отрицательная точка роста с внутренним источником
            4. Отрицательная точка роста с внешним источником
            5. Точка развития с внутренним источником
            6. Точка развития с внешним источником
            7. Точка развития с отрицательным ростом
            8. Отрицательная точка развития

         */

        $pointers = [];
        $districtMaps = [];

        foreach ($coefficients as $iD => $itemDistrict) { // Перебирвем районы

           /*
            * Добавим название района в массив точек роста
            */

            foreach ($districts as $itemDistrictDB) { // Перебираем найденные объекты
                if ($iD === $itemDistrictDB->getId()) {  // Сравниваем Id из текущего массива
                    $pointers[$iD]['name'] = $itemDistrictDB->getDistrictName(); // Присваеваем имя текущему элементу массива
                    $pointers[$iD]['id'] = $itemDistrictDB->getId(); // Присваеваем id текущему элементу массива

                    // Дополнительный массив для карты
                    $districtMaps[] = str_replace(' муниципальный', '',$itemDistrictDB->getDistrictName());
                   // $itemDistrictName = str_replace(' муниципальный', '',$itemDistrictDB->getDistrictName());
                   // $url = 'http://nominatim.openstreetmap.org/search?q=Алтайский ' . $itemDistrictName . '&format=json&polygon_geojson=1';
                    //$r = Request::create( 'http://nominatim.openstreetmap.org/search?q=Алтайский ' . $itemDistrictName . '&format=json&polygon_geojson=1', 'GET' );

                    //$jsonout = json_decode($r);
                   // print_r($jsonout[0]);
                    //dump($r);
                   /* $curl = new Curl();
                    $browser = new Browser();
                    $browser->setClient($curl);
                    $response = $browser->get('http://nominatim.openstreetmap.org/search?q=Алтайский ' . $itemDistrictName . '&format=json&polygon_geojson=1');

                    //$districtMaps["JSON"] = $browser->getLastRequest()."\n";
// $response is an object.
// You can use $response->getContent() or $response->getHeaders() to get only one part of the response.
                    $districtMaps["JSON"][] = $response->getContent();
                    dump($districtMaps);*/

                   /* // инициализируем cURL
                    $curl = curl_init();
                    //Дальше устанавливаем опции запроса в любом порядке
//Здесь устанавливаем URL к которому нужно обращаться
                    curl_setopt($curl, CURLOPT_URL, $url);
//Настойка опций cookie
                    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cook.txt');//сохранить куки в файл
                    curl_setopt($curl, CURLOPT_COOKIEFILE, 'cook.txt');//считать куки из файла
//устанавливаем наш вариат клиента (браузера) и вид ОС
                    curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.00 (Windows NT 5.1; U; ru) Presto/2.2.0");
//Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
                    curl_setopt($curl, CURLOPT_FAILONERROR, 1);
//Устанавливаем значение referer - адрес последней активной страницы
                    //curl_setopt($curl, CURLOPT_REFERER, 'http://www.olark.com/');
//Максимальное время в секундах, которое вы отводите для работы CURL-функций.
                    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
                    curl_setopt($curl, CURLOPT_HTTPGET, 1); // устанавливаем метод POST
//ответственный момент здесь мы передаем наши переменные
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $url);
//Установите эту опцию в ненулевое значение, если вы хотите, чтобы шапка/header ответа включалась в вывод.
                    curl_setopt($curl, CURLOPT_HEADER, 0);
//Внимание, важный момент, сертификатов, естественно, у нас нет, так что все отключаем
                    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);// не проверять SSL сертификат
                    curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);// не проверять Host SSL сертификата
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);// разрешаем редиректы
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $html = curl_exec($curl); // выполняем запрос и записываем в переменную
                    curl_close($curl); // заканчиваем работу curl
                    dump($html);

                    die();*/
                }

            }

            // 1. Точка роста с внутренним источником

            $pointers[$iD]['pointers'][0]['name'] = 'Точка роста с внутренним источником';
            $pointers[$iD]['pointers'][0]['value'] = 3 * $itemDistrict['zone-4']['value'] - $itemDistrict['zone-1']['value'] - $itemDistrict['zone-3']['value'] - $itemDistrict['zone-2']['value'];


            //  2. Точка роста с внешним источником

            $pointers[$iD]['pointers'][1]['name'] = 'Точка роста с внешним источником';
            $pointers[$iD]['pointers'][1]['value'] = 3 * $itemDistrict['zone-2']['value'] - $itemDistrict['zone-1']['value'] - $itemDistrict['zone-3']['value'] - $itemDistrict['zone-4']['value'];


            //  3. Отрицательная точка роста с внутренним источником

            $pointers[$iD]['pointers'][2]['name'] = 'Отрицательная точка роста с внутренним источником';
            $pointers[$iD]['pointers'][2]['value'] = $itemDistrict['zone-1']['value'] + $itemDistrict['zone-3']['value'] + $itemDistrict['zone-2']['value'] - 3 * $itemDistrict['zone-4']['value'];


            // 4. Отрицательная точка роста с внешним источником

            $pointers[$iD]['pointers'][3]['name'] = 'Отрицательная точка роста с внешним источником';
            $pointers[$iD]['pointers'][3]['value'] = $itemDistrict['zone-1']['value'] + $itemDistrict['zone-3']['value'] + $itemDistrict['zone-4']['value'] - 3 * $itemDistrict['zone-2']['value'];


            // 5. Точка развития с внутренним источником

            $pointers[$iD]['pointers'][4]['name'] = 'Точка развития с внутренним источником';
            $pointers[$iD]['pointers'][4]['value'] = 3 * $itemDistrict['zone-3']['value'] - $itemDistrict['zone-2']['value'] - $itemDistrict['zone-4']['value'] - $itemDistrict['zone-1']['value'];


            // 6. Точка развития с внешним источником

            $pointers[$iD]['pointers'][5]['name'] = 'Точка развития с внешним источником';
            $pointers[$iD]['pointers'][5]['value'] = 3 * $itemDistrict['zone-1']['value'] - $itemDistrict['zone-3']['value'] - $itemDistrict['zone-2']['value'] - $itemDistrict['zone-4']['value'];


            // 7. Точка развития с отрицательным ростом

            $pointers[$iD]['pointers'][6]['name'] = 'Точка развития с отрицательным ростом';
            $pointers[$iD]['pointers'][6]['value'] = 3 * $itemDistrict['zone-1']['value'] - $itemDistrict['zone-3']['value'] - 2 * $itemDistrict['zone-2']['value'] - $itemDistrict['zone-4']['value'];


            // 8. Отрицательная точка развития

            $pointers[$iD]['pointers'][7]['name'] = 'Отрицательная точка развития';
            $pointers[$iD]['pointers'][7]['value'] = $itemDistrict['zone-3']['value'] + $itemDistrict['zone-2']['value'] + $itemDistrict['zone-4']['value'] - 3 * $itemDistrict['zone-1']['value'];

        }


        $arResult['parameters'] = $parametersAll;
        $arResult['M1'] = $M1;
        $arResult['resultM2'] = $resultM2;
        $arResult['coefficients'] = $coefficients;
        $arResult['pointers'] = $pointers;
        $arResult['districtMaps'] = $districtMaps;


        return $this->render('AppMatrixMatrixBundle:Page:calculation.html.twig', [
            'arResult' => $arResult,
            'projects' => $project,
            'coefficientsJSON' => $coefficientsJSON
        ]);
    }

}