<?php

//Replace the custom genesis loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_archive_loop' );

function pgsa_custom_archive_loop() { 

//Loop Variables
$terms = get_terms("procedures");
$count = count($terms);

if ( $count > 0 ){
    foreach ( $terms as $term ) {
    	$term_link = get_term_link( $term );
        echo '<h2><a href="' . esc_url( $term_link ) . '">' . $term->name . '</a></h2>';
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
        // the loop
        while ($loop->have_posts()) : $loop->the_post();
            // do loop content
        ?>
		<?php global $post;
		$patient_photos = get_post_meta( get_the_ID(), '_pgsa_photos_patient-photos', true );
		 ?>
		<?php foreach ( $patient_photos as $index => $value ) { ?>
			<?php if ($index <= 1) { ?> <!-- Show only the first TWO set of photos -->
			<div class="one-fourth ">							
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_before_photo']; ?>" /></a>
			</div>
			<div class="one-fourth">								
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_after_photo']; ?>" /></a>
			</div>
			<?php } ?>
		<?php } ?>


		<?php 
        endwhile;
        // reset $post so that the rest of the template is in the original context
        wp_reset_postdata();
        echo '<div class="clearfix"></div>';
    }
}
}

// Add Custom Scripts & CSS
add_action( 'wp_enqueue_scripts', 'pgsa_script_css_archive' );
function pgsa_script_css_archive() {
	wp_enqueue_style( 'photo_gallery_archive_styles', plugin_dir_url( __FILE__ ) . 'css/archive-styles.css' );
}

genesis();