<?php

//Replace the custom genesis loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_archive_loop' );

function pgsa_custom_archive_loop() { ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php
    $patient_photos = get_post_meta( get_the_ID(), '_pgsa_photos_patient-photos', true );
		    $age 	= get_post_meta( get_the_ID(), '_pgsa_info_age', true );
		    $gender = get_post_meta( get_the_ID(), '_pgsa_info_gender_select', true );
		    $ethnic = get_post_meta( get_the_ID(), '_pgsa_info_ethnicity_select', true );
		    $height = get_post_meta( get_the_ID(), '_pgsa_info_height', true );
		    $weight = get_post_meta( get_the_ID(), '_pgsa_info_weight', true );
	    	$detail = get_post_meta( get_the_ID(), '_pgsa_info_procedure_detail', true );
	// Echo the metadata
	?>

	<article <?php post_class() ?> >

		<?php foreach ( $patient_photos as $index => $value ) { ?>
			<?php if ($index == 0) { ?> <!-- Show only first set of photos -->
			<div class="one-fourth first">							
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_before_photo']; ?>" /></a>
			</div>
			<div class="one-fourth">								
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_after_photo']; ?>" /></a>
			</div>
			<?php } ?>
		<?php } ?>

		<div class="two-fourths pgsa-patient-info">
			
 			<h3><?php echo get_the_term_list( $post->ID, 'procedures', '', ', ', '' ); ?> </h3>
 			<section class="one-half first">
 			<ul>
 				<li><span class="pgsa-info-label-archive pgsa-info-label-gender">Age:</span> <?php echo esc_html( $age ); ?></li>
 				<li><span class="pgsa-info-label-archive pgsa-info-label-gender">Gender:</span> <?php echo esc_html( $gender ); ?></li>
 				<li><span class="pgsa-info-label-archive pgsa-info-label-gender">Ethnicity:</span> <?php echo esc_html( $ethnic ); ?></li>
 			</ul>
 			</section>
 			<section class="one-half">
 			<ul>
 				<li><span class="pgsa-info-label-archive pgsa-info-label-gender">Height:</span> <?php echo esc_html( $height ); ?></li>
 				<li><span class="pgsa-info-label-archive pgsa-info-label-gender">Weight:</span> <?php echo esc_html( $weight ); ?> </li>
 			</ul>

 			<a href="<?php the_permalink() ?>">View Photos <i class="fa fa-angle-double-right"></i></a>
 			</section>
 			
		</div>
	</article>


	<?php endwhile; else : ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php endif; ?>

<?php }

// Add Custom Scripts & CSS
add_action( 'wp_enqueue_scripts', 'pgsa_script_css_single' );
function pgsa_script_css_single() {
	wp_enqueue_style( 'photo_gallery_single_styles', plugin_dir_url( __FILE__ ) . 'css/archive-styles.css' );
}

genesis();