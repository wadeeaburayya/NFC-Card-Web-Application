$('.popuparea').hide();
var mobilemenusub1 = 1;
$('.btnclose').click(function () {
    if (mobilemenusub1 == 1) {
        $('.popuparea').show();
        $('.popupbox').animate({ top: '250px' }, 300);
        $('.popupbox').animate({ top: '50px' }, 300);
        mobilemenusub1 = 2;
    }
    else {

        $('.popupbox').animate({ top: '250px' }, 300);
        $('.popupbox').animate({ top: '-600px' }, 300);
        setTimeout(function () {
            $('.popuparea').hide();
        }, 700);
        mobilemenusub1 = 1;
    }
});