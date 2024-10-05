jQuery(document).ready(function($) {
    var mediaUploader;

    $('#upload_images_button').click(function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Images',
            button: {
                text: 'Add Images'
            },
            multiple: true
        });

        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').map(function(attachment) {
                attachment.toJSON();
                return attachment.id;
            });

            // Get the existing image IDs
            var existing_ids = $('#gallery_images').val() ? $('#gallery_images').val().split(',') : [];

            // Combine existing IDs with newly selected ones
            var all_ids = existing_ids.concat(attachments);

            // Update the hidden input field
            $('#gallery_images').val(all_ids.join(','));
            updateGalleryImages(all_ids);
        });

        mediaUploader.open();
    });

    // Function to update gallery images display
    function updateGalleryImages(ids) {
        $('#gallery_images_container').html(''); // Clear existing images
        ids.forEach(function(id) {
            var img_url = wp.media.attachment(id).attributes.url;
            $('#gallery_images_container').append('<div class="gallery-image" data-id="' + id + '">' +
                '<img src="' + img_url + '" width="100" height="100" />' +
                ' <a href="#" class="delete-image" style="color: red;">X</a>' +
                '</div>');
        });
    }

    // Event delegation to handle delete image click
    $('#gallery_images_container').on('click', '.delete-image', function(e) {
        e.preventDefault();
        var $imageDiv = $(this).closest('.gallery-image');
        var imageId = $imageDiv.data('id');

        // Get current IDs and remove the clicked one
        var current_ids = $('#gallery_images').val().split(',');
        current_ids = current_ids.filter(function(id) {
            return id !== imageId.toString(); // Remove the deleted image ID
        });

        // Update the hidden input and the gallery display
        $('#gallery_images').val(current_ids.join(','));
        $imageDiv.remove(); // Remove the image from the container
    });
});



// change the order of images in your gallery

jQuery(document).ready(function ($) {
    // Make gallery images sortable
    $('#gallery_images_container').sortable({
        update: function () {
            var imageOrder = [];
            $('#gallery_images_container .gallery-image').each(function () {
                imageOrder.push($(this).attr('data-id'));
            });
            $('#gallery_images').val(imageOrder.join(','));
        }
    });
});


// // admin add video 
jQuery(document).ready(function($) {
    let frame;
    let galleryIndex = parseInt($('#extra_gallery_index').val());

    $('#upload_extra_images_button').click(function(e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Images',
            button: {
                text: 'Use Images'
            },
            multiple: true
        });

        frame.on('select', function() {
            
            const attachments = frame.state().get('selection').toJSON();

            attachments.forEach(function(attachment) {
                $('#extra_gallery_images_container').append(`
                    <div class="extra-gallery-row">
                        <img src="${attachment.url}" width="100" />
                        <input type="hidden" name="extra_gallery_data[${galleryIndex}][image_id]" value="${attachment.id}" />
                        <label>Link:</label>
                        <input type="url" name="extra_gallery_data[${galleryIndex}][link]" class="widefat" />
                        <a href="#" class="remove-extra-gallery-row button">Remove</a>
                    </div>
                `);
                galleryIndex++;
            });

            $('#extra_gallery_index').val(galleryIndex);
        });

        frame.open();
    });

    $('#extra_gallery_images_container').on('click', '.remove-extra-gallery-row', function(e) {
        e.preventDefault();
        $(this).closest('.extra-gallery-row').remove();
    });

    // Clear all images and links functionality
    $('#clear_extra_gallery_button').click(function(e) {
        e.preventDefault();
        $('#extra_gallery_images_container').empty(); // Clear the container
        $('#gallery_images').val(''); // Clear the hidden input for gallery images
        $('#extra_gallery_index').val(0); // Reset gallery index

        // Make an AJAX call to clear data in the database
        let postId = $('#post_ID').val(); // Get the current post ID
        $.post(ajaxurl, {
            action: 'clear_extra_gallery_data',
            post_id: postId
        }, function(response) {
            if (response.success) {
                console.log('Extra gallery data cleared successfully.');
            } else {
                console.error('Error clearing extra gallery data: ', response.data);
            }
        });
    });

    // Condition: make link required
    $('#post').on('submit', function(e) {
        let valid = true;
        $('.extra-gallery-row input[name$="[link]"]').each(function() {
            if ($(this).val() === '') {
                valid = false;
                $(this).css('border', '1px solid red');
            } else {
                $(this).css('border', '1px solid #ccc');
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Please fill in all YouTube links before saving.');
        }
    });
});



// add presntation
jQuery(document).ready(function($) {
    let frame;

    $('#upload_presentation_image_button').click(function(e) {
        e.preventDefault();

        // Open media frame
        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use Image'
            },
            multiple: false
        });

        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#st_presentation_image').val(attachment.id);
            $('#presentation_image_container').html('<img src="' + attachment.url + '" width="100" />' +
                '<a href="#" id="remove_presentation_image_button" class="button">Remove Image</a>');
            $('#upload_presentation_image_button').hide();
        });

        frame.open();
    });

    // Remove the selected image
    $('#presentation_image_container').on('click', '#remove_presentation_image_button', function(e) {
        e.preventDefault();
        $('#st_presentation_image').val('');
        $('#presentation_image_container').html('');
        $('#upload_presentation_image_button').show();
    });
});
