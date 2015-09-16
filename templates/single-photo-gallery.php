<?php

//Force the use of the Full Width Template in Genesis themes
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
add_action( 'genesis_after_entry_content', 'genesis_prev_next_post_nav', 5 );


//Replace the custom genesis loop
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
	    $procedures = get_the_term_list( $post->ID, 'procedures', '', ', ', '' );
	// Echo the metadata
	?>

	<?php previous_post('&laquo;&laquo; %', 'Previous Patient ', 'no'); ?> | <?php next_post('% &raquo;&raquo; ', 'Next Patient ', 'no'); ?>
	<article <?php post_class() ?> >
		<h1><?php the_title(); ?></h1>
		<?php foreach ( $patient_photos as $value ) { ?>

			<div class="one-half first">							
				<a href="<?php echo $value['_pgsa_photos_before_photo']; ?>"  data-lity><img src="<?php echo $value['_pgsa_photos_before_photo']; ?>" /></a>
				<p><span class="pgsa-photo-caption pgsa-photo-caption-before">Before</span><?php if (!empty($value['_pgsa_photos_before_caption'])) { ?>: <?php echo $value['_pgsa_photos_before_caption']; ?><?php } ?></p>
			</div>
			<div class="one-half">								
				<a href="<?php echo $value['_pgsa_photos_after_photo']; ?>"   data-lity><img src="<?php echo $value['_pgsa_photos_after_photo']; ?>" /></a>
				<p><span class="pgsa-photo-caption pgsa-photo-caption-after">After</span><?php if (!empty($value['_pgsa_photos_after_photo'])) { ?>: <?php echo $value['_pgsa_photos_after_caption']; ?><?php } ?></p>
			</div>
		<?php } ?>


		<section class="pgsa-patient-info">
 			<div class="one-third first">
	 			<ul class="pgsa-info-label-list">
	 				<li><span class="pgsa-info-label-single pgsa-info-label-procedures">Procedures:</span> <?php echo $procedures ?></li>
	 				<?php if (!empty($age)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-age">Age:</span> <?php echo esc_html( $age ); ?></li><?php } ?>
	 			</ul>
 			</div>
 			<div class="one-third">
	 			<ul class="pgsa-info-label-list">
	 				<?php if (!empty($ethnic)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-ethnicity">Ethnicity:</span> <?php echo esc_html( $ethnic ); ?></li><?php } ?>
	 				<?php if (!empty($height)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-height">Height:</span> <?php echo esc_html( $height ); ?></li><?php } ?>
	 			</ul>
 			</div>
 			<div class="one-third">
	 			<ul class="pgsa-info-label-list">
	 			 	<?php if (!empty($gender)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-gender">Gender:</span> <?php echo esc_html( $gender ); ?></li><?php } ?>
	 				<?php if (!empty($weight)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-weight">Weight:</span> <?php echo esc_html( $weight ); ?> </li><?php } ?>
	 			</ul>
 			</div>
 			<div class="clearfix"></div>
 			<?php if (!empty($detail)) { ?><span class="pgsa-info-label-single pgsa-info-label-detail">Description:</span>
			<p><?php echo esc_html( $detail ); ?></p><?php } ?>
		</section>


	</article>

	<?php endwhile; else : ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php endif; ?>

<?php } //End Custom Loop


// Add Custom Scripts & CSS
add_action( 'wp_enqueue_scripts', 'pgsa_script_css_single' );
function pgsa_script_css_single() {
	wp_enqueue_style( 'photo_gallery_single_styles', plugin_dir_url( __FILE__ ) . 'css/single-styles.css' );
	wp_enqueue_style( 'photo_gallery_lity_lightbox_css', plugin_dir_url( __FILE__ ) . 'css/lity.min.css' );
	wp_enqueue_script( 'photo_gallery_lity_lightbox_js', plugin_dir_url( __FILE__ ) . 'js/lity.min.js',	array( 'jquery' ));
}

 
/** Replace the standard loop with our custom loop */
genesis();