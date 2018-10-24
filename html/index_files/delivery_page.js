$(document).ready(function () {
    $('.delivery-btn').click(function () {
        $('.delivery-btn').removeClass('active');
        $(this).addClass('active');
        $('.delivery-content').addClass('disabled-div');
        $('.delivery-content[data-delivery-content="' + $(this).attr("data-delivery") + '"]').removeClass('disabled-div');
    });
});