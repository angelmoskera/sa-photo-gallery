<?php


remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_single_loop' );
function pgsa_custom_single_loop() { ?>
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
		<h1><?php the_title(); ?></h1>
		<?php foreach ( $patient_photos as $value ) { ?>

			<div class="one-half first">							
				<a href="<?php echo $value['_pgsa_photos_before_photo']; ?>"  data-lity><img src="<?php echo $value['_pgsa_photos_before_photo']; ?>" /></a>
				<p>Before <?php echo $value['_pgsa_photos_before_caption']; ?></p>
			</div>
			<div class="one-half">								
				<a href="<?php echo $value['_pgsa_photos_after_photo']; ?>"   data-lity><img src="<?php echo $value['_pgsa_photos_after_photo']; ?>" /></a>
				<p>After <?php echo $value['_pgsa_photos_after_caption']; ?></p>
			</div>
		<?php } ?>


		<section class="pgsa-patient-info">
 			<div class="one-half first">
	 			<ul>
	 				<li>Age: <?php echo esc_html( $age ); ?></li>
	 				<li>Gender: <?php echo esc_html( $gender ); ?></li>
	 				<li>Ethnicity: <?php echo esc_html( $ethnic ); ?></li>
	 			</ul>
 			</div>
 			<div class="one-half">
	 			<ul>
	 				<li>Height: <?php echo esc_html( $height ); ?></li>
	 				<li>Weight:<?php echo esc_html( $weight ); ?> </li>
	 			</ul>
 			</div>
 			<div class="clearfix"></div>
			<p><?php echo esc_html( $detail ); ?></p>
		</section>
	</article>

	<?php endwhile; else : ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php endif; ?>

<?php } //End Custom Loop


// Add Modal Scripts
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
function my_scripts_method() {
	wp_enqueue_style( 'photo_gallery_single_styles', plugin_dir_url( __FILE__ ) . 'css/single-styles.css' );
	wp_enqueue_style( 'photo_gallery_lity_lightbox_css', plugin_dir_url( __FILE__ ) . 'css/lity.min.css' );
	wp_enqueue_script( 'photo_gallery_lity_lightbox_js', plugin_dir_url( __FILE__ ) . 'js/lity.min.js',	array( 'jquery' ));
}

// Add Modal Custom HTML

 
/** Replace the standard loop with our custom loop */
genesis();