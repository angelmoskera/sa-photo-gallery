<?php
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pgsa_custom_single_loop' );

function pgsa_custom_single_loop() { ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php
    $age = get_post_meta( get_the_ID(), 'age', true );
    $gender = get_post_meta( get_the_ID(), 'gender_select', true );
    $ethnic = get_post_meta( get_the_ID(), 'ethnicity_select', true );
    $height = get_post_meta( get_the_ID(), '_pgsa_info_height', true );
    $weight = get_post_meta( get_the_ID(), '_pgsa_info_weight', true );
    $patient_photos = get_post_meta( get_the_ID(), '_pgsa_photos_demo', true );

	// Echo the metadata
	
	?>

	<article <?php post_class() ?> >
		<h1><?php the_title(); ?></h1>
		<?php foreach ( $patient_photos as $value ) { ?>

			<div class="one-half first">							
				<img src="<?php echo $value['before_photo']; ?>" />
				<p>Before <?php echo $value['before_caption']; ?></p>
			</div>
			<div class="one-half">								
				<img src="<?php echo $value['after_photo']; ?>" />
				<p>After <?php echo $value['after_caption']; ?></p>
			</div>
		<?php } ?>


		<div>
			
 			
 			<section class="one-half first">
 			<ul>
 				<li>Age: <?php echo esc_html( $age ); ?></li>
 				<li>Gender: <?php echo esc_html( $gender ); ?></li>
 				<li>Ethnicity: <?php echo esc_html( $ethnic ); ?></li>
 			</ul>
 			</section>
 			<section class="one-half">
 			<ul>
 				<li>Height: <?php echo esc_html( $height ); ?></li>
 				<li>Weight:<?php echo esc_html( $weight ); ?> </li>
 			</ul>

 			<a href="<?php the_permalink() ?>">View Photos »</a>
 			</section>
 			
		</div>
		

	</article>




	<?php endwhile; else : ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php endif; ?>

<?php }
 
/** Replace the standard loop with our custom loop */


genesis();