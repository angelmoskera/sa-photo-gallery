<?php

//Replace the custom genesis loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_archive_loop' );

function pgsa_custom_archive_loop() { 

//Loop Variables
$terms = get_terms('procedures');
$count = count($terms);

if ( $count > 0 ){
    foreach ( $terms as $term ) {

    	$term_link = get_term_link( $term );
    	?> 
		<?php
        $loop = new WP_Query( array( 
            'post_type' => 'photo-gallery',
            'post_per_page' => -1,
            'showposts' => 1,
            'orderby' => 'date',
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'procedures',
                    'field' => 'id',
                    'terms' => $term->term_id
                )
            )
        ));

        ?>
        <article itemscope itemtype="https://schema.org/ImageGallery" <?php post_class() ?>>
        <h1>Photogallery</h1>
        <?php
        // the loop
        while ($loop->have_posts()) : $loop->the_post();
        $patient_photos = get_post_meta( get_the_ID(), '_pgsa_photos_patient-photos', true );
                $view   = get_post_meta( get_the_ID(), '_pgsa_photos_view_select', true );
                $procedure = get_the_term_list( $post->ID, 'procedures', '', '' );
        ?>
            <section>
    			<h2 itemprop="name"><?php echo $procedure  ?></h2> 
    					<?php foreach ( $patient_photos as $index => $value ) { ?>
    			<?php if ($index <= 1) { ?> <!-- Show only the first TWO set of photos -->
    			<div class="one-fourth">
                    <div class="text-align:center;">Before</div>               
    				<a itemprop="url" href="<?php the_permalink() ?>"><img itemprop="image" alt="<?php 'Before'; ?>" title="<?php 'Before';?>" src="<?php echo $value['_pgsa_photos_before_photo']; ?>" /></a>
    			</div>
    			<div class="one-fourth">
                    <div class="text-align:center;">after</div>
    				<a itemprop="url" href="<?php the_permalink() ?>"><img itemprop="image" alt="<?php 'After'; ?>" title="<?php 'After';?>" src="<?php echo $value['_pgsa_photos_after_photo']; ?>" /></a>
    			</div>
    			
    			<?php } ?>
    		<?php } ?>
            </section>
		
		<?php 
        endwhile;
        ?>
        </article>
        <?php 
        // reset $post so that the rest of the template is in the original context
        wp_reset_postdata();
        ?>
 
        <?php
    }
}
}

// Add Custom Scripts & CSS
add_action( 'wp_enqueue_scripts', 'pgsa_script_css_archive' );
function pgsa_script_css_archive() {
	wp_enqueue_style( 'photo_gallery_archive_styles', plugin_dir_url( __FILE__ ) . 'css/photo-gallery-styles.css' );
}

genesis();