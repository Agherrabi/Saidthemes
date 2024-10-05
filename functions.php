<?php 
function my_enqueue_bootstrap() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('st-main-style', get_template_directory_uri() . '/assets/css/main.css');

    
    // js
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js', array('jquery'), null, true);
    wp_enqueue_script('st-main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_bootstrap');

// enqueue_script for the custom post St-gallery
function st_gallery_enqueue_media_uploader() {
    global $typenow;
    if ($typenow == 'st-gallery') {
        wp_enqueue_media(); // This line is crucial for using media uploader
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('st-gallery-uploader', get_template_directory_uri() . '/assets/js/gallery-uploader.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'st_gallery_enqueue_media_uploader');



// enqueue_script for tabs in forntend for the custom post 
function enqueue_gallery_tab_script() {
    wp_enqueue_script('gallery-tabs', get_template_directory_uri() . '/assets/js/gallery-tabs.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_gallery_tab_script');

// enqueue_script for tabs in forntend grid masonry 
function enqueue_gallery_masonry_script() {
    wp_enqueue_script('gallery-masonry', get_template_directory_uri() . '/js/gallery-masonry.js', array('jquery', 'masonry-js'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_gallery_masonry_script');

//fontawsome
function enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

//swiper lightbox
function enqueue_swiper() {
    // Swiper CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css');
    // Swiper JS
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper');

// custom CSS to the WordPress admin dashboard
function custom_admin_styles() {
    wp_enqueue_style('custom-admin-styles', get_template_directory_uri() . '/assets/css/admin-styles.css');
}
add_action('admin_enqueue_scripts', 'custom_admin_styles');



// theme Option
add_theme_support('menus');

function mytheme_setup() {
    add_theme_support('custom-logo', array(
        'height'      => 100, // Set the desired height for the logo
        'width'       => 300, // Set the desired width for the logo
        'flex-height' => true, // Allow flexible height
        'flex-width'  => true, // Allow flexible width
    ));
}
add_action('after_setup_theme', 'mytheme_setup');


// Menus

register_nav_menus(
    array(
        'top-menu' => 'Top Header Menu', 
        'header-menu' => 'Header Menu', 
        'footer-menu' => 'Footer Menu', 
    )
);


// add custom post Gallery

function create_st_gallery_post_type() {
    $labels = array(
        'name' => __('ST Galleries'),
        'singular_name' => __('ST Gallery'),
        'menu_name' => __('ST Galleries'),
        'name_admin_bar' => __('ST Gallery'),
        'add_new' => __('Add New'),
        'add_new_item' => __('Add New ST Gallery'),
        'new_item' => __('New ST Gallery'),
        'edit_item' => __('Edit ST Gallery'),
        'view_item' => __('View ST Gallery'),
        'all_items' => __('All ST Galleries'),
        'search_items' => __('Search ST Galleries'),
        'not_found' => __('No ST Galleries found.'),
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title',  'thumbnail'),
        'menu_icon' => 'dashicons-format-gallery', // icon for the custom post type
        'show_in_rest' => true,
    );
    
    register_post_type('st-gallery', $args);
}
add_action('init', 'create_st_gallery_post_type');


function st_gallery_add_meta_box() {
    add_meta_box(
        'st_gallery_images',
        __('Gallery Images'),
        'st_gallery_images_meta_box_callback',
        'st-gallery',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'st_gallery_add_meta_box');

function st_gallery_images_meta_box_callback($post) {
    wp_nonce_field('st_gallery_save_meta_box_data', 'st_gallery_meta_box_nonce');

    $gallery_images = get_post_meta($post->ID, 'st_gallery_images', true);

    echo '<a href="#" id="upload_images_button" class="button">Select Images</a>';
    echo '<input type="hidden" id="gallery_images" name="gallery_images" value="' . esc_attr($gallery_images) . '" />';
    echo '<div id="gallery_images_container" class="sortable-gallery">';

    if (!empty($gallery_images)) {
        $image_ids = explode(',', $gallery_images);
        foreach ($image_ids as $index => $image_id) {
            echo '<div class="gallery-image" data-id="' . esc_attr($image_id) . '" data-order="' . $index . '">';
            echo '<div class="gallery-image-container">';
            echo wp_get_attachment_image($image_id, 'thumbnail');
            echo ' <a href="#" class="delete-image" style="color: red;">X</a>';
            echo '</div>';
            echo '</div>';
        }
    }

    echo '</div>';
}



function st_gallery_save_meta_box_data($post_id) {
    if (!isset($_POST['st_gallery_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['st_gallery_meta_box_nonce'], 'st_gallery_save_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['gallery_images'])) {
        update_post_meta($post_id, 'st_gallery_images', sanitize_text_field($_POST['gallery_images']));
    }
}
add_action('save_post_st-gallery', 'st_gallery_save_meta_box_data');




// admin add video 
function st_extra_gallery_add_meta_box() {
    add_meta_box(
        'st_extra_gallery_images',
        __('Extra Gallery Images'),
        'st_extra_gallery_images_meta_box_callback',
        'st-gallery',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'st_extra_gallery_add_meta_box');

function st_extra_gallery_images_meta_box_callback($post) {
    wp_nonce_field('st_extra_gallery_save_meta_box_data', 'st_extra_gallery_meta_box_nonce');

    $extra_gallery_data = get_post_meta($post->ID, 'st_extra_gallery_data', true);
    $extra_gallery_data = is_array($extra_gallery_data) ? $extra_gallery_data : [];

    echo '<a href="#" id="upload_extra_images_button" class="button">Select Extra Images</a>';
    echo '<a href="#" id="clear_extra_gallery_button" class="button" style="margin-left: 10px;">Clear All Images and Links</a>';
    echo '<div id="extra_gallery_images_container">';

    if (!empty($extra_gallery_data)) {
        foreach ($extra_gallery_data as $index => $item) {
            $image_id = isset($item['image_id']) ? $item['image_id'] : '';
            $link = isset($item['link']) ? esc_url($item['link']) : '';

            echo '<div class="extra-gallery-row">';
            echo wp_get_attachment_image($image_id, 'thumbnail');
            echo '<input type="hidden" name="extra_gallery_data[' . $index . '][image_id]" value="' . esc_attr($image_id) . '" />';
            echo '<input placeholder="Link video youtube" type="url" name="extra_gallery_data[' . $index . '][link]" value="' . $link . '" class="widefat" />';
            echo '<a href="#" class="remove-extra-gallery-row button">Remove</a>';
            echo '</div>';
        }
    }

    echo '</div>';
    echo '<input type="hidden" id="extra_gallery_index" value="' . count($extra_gallery_data) . '" />';
}

add_action('wp_ajax_clear_extra_gallery_data', 'clear_extra_gallery_data_callback');


function st_extra_gallery_save_meta_box_data($post_id) {
    if (!isset($_POST['st_extra_gallery_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['st_extra_gallery_meta_box_nonce'], 'st_extra_gallery_save_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if extra gallery data is empty or if you want to clear it under certain conditions
    if (isset($_POST['extra_gallery_data']) && is_array($_POST['extra_gallery_data'])) {
        $extra_gallery_data = [];

        foreach ($_POST['extra_gallery_data'] as $data) {
            if (isset($data['image_id']) && !empty($data['link'])) {
                $extra_gallery_data[] = [
                    'image_id' => sanitize_text_field($data['image_id']),
                    'link' => esc_url_raw($data['link'])
                ];
            }
        }

        // Only save if there are valid entries
        if (!empty($extra_gallery_data)) {
            update_post_meta($post_id, 'st_extra_gallery_data', $extra_gallery_data);
        } else {
            // Clear the extra gallery data if no valid entries are found
            delete_post_meta($post_id, 'st_extra_gallery_data');
        }
    } else {
        // Clear the extra gallery data if no extra gallery data is submitted
        delete_post_meta($post_id, 'st_extra_gallery_data');
    }
}
add_action('save_post', 'st_extra_gallery_save_meta_box_data');


function st_presentation_add_meta_box() {
    add_meta_box(
        'st_presentation_meta_box',
        __('Presentation'),
        'st_presentation_meta_box_callback',
        'st-gallery', // Assuming your post type is named 'portfolio'
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'st_presentation_add_meta_box');

function st_presentation_meta_box_callback($post) {
    wp_nonce_field('st_presentation_save_meta_box_data', 'st_presentation_meta_box_nonce');

    $presentation_image = get_post_meta($post->ID, 'st_presentation_image', true);
    $presentation_title = get_post_meta($post->ID, 'st_presentation_title', true);
    $presentation_text = get_post_meta($post->ID, 'st_presentation_text', true);
    $presentation_active = get_post_meta($post->ID, 'st_presentation_active', true);

    echo '<p><label for="st_presentation_active">Activate Presentation:</label></p>';
    echo '<input type="checkbox" id="st_presentation_active" name="st_presentation_active" value="1" ' . checked($presentation_active, '1', false) . ' />';

    echo '<p><label for="st_presentation_image">Image:</label></p>';
    echo '<input type="hidden" id="st_presentation_image" name="st_presentation_image" value="' . esc_attr($presentation_image) . '" />';
    echo '<div id="presentation_image_container">';
    if ($presentation_image) {
        echo wp_get_attachment_image($presentation_image, 'thumbnail');
        echo '<a href="#" id="remove_presentation_image_button" class="button">Remove Image</a>';
    }
    echo '</div>';
    echo '<a href="#" id="upload_presentation_image_button" class="button"' . ($presentation_image ? ' style="display:none;"' : '') . '>Select Image</a>';

    echo '<p><label for="st_presentation_title">Title:</label></p>';
    echo '<input type="text" id="st_presentation_title" name="st_presentation_title" value="' . esc_attr($presentation_title) . '" class="widefat" />';

    echo '<p><label for="st_presentation_text">Text:</label></p>';
    echo '<textarea id="st_presentation_text" name="st_presentation_text" rows="4" class="widefat">' . esc_textarea($presentation_text) . '</textarea>';
}

function st_presentation_save_meta_box_data($post_id) {
    if (!isset($_POST['st_presentation_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['st_presentation_meta_box_nonce'], 'st_presentation_save_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the 'Activate Presentation' checkbox
    $presentation_active = (isset($_POST['st_presentation_active']) && $_POST['st_presentation_active'] == '1') ? '1' : '0';
    update_post_meta($post_id, 'st_presentation_active', $presentation_active);

    // Save the other fields
    if (isset($_POST['st_presentation_image'])) {
        update_post_meta($post_id, 'st_presentation_image', sanitize_text_field($_POST['st_presentation_image']));
    }

    if (isset($_POST['st_presentation_title'])) {
        update_post_meta($post_id, 'st_presentation_title', sanitize_text_field($_POST['st_presentation_title']));
    }

    if (isset($_POST['st_presentation_text'])) {
        update_post_meta($post_id, 'st_presentation_text', sanitize_textarea_field($_POST['st_presentation_text']));
    }
}
add_action('save_post', 'st_presentation_save_meta_box_data');





// theme option


