<?php
// src/AppMatrix/MatrixBundle/Controller/PageController.php

namespace AppMatrix\MatrixBundle\Controller;

use AppMatrix\MatrixBundle\Entity\District;
use AppMatrix\MatrixBundle\Entity\Form;
use AppMatrix\MatrixBundle\Form\EnquiryType;
use Symfony\Component\HttpFoundation\Request;
use AppMatrix\MatrixBundle\PHPExcel\importCSV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:index.html.twig');
    }


    public function formAction(Request $request) {

        /**
         * Форма отправки
         */

        $enquiry = new Form();

        $form = $this->createForm(EnquiryType::class, $enquiry);

        if ($request->isMethod($request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                // Получаем имя параметра
                $parameter_name = $request->request->get('app_matrix_matrix_bundle_enquiry_type')['parameter_name'];
                $parameter_type = $request->request->get('app_matrix_matrix_bundle_enquiry_type')['parameter_type'];
                $file = $request->files->get('app_matrix_matrix_bundle_enquiry_type')['file']->getClientOriginalName();
                $path = $request->files->get('app_matrix_matrix_bundle_enquiry_type')['file']->getRealPath();

                dump($request);


                dump($parameter_name);
                dump($parameter_type);
                dump($file);
                dump($path);

                //$root = str_replace("app", "", $this->get('kernel')->getRootDir()); // Путь от корня
                //$path =  $root . 'upload/import_csv/'; // Путь к дирректории импорта

                $phpCSV = new importCSV; // Создание объекта

                $correctFiles = $phpCSV->detectCSV($file); // Проверка на корректность .csv

                if (!empty($correctFiles['CORRECT'])) {

                    $arrParse = $phpCSV->parseCSV($path); // Парсинг корректных файлов

                    dump($arrParse);
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
                    //$parameter_name = "Продукция сельского хозяйства (в фактически действовавших ценах), тысяча рублей";

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
                            $district->setParameterType($parameter_type);
                            $district->setCreated($dataTime);
                            $district->setUpdated($dataTime);

                            //Передаем менеджеру объект модели
                            $em->persist($district);
                            //Добавляем запись в таблицу
                            $em->flush();
                        }
                    }

                    $result = 'true';
                } else {
                    $result = 'false';
                }



                // Perform some action, such as sending an email

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('AppMatrixMatrixBundle_form') . "?result=". $result);
            }
        }

        return $this->render('AppMatrixMatrixBundle:Page:form.html.twig', array(
            'form' => $form->createView()
        ));

    }



    public function testAction()
    {
        return $this->render('AppMatrixMatrixBundle:Page:test.html.twig');
    }

}