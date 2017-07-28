import $ from 'jquery';
require("jquery-mousewheel");
//require('malihu-custom-scrollbar-plugin');
import fancybox from 'fancybox';
fancybox($);

require('chart.js');
import Chart from 'chart.js';


$(document).ready(function () {
    $('#delete-link').fancybox();

    $('.delete-link').on('click', function () {
      var data = $(this).data();
      $('#form_delete').find('#form_delete_yes').val(data["delete"]);
    });
    $('#form_delete_no').on('click', function () {
        $.fancybox.close( $('#delete-link') );
    });


    /**
     * Плавный скролл
     */
/*
    $(".slow-scroll--js").on("click", function (event) {
        console.log('test');
        //отменяем стандартную обработку нажатия по ссылке
        event.preventDefault();

        //забираем идентификатор бока с атрибута href
        var id  = $(this).attr('href'),

            //узнаем высоту от начала страницы до блока на который ссылается якорь
            top = $(id).offset().top;

        //анимируем переход на расстояние - top за 1500 мс
        $('body,html').animate({scrollTop: top}, 1500);
    });*/
});