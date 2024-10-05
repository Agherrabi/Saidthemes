jQuery(document).ready(function($) {
    // Activate tab functionality
    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        // Show the selected tab content and hide others
        $('.tab-pane').removeClass('show active');
        $(target).addClass('show active');

        // Activate the selected tab
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
    });
});


// Layout Masonry after all images have loaded
jQuery(document).ready(function($) {
    var $grid = $('.gallery-images').masonry({
        itemSelector: 'img',
        columnWidth: 'img',
        gutter: 15,
        fitWidth: true
    });

    // Layout Masonry after all images have loaded
    $grid.imagesLoaded().progress(function() {
        $grid.masonry('layout');
    });
});



// load viedo after click in thumbnail
jQuery(document).ready(function ($) {
    // Video thumbnail click event
    $('.video-thumbnail').click(function (e) {
        e.preventDefault(); // Prevent default link behavior

        const $parent = $(this).closest('.extra-gallery-item'); // Get the closest gallery item
        const videoId = $(this).data('video-id'); // Get the video ID from data attribute
        const $iframe = $parent.find('.video-iframe'); // Find the iframe within this item
        console.log(videoId);
        // If the iframe is currently hidden, set its src and show it
        if ($iframe.length) {
            const videoSrc = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1'; // You can add autoplay if desired
            $iframe.attr('src', videoSrc); // Set the iframe src
            $iframe.show(); // Show the iframe
        }
    });
});
