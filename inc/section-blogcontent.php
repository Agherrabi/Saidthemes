<?php  if (have_posts()) : while (have_posts()) : the_post(); ?>
    
    <p><?php echo get_the_date('d/m/Y');?></p>
    <?php  the_content(); ?>

    <p><?php echo "Posted by : ". get_the_author_meta('nickname');?></p>
    
    <?php
        $tags =  get_the_tags(); 
        if($tags) {
            foreach($tags as $tag):?>
                <a class="btn btn-success" href="<?php echo get_tag_link($tag->term_id); ?>">
                    <?php  echo $tag->name; ?>
                </a>
            <?php  endforeach; ?>
        <?php } ?>
       


<?php  endwhile;  else :echo '<p>No content found</p>';  endif;  ?>