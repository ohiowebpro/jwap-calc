jQuery(function($){


    $('.owp-calc-item a').click(function(){
        $(this).parent().find('.owp-calc-area').slideToggle();
        return false;
    });

    $('.owp-calc-submit').click(function() {
        $('.owp-calc-action-form-resp').text('');
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
            $('.owp-calc-savings').html('<a href="#owp-calc-action">Energy savings per 7 month production season:  $' + calc_total + '</a>').animate({opacity: 1}, 200);
            $('#calculated_savings').val(calc_total);
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
        $('.owp-calc-error').remove();
        $('.owp-form-missing').removeClass('owp-form-missing');
        e.preventDefault();
        let dataArr = $(this).serializeArray();
        let validate = true;
        $.each(dataArr, function(i, field){
            if (!field.value) {
                $('#' + field.name).addClass('owp-form-missing');
                validate = false;
            }
        });
        if (validate == true) {
            let data = $(this).serialize();
            let calc = $('.owp-calc-form').serialize();
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'post',
                data: data + '&' + calc,
                success: function (returnData) {
                    if (returnData.data == 'success') {
                        $('.owp-calc-action-form-resp').html('<p class="owp-calc-success">Thank you! We will get back to you shortly.</p>');
                    } else {
                        $('.owp-calc-action-form-resp').append('<p class="owp-calc-error">Error Sending data.</p>');
                    }
                    $('.owp-calc-form')[0].reset();
                    $('.owp-calc-action-form')[0].reset();
                    $('.owp-calc-action').slideUp();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('Request failed: ' + thrownError.message);
                    $('.owp-calc-action-form-resp').append('<p class="owp-calc-error">Error Sending data.</p>');
                },
            });

        } else {
            $('.owp-calc-action-form').append('<p class="owp-calc-error">Please fill out all fields.</p>');
        }
        //alert(JSON.stringify(data));
    });


});