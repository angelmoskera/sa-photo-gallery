<?php
/*
 Plugin Name: Photo Gallery SA
 Plugin URI: http://www.surgeonsadvisor.com/
 Description: Medical Photo Gallery that groups patients with their related procedures.
 Author: Angel Yarmas
 Version: 1.0
 */

 // Load the auto-update class
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'http://ugel02.gob.pe/update.json', 
    __FILE__
);

// Create Photo gallery
function pgsa_create_posttype() {


	// Register custom post type
	register_post_type( 'photo-gallery',
		array(
			'labels' => array(
				'name' 			=> __( 'Photo Gallery' ),
				'singular_name' => __( 'Photo Gallery' ),
				'all_items'     => __( 'All Patients' ),
				'name_admin_bar'=> __( 'Patient' ),
				'edit_item'     => __( 'Edit Patient' ),
				'view_item'     => __( 'View Patient' ),
			),
			'public' 			=> true, 
			'has_archive' 		=> true,
			'menu_icon' 		=> 'dashicons-format-gallery',
			'supports'   		=> array('title'),
			'hierarchical'      => false,

		)
	);


	// Register taxonomy
	register_taxonomy( 'procedures', 'photo-gallery', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => __( 'Procedures' ),
			'add_new_item' => 'Add a New Procedure',
			),
		'query_var' => true,
		'show_admin_column' => true,
        'public'=>true,
        'has_archive' => true,
		'show_ui' => true,

		)

	);
}

add_action( 'init', 'pgsa_create_posttype' );

//Remove post slug box
function pgsa_remove_slug_field() {
	remove_meta_box( 'slugdiv' , 'photo-gallery' , 'normal' ); 
}
add_action( 'admin_menu' , 'pgsa_remove_slug_field' );

// Add Custom css to Admin Área
function pgsa_custom_admin_css($hook) { 
	if( get_post_type() == 'photo-gallery' ) {
	    wp_enqueue_style( 'photo_gallery_styles', plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'pgsa_custom_admin_css' );


// Force use of template
add_filter( 'template_include', 'include_pgsa_template', 1 );
function include_pgsa_template( $template_path ){
    if ( get_post_type() == 'photo-gallery' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array( 'single-photo-gallery.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path( __FILE__ ) . '/templates/single-photo-gallery.php';
            }
        } elseif (is_archive()) {
            if ( $theme_file = locate_template( array( 'archive-photo-gallery.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path( __FILE__ ) . '/templates/archive-photo-gallery.php';
            }
        }
    }
    return $template_path;
}


//PATIEN INFO METABOXES
add_action( 'cmb2_init', 'cmb2_sample_metaboxes' );
/* Define the metabox and field configurations. */
function cmb2_sample_metaboxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefixinfo = '_pgsa_info_';

    /* Initiate the metabox */
    $cmb = new_cmb2_box( array(
        'id'            => $prefixinfo . 'patient-info',
        'title'         => __( 'Patient info', 'cmb2' ),
        'object_types'  => array( 'photo-gallery', ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
        'row_classes' => 'row',
    ) );

    // Age Metabox
    $cmb->add_field( array(
        'name'       => __( 'Age', 'cmb2' ),
        'desc'       => __( 'Patient age. Ex. Over 56, Undisclosed, 40 - 49yo, 56', 'cmb2' ),
        'id'         => $prefixinfo . 'age',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
        'row_classes' => 'col-md-4',
    ) );

    // Gender Metabox
    $cmb->add_field( array(
    'name'             => 'Gender',
    'desc'             => 'Select the patient gender',
    'id'               => $prefixinfo . 'gender_select',
    'type'             => 'select',
    'show_option_none' => true,
    'options'          => array(
        'female' => __( 'Female', 'cmb' ),
        'male'   => __( 'Male', 'cmb' ),
	    ),
    'row_classes' => 'col-md-4',
	) );

    // Ethnicity Metabox
	$cmb->add_field( array(
    'name'             => 'Ethnicity',
    'desc'             => 'Select the patient ethnicity',
    'id'               => $prefixinfo . 'ethnicity_select',
    'type'             => 'select',
    'show_option_none' => true,
    'options'          => array(
        'caucasian' => __( 'Caucasian', 'cmb' ),
        'african'   => __( 'African american', 'cmb' ),    
        'asian'   	=> __( 'Asian', 'cmb' ),
	    ),
    'row_classes' => 'col-md-4',
	) );

    // Height Metabox
    $cmb->add_field( array(
        'name'       => __( 'Height', 'cmb2' ),
        'desc'       => __( 'Patient height. Ex. Over 6’0", Undisclosed, 5’ 6” - 6’ 0”', 'cmb2' ),
        'id'         => $prefixinfo . 'height',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
        'row_classes'=> 'col-md-4',
    ) );

    // Weight Metabox
    $cmb->add_field( array(
        'name'       => __( 'Weight', 'cmb2' ),
        'desc'       => __( 'Patient width. Ex. Over 150lbs, Undisclosed, 150 - 199lbs', 'cmb2' ),
        'id'         => $prefixinfo . 'weight',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
        'row_classes'=> 'col-md-4',
    ) );

    // Procedures Detail Metabox
	$cmb->add_field( array(
    'name'    => 'Procedure detail',
    'desc'    => 'Procedure detail for the patient if exists',
    'id'      => $prefixinfo . 'procedure_detail',
    'type' 	  => 'textarea_small',
    'row_classes' => 'col-md-12',
	) );
}


//PATIENT PHOTOS METABOXES
add_action( 'cmb2_init', 'pgsa_patient_photos' );
function pgsa_patient_photos() {
	// Start with an underscore to hide fields from custom fields list
	$prefixphotos = '_pgsa_photos_';
	/**
	 * Repeatable Field Groups
	 */
	$cmb_group = new_cmb2_box( array(
		'id'           => $prefixphotos . 'metabox',
		'title'        => __( 'Patient photos', 'cmb2' ),
		'object_types' => array( 'photo-gallery', ),
	) );
	// $group_field_id is the field id string, so in this case: $prefixphotos . 'demo'
	$group_field_id = $cmb_group->add_field( array(
		'id'          => $prefixphotos . 'patient-photos',
		'type'        => 'group',
		'options'     => array(
			'group_title'   => __( 'Group {#}', 'cmb2' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add another group', 'cmb2' ),
			'remove_button' => __( 'Remove group', 'cmb2' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
		'row_classes' => 'row',
	) );
	/**
	 * Group fields works the same, except ids only need
	 * to be unique to the group. Prefixphotos is not needed.
	 *
	 * The parent field's id needs to be passed as the first argument.
	 */
	$cmb_group->add_group_field( $group_field_id, array(
	    'name'             => 'Select View',
	    'id'               => $prefixphotos . 'view_select',
	    'type'             => 'select',
	    'show_option_none' => true,
	    'options'          => array(
	        'view1' => __( 'Frontal', 'cmb' ),
	        'view2' => __( 'Lateral Right', 'cmb' ),
	        'view3' => __( 'Lateral Left', 'cmb' ),
	        'view4' => __( 'Oblique Right', 'cmb' ),
	        'view5' => __( 'Oblique Left', 'cmb' ),
	        'view6' => __( 'Upper Back', 'cmb' ),
	        'view7' => __( 'Lower Back', 'cmb' ),
	    ),
	    'attributes'  => array(
        'required'    => 'required',
    	),
	    
	    'row_classes' => 'col-md-6 pgsa-select-view',
	) );

	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'Before photo', 'cmb2' ),
		'id'   => $prefixphotos . 'before_photo',
		'type' => 'file',
		'row_classes' => 'col-md-6',
	    'attributes'  => array(
        'required'    => 'required',
    	),
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'After photo', 'cmb2' ),
		'id'   => $prefixphotos . 'after_photo',
		'type' => 'file',
		'row_classes' => 'col-md-6',
	    'attributes'  => array(
        'required'    => 'required',
    	),
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'Before Caption', 'cmb2' ),
		'id'   => $prefixphotos . 'before_caption',
		'desc' => 'This text will go <b>AFTER</b> the text "Before"',
		'type' => 'text',
		'row_classes' => 'col-md-6',
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'After Caption', 'cmb2' ),
		'id'   => $prefixphotos . 'after_caption',
		'desc' => 'This text will go <b>AFTER</b> the text "After"',
		'type' => 'text',
		'row_classes' => 'col-md-6',
	) );
}


add_action( 'init', 'pgsa_initialize_cmb_meta_boxes', 9999 );
function pgsa_initialize_cmb_meta_boxes() {
	if ( file_exists(  __DIR__ . '/custom_metaboxes/init.php' ) ) {
	  require_once  __DIR__ . '/custom_metaboxes/init.php';
	} elseif ( file_exists(  __DIR__ . '/custom_metaboxes/init.php' ) ) {
	  require_once  __DIR__ . '/custom_metaboxes/init.php';
	}
}



// Interaction Messsages
function my_bulk_post_updated_messages_filter( $bulk_messages, $bulk_counts ) {

    $bulk_messages['photo-gallery'] = array(
        'updated'   => _n( '%s Patient updated.', '%s Patients updated.', $bulk_counts['updated'] ),
        'locked'    => _n( '%s Patient not updated, somebody is editing it.', '%s Patients not updated, somebody is editing them.', $bulk_counts['locked'] ),
        'deleted'   => _n( '%s Patient permanently deleted.', '%s Patients permanently deleted.', $bulk_counts['deleted'] ),
        'trashed'   => _n( '%s Patient moved to the Trashgg', '%s Patients moved to the Trash.', $bulk_counts['trashed'] ),
        'untrashed' => _n( '%s Patient restored from the Trash.', '%s Patients restored from the Trash.', $bulk_counts['untrashed'] ),
    );

    return $bulk_messages;

}

add_filter( 'bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter', 10, 2 );

//Contextual Help
function my_contextual_help( $contextual_help, $screen_id, $screen ) { 
  if ( 'photo-gallery' == $screen->id ) {

    $contextual_help = '<h2>Products</h2>
    <p>Products show the details of the items that we sell on the website. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
    <p>You can view/edit the details of each product by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

  } elseif ( 'edit-product' == $screen->id ) {

    $contextual_help = '<h2>Editing products</h2>
    <p>This page allows you to view/modify product details. Please make sure to fill out the available boxes with the appropriate details (product image, price, brand) and <strong>not</strong> add these details to the product description.</p>';

  }
  return $contextual_help;
}
add_action( 'contextual_help', 'my_contextual_help', 10, 3 );




?>