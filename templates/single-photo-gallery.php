<?php

//Force the use of the Full Width Template in Genesis themes
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
add_action( 'genesis_after_entry_content', 'genesis_prev_next_post_nav', 5 );

// Load Next and Prev Patients after the header
function pgsa_prev_next_post_nav() {
	if ( 'photo-gallery' == get_post_type()) {
		echo '<div class="wrap"><div class="prev-next-navigation">';
			previous_post_link( '<div class="previous">%link</div>', '<i class="fa fa-chevron-circle-left"></i> %title', TRUE, ' ', 'procedures' );	next_post_link( '<div class="next">%link</div>', '%title <i class="fa fa-chevron-circle-right"></i>', TRUE, ' ', 'procedures' );
		echo '</div></div>';
	}
}
add_action( 'genesis_before_content', 'pgsa_prev_next_post_nav' );

//Replace the custom genesis loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_single_loop' );
function pgsa_custom_single_loop() { ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php

    $patient_photos = get_post_meta( get_the_ID(), '_pgsa_photos_patient-photos', true );
		    $age 	= get_post_meta( get_the_ID(), '_pgsa_info_age', true );
		    $view 	= get_post_meta( get_the_ID(), '_pgsa_photos_view_select', true );
		    $gender = get_post_meta( get_the_ID(), '_pgsa_info_gender_select', true );
		    $ethnic = get_post_meta( get_the_ID(), '_pgsa_info_ethnicity_select', true );
		    $height = get_post_meta( get_the_ID(), '_pgsa_info_height', true );
		    $weight = get_post_meta( get_the_ID(), '_pgsa_info_weight', true );
	    	$detail = get_post_meta( get_the_ID(), '_pgsa_info_procedure_detail', true );
	    	$doctor = get_post_meta( get_the_ID(), '_pgsa_info_doctor', true );
	      $location = get_post_meta( get_the_ID(), '_pgsa_info_location', true );
	    $procedures = get_the_term_list( $post->ID, 'procedures', '', ', ', '' );
	// Echo the metadata
	?>

	<article itemscope itemtype="http://schema.org/MedicalProcedure" <?php post_class() ?> >
		<h1><span itemprop="name"><?php echo $procedures ?></span>	<?php the_title(); ?></h1>
		<section class="pgsa-surgeon-info">
			<h3>Surgeon: <span> M.D., F.A.C.S.</span></h3>
			<h3>Location: <span> </span></h3>
		</section>
		<section itemscope itemtype="http://schema.org/Person" class="pgsa-patient-info">
 			<div class="one-third first">
	 			<ul class="pgsa-info-label-list">
	 				<li><span class="pgsa-info-label-single pgsa-info-label-doctor">Doctor:</span> <?php echo esc_html( $doctor ); ?></li>
	 				<li><span class="pgsa-info-label-single pgsa-info-label-location">Location:</span> <?php echo esc_html( $location ); ?></li>
	 				<?php if (!empty($age)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-age">Age:</span> <?php echo esc_html( $age ); ?></li><?php } ?>
	 				<?php if (!empty($gender)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-gender">Gender:</span><span itemprop="gender"> <?php echo esc_html( $gender ); ?></span></li><?php } ?>
	 			</ul>
 			</div>
 			<div class="one-third">
	 			<ul class="pgsa-info-label-list">
	 				<?php if (!empty($height)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-height">Height:</span> <span itemprop="height"><?php echo esc_html( $height ); ?></span></li><?php } ?>	
	 				<?php if (!empty($weight)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-weight">Weight:</span> <span itemprop="weight"><?php echo esc_html( $weight ); ?></span></li><?php } ?>
	 			</ul>
 			</div>
 			<div class="one-third">
	 			<ul class="pgsa-info-label-list">
	 			 	<?php if (!empty($ethnic)) { ?><li><span class="pgsa-info-label-single pgsa-info-label-ethnicity">Ethnicity:</span> <?php echo esc_html( $ethnic ); ?></li><?php } ?>
	 			</ul>
 			</div>
 			<div class="clearfix"></div>
 			<?php if (!empty($detail)) { ?><span class="pgsa-info-label-single pgsa-info-label-detail">Description:</span>
			<p itemprop="description"><?php echo esc_html( $detail ); ?></p><?php } ?>
		</section>
		<section>
		<?php foreach ( $patient_photos as $value ) { ?>
			<div itemscope itemtype="https://schema.org/ImageGallery">
				<h3 itemprop="name"><?php echo $value['_pgsa_photos_view_select']; ?></h3>
				<div class="one-half first">							
					<a href="<?php echo $value['_pgsa_photos_before_photo']; ?>" data-lity><img itemprop="image" class="aligncenter imageresource" src="<?php echo $value['_pgsa_photos_before_photo']; ?>" /></a>
					<p class="pgsa-photo-caption"><span class="pgsa-photo-caption-before">Before</span><?php if (!empty($value['_pgsa_photos_before_caption'])) { ?>: <?php echo $value['_pgsa_photos_before_caption']; ?><?php } ?></p>
				</div>
				<div class="one-half">
					<a href="<?php echo $value['_pgsa_photos_after_photo']; ?>" data-lity><img itemprop="image"  class="aligncenter imageresource" src="<?php echo $value['_pgsa_photos_after_photo']; ?>" /></a>
					<p class="pgsa-photo-caption"><span class="pgsa-photo-caption pgsa-photo-caption-after">After</span><?php if (!empty($value['_pgsa_photos_after_photo'])) { ?> <?php echo $value['_pgsa_photos_after_caption']; ?><?php } ?></p>
				</div>
			</div>
		<?php } ?>
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
	wp_enqueue_style( 'photo_gallery_lity_lightbox_js', plugin_dir_url( __FILE__ ) . 'css/lity.min.css');
	wp_enqueue_script( 'photo_gallery_lity_lightbox_css', plugin_dir_url( __FILE__ ) . 'js/lity.min.js',	array( 'jquery' ) );
}

 
/** Replace the standard loop with our custom loop */
genesis();