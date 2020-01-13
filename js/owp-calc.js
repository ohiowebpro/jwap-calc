jQuery(function($){


    $('.owp-calc-item a').click(function(){
        $(this).parent().find('.owp-calc-area').slideToggle();
        return false;
    });

    $('.owp-calc-submit').click(function() {
        $('.owp-calc-output').slideUp(function(){
            let calc_total = 0;
            $('.owp-calc-val').each(function(){
                let linear = $(this).data('val');
                let count  = $(this).val();
                if (linear && count && count > 0) {
                    calc_total = calc_total + (linear * count);
                }

            });
            calc_total = calc_total.toFixed(2);

            $('.owp-calc-savings').html('Energy savings per 7 month production season:  $' + calc_total);
            $('.owp-calc-output').slideDown();
        });

        return false;
    });

});