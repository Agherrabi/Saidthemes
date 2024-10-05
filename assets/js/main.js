jQuery(document).ready(function($) {
    // Lightbox elements
    const lightbox = $(`
        <div class="lightbox lightbox-gallery">
            <span class="close">&times;</span>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <!-- Slides will be inserted here dynamically -->
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    `);
    $('body').append(lightbox);

    let currentIndex = 0;
    let swiper; // Declare Swiper variable

    // Function to create Swiper slides dynamically from the gallery images
    function populateSlides(images) {
        const swiperWrapper = lightbox.find('.swiper-wrapper');
        swiperWrapper.empty(); // Clear existing slides

        images.each(function() {
            const imgSrc = $(this).attr('src'); // Get image source
            const slide = `<div class="swiper-slide"><img src="${imgSrc}" alt="gallery image"></div>`;
            swiperWrapper.append(slide); // Append slide
        });
    }

    // Function to show the lightbox with Swiper initialized
    function showLightbox(index) {
        const images = $('.tab-pane.active .gallery-images img'); // Get images of the active tab
        populateSlides(images); // Populate slides with the images

        // Initialize Swiper if not already initialized
        if (!swiper) {
            swiper = new Swiper('.swiper-container', {
                initialSlide: index,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                mousewheel: true,  // Enable mousewheel control
                keyboard: {
                    enabled: true,   // Enable keyboard control
                },
                loop: true, // Enable loop for continuous scrolling
            });
        } else {
            swiper.slideTo(index); // If Swiper is already initialized, go to the clicked image
        }

        lightbox.fadeIn(); // Show the lightbox
    }

    // Click event for gallery images
    $('.gallery-images img').on('click', function() {
        const images = $('.tab-pane.active .gallery-images img'); // Get images of active tab
        currentIndex = images.index(this); // Set current index
        showLightbox(currentIndex); // Show the lightbox with Swiper
    });

    // Close button event
    $('.close').on('click', function() {
        lightbox.fadeOut(); // Hide the lightbox
    });

    // Close lightbox when clicking outside the image
    lightbox.on('click', function(event) {
        if (event.target === this) {
            lightbox.fadeOut(); // Hide the lightbox
        }
    });
});


// frontend video : tabs video
jQuery(document).ready(function($) {
    $('.video-thumbnail').click(function(e) {
        e.preventDefault();

        let container = $(this);
        
        // Hide the image and show the iframe
        container.find('.thumbnail-image').hide();
        container.find('.video-iframe').show();
        
    });
});




