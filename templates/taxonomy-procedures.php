<?php

//Replace the custom genesis loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_archive_loop' );

function pgsa_custom_archive_loop() { ?>
<?php 
$qobj = get_queried_object();
$args = array(
	'post_type'  => 'photo-gallery',
	'orderby' => 'title',
	'order'   => 'DESC',
	'tax_query' => array(
        array(
          'taxonomy' => $qobj->taxonomy,
          'field' => 'id',
          'terms' => $qobj->term_id,
        )
      )
);
$pgsa_archive_query = new WP_Query( $args ); ?>
    <?php 
$value    = get_query_var($wp_query->query_vars['procedures']);
echo get_term_by('slug',$value,$wp_query->query_vars['procedures']);
    if ( $pgsa_archive_query->have_posts() ) : while ( $pgsa_archive_query->have_posts() ) : $pgsa_archive_query->the_post(); ?>
    <?php
    $patient_photos = get_post_meta( get_the_ID(), '_pgsa_photos_patient-photos', true );
    		$view 	= get_post_meta( get_the_ID(), '_pgsa_photos_view_select', true );
		    $age 	= get_post_meta( get_the_ID(), '_pgsa_info_age', true );
		    $gender = get_post_meta( get_the_ID(), '_pgsa_info_gender_select', true );
		    $ethnic = get_post_meta( get_the_ID(), '_pgsa_info_ethnicity_select', true );
		    $height = get_post_meta( get_the_ID(), '_pgsa_info_height', true );
		    $weight = get_post_meta( get_the_ID(), '_pgsa_info_weight', true );
	    	$detail = get_post_meta( get_the_ID(), '_pgsa_info_procedure_detail', true );
	    	$procedures = get_the_term_list( $post->ID, 'procedures', '', ', ', '' );
	// Echo the metadata
	?>

	<article <?php post_class(); ?> >
		<h1><span itemprop="name"><?php echo $procedures ?></span></h1>
		

		<?php foreach ( $patient_photos as $index => $value ) { ?>

			<?php if ($index == 0) { ?> <!-- Show first set of photos -->
			<section>
			<h2><? the_title(); ?></h2>
			<div class="one-fourth first">
				<div style="text-align:center;">Before</div>
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_before_photo']; ?>" alt="<?php echo the_title()." - Before"; ?>" title="<?php echo the_title()." - Before"; ?>"/></a>
			</div>
			<div class="one-fourth">
				<div style="text-align:center;">After</div>							
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_after_photo']; ?>" alt="<?php echo the_title()." - After"; ?>" title="<?php echo the_title()." - After"; ?>"/></a>
			</div>
			<?php }else if($index == 1){ ?> <!--Show second set of photos -->
			<div class="one-fourth">
				<div style="text-align:center;">Before</div>
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_before_photo']; ?>" alt="<?php echo the_title()." - Before"; ?>" title="<?php echo the_title()." - Before"; ?>"/></a>
			</div>
			<div class="one-fourth">
				<div style="text-align:center;">After</div>
				<a href="<?php the_permalink() ?>"><img src="<?php echo $value['_pgsa_photos_after_photo']; ?>" alt="<?php echo the_title()." - After"; ?>" title="<?php echo the_title()." - After"; ?>"/></a>
			</div>
			</section>
			<?php	} ?>
		<?php } ?>
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