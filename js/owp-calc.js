jQuery(function($){


    $('.owp-calc-item a').click(function(){
        $(this).parent().find('.owp-calc-val').slideToggle();
    });

});