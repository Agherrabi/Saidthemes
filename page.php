<?php get_header(); ?>

<section class="page-wrap">
    <div class="container">
       
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="galleryTab" role="tablist">
            <?php
            $st_gallery_posts = new WP_Query(array(
                'post_type' => 'st-gallery',
                'posts_per_page' => -1
            ));

            if ($st_gallery_posts->have_posts()):
                $i = 0;
                while ($st_gallery_posts->have_posts()): $st_gallery_posts->the_post(); ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($i === 0) ? 'active' : ''; ?>" id="tab-<?php echo get_the_ID(); ?>" data-toggle="tab" href="#gallery-<?php echo get_the_ID(); ?>" role="tab">
                            <?php the_title(); ?>
                        </a>
                    </li>
                <?php
                $i++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="galleryTabContent">
            <?php
            if ($st_gallery_posts->have_posts()):
                $i = 0;
                while ($st_gallery_posts->have_posts()): $st_gallery_posts->the_post();

                    $gallery_images = get_post_meta(get_the_ID(), 'st_gallery_images', true);
                    $image_ids = explode(',', $gallery_images);

                    // Fetch extra gallery data
                    $extra_gallery_data = get_post_meta(get_the_ID(), 'st_extra_gallery_data', true);

                    // Fetch presentation data
                    $presentation_image_id = get_post_meta(get_the_ID(), 'st_presentation_image', true);
                    $presentation_title = get_post_meta(get_the_ID(), 'st_presentation_title', true);
                    $presentation_text = get_post_meta(get_the_ID(), 'st_presentation_text', true);
                    ?>
                    <div class="tab-pane fade <?php echo ($i === 0) ? 'show active' : ''; ?>" id="gallery-<?php echo get_the_ID(); ?>" role="tabpanel">
                        
                        <!-- Presentation -->
                        <?php
                        $presentation_active = get_post_meta(get_the_ID(), 'st_presentation_active', true);
                        $presentation_image_id = get_post_meta(get_the_ID(), 'st_presentation_image', true);
                        $presentation_title = get_post_meta(get_the_ID(), 'st_presentation_title', true);
                        $presentation_text = get_post_meta(get_the_ID(), 'st_presentation_text', true);

                        if ($presentation_active) :
                        ?>
                        <div class="gallery-presntaion">
                            <?php if (!empty($presentation_image_id)) : ?>
                                <div class="presentation-image">
                                    <?php echo wp_get_attachment_image($presentation_image_id, 'large'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="presentation-content">
                                <?php if (!empty($presentation_title)) : ?>
                                    <h3 class="presentation-title"><?php echo esc_html($presentation_title); ?></h3>
                                <?php endif; ?>

                                <?php if (!empty($presentation_text)) : ?>
                                    <p class="presentation-text"><?php echo esc_html($presentation_text); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <!-- End Presentation -->


                        <!-- Videos -->
                        <?php if (!empty($extra_gallery_data)) { ?>
    <div class="extra-gallery-images">
        <?php
        foreach ($extra_gallery_data as $index => $item) {
            $image_id = isset($item['image_id']) ? $item['image_id'] : '';
            $link = isset($item['link']) ? esc_url($item['link']) : '';

            // Extract the video ID from the URL
            if ($link) {
                preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $link, $matches);
                $video_id = !empty($matches) ? $matches[1] : '';
            }

            if ($image_id && !empty($video_id)) {
                echo '<div class="extra-gallery-item" id="extra-gallery-item-' . esc_attr($index) . '">';
                echo '<a href="#" class="video-thumbnail" data-video-id="' . esc_attr($video_id) . '">';
                echo wp_get_attachment_image($image_id, 'large', false, array('class' => 'thumbnail-image'));
                echo '</a>';
                // Start with an empty src and hide it
                echo '<iframe class="video-iframe" width="100%" height="400" src="" frameborder="0" allowfullscreen style="display:none;"></iframe>';
                echo '</div>';
            }
        }
        ?>
    </div>
<?php } ?>

                        <!-- End Videos -->

                        <!-- Start gallery-images -->
                        <div class="gallery-images">
                            <?php
                            if (!empty($gallery_images)) {
                                foreach ($image_ids as $image_id) {
                                    echo wp_get_attachment_image($image_id, 'large');
                                }
                            }
                            ?>
                        </div>
                        <!-- End gallery-images -->
                       
                    </div>
                    <?php
                    $i++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
