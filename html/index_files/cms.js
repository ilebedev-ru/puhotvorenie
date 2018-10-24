$(document).ready(function () {
    var slideCount = 1,
        slideBlocksLength = 3;
        slideBlocksLength = $('.production-slide').length;
    // console.log(slideBlocksLength);
    // console.log($('.all-length'));
    $('.production-slide').eq(slideCount - 1).removeClass('disabled-div');
    addCurrent(slideCount);
    $('.all-slider-length').text(slideBlocksLength);
    $('.next-production-slide').on('click', function () {
        if (slideCount == slideBlocksLength) {
            return false;
        }
        slideCount++;
        addCurrent(slideCount);
        $('.production-slide').addClass('disabled-div');
        $('.production-slide').eq(slideCount - 1).removeClass('disabled-div');
    });
    $('.prev-production-slide').on('click', function () {
        if (slideCount == 1) {
            return false;
        }
        slideCount--;
        addCurrent(slideCount);
        $('.production-slide').addClass('disabled-div');
        $('.production-slide').eq(slideCount - 1).removeClass('disabled-div');
    });
    function addCurrent(slideCount) {
        $('.current').html(slideCount);
    }
});