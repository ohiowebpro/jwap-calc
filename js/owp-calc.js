jQuery(function($){


    $('.owp-calc-item a').click(function(){
        $(this).parent().find('.owp-calc-area').slideToggle();
        return false;
    });

    $('.owp-calc-submit').click(function() {
        $('.owp-calc-savings').animate({opacity: 0}, 400, function(){
            let calc_total = 0;
            $('.owp-calc-val').each(function(){
                if ($(this).is(":visible")) {
                    let linear = $(this).data('val');
                    let count = $(this).val();
                    if (linear && count && count > 0 && !isNaN(count)) {
                        calc_total = calc_total + (linear * count);
                    }
                }
            });
            calc_total = calc_total.toFixed(2);
            $('.owp-calc-savings').html('Energy savings per 7 month production season:  $' + calc_total).animate({opacity: 1}, 200);

            if (calc_total > 0) {
                $('.owp-calc-action').slideDown();
            }
        });
        return false;
    });

    //increment value
    $('.input-group').on('click', '.button-plus', function() {
        let thisInput = $(this).parent().find('.owp-calc-val');
        let value = parseInt(thisInput.val(), 10);
        if (!isNaN(value)) {
            thisInput.val(value + 1);
        }
        return false;
    });
    //decrement value
    $('.input-group').on('click', '.button-minus', function() {
        let thisInput = $(this).parent().find('.owp-calc-val');
        let value = parseInt(thisInput.val(), 10);
        if (!isNaN(value) && value > 0) {
            thisInput.val(value - 1);
        }
        return false;
    });

    //save contact info
    $(".owp-calc-action-form").submit(function(e){
        e.preventDefault();
        let data = $(this).serializeArray();

        alert(JSON.stringify(data));
    });


});