$(document).ready(function () {
    var count = 1,
        blocksLength = $('.comment-blocks').length;
    $('.comment-blocks').eq(count - 1).removeClass('disabled-div');
    addCurrent(count);
    $('.all-length').html(blocksLength);
    $('.next-comment-block').on('click', function () {
        if (count == blocksLength) {
            return false;
        }
        count++;
        addCurrent(count);
        $('.comment-blocks').addClass('disabled-div');
        $('.comment-blocks').eq(count - 1).removeClass('disabled-div');
    });
    $('.prev-comment-block').on('click', function () {
        if (count == 1) {
            return false;
        }
        count--;
        addCurrent(count);
        $('.comment-blocks').addClass('disabled-div');
        $('.comment-blocks').eq(count - 1).removeClass('disabled-div');
    });
    function addCurrent(count) {
        $('.current').html(count);
    }
});