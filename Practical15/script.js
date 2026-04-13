$(function () {
    var $slides = $('.slide');
    var index = 0;
    var total = $slides.length;
    var intervalId;

    function showSlide(nextIndex) {
        if (nextIndex < 0) {
            nextIndex = total - 1;
        }
        if (nextIndex >= total) {
            nextIndex = 0;
        }

        $slides.stop(true, true).removeClass('active').fadeOut(250);
        $slides.eq(nextIndex).stop(true, true).addClass('active').fadeIn(250);
        index = nextIndex;
    }

    function nextSlide() {
        showSlide(index + 1);
    }

    function prevSlide() {
        showSlide(index - 1);
    }

    function startAutoPlay() {
        intervalId = setInterval(nextSlide, 3000);
    }

    function resetAutoPlay() {
        clearInterval(intervalId);
        startAutoPlay();
    }

    $('#nextBtn').on('click', function () {
        nextSlide();
        resetAutoPlay();
    });

    $('#prevBtn').on('click', function () {
        prevSlide();
        resetAutoPlay();
    });

    $slides.hide().removeClass('active');
    $slides.eq(0).show().addClass('active');
    startAutoPlay();
});
