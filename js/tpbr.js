jQuery(document).ready(function(){

    var current_color = jQuery('#tpbrcc').text();

    var $box = jQuery('#colorPicker');
    $box.tinycolorpicker();
    var box = $box.data("plugin_tinycolorpicker");

    if (current_color) {
        box.setColor(current_color);
    }

    var want_button = jQuery( ".tpbr_yn_button" ).val();
    if (want_button == 'button') {
        jQuery( ".tpbr_button_box" ).show();
    }
    if (want_button == 'nobutton') {
        jQuery( ".tpbr_button_box" ).hide();
    }

    jQuery( ".tpbr_yn_button" ).on('change', function() {
         var want_button = jQuery( ".tpbr_yn_button" ).val();
         if (want_button == 'button') {
             jQuery( ".tpbr_button_box" ).slideDown(100);
         }
         if (want_button == 'nobutton') {
             jQuery( ".tpbr_button_box" ).slideUp(100);
         }
    })

});
