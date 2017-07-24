import $ from 'jquery';
require("jquery-mousewheel");
//require('malihu-custom-scrollbar-plugin');
import fancybox from 'fancybox';
fancybox($);

$(document).ready(function () {
    $('#delete-link').fancybox();

    $('.delete-link').on('click', function () {
      var data = $(this).data();
      $('#form_delete').find('#form_delete_yes').val(data["delete"]);
    });
    $('#form_delete_no').on('click', function () {
        $.fancybox.close( $('#delete-link') );
    });
});