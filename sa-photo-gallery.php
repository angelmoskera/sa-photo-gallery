<?php
/*
 Plugin Name: Photo Gallery SA
 Plugin URI: http://ayarmas:164f11@ayarmas.surgeonsadvisor.com
 Description: Medical Photo Gallery that groups patients with their related procedures.
 Author: Angel Yarmas
 Version: 1.0
 */

 // Load the auto-update class
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'http://ayarmas:164f11@ayarmas.surgeonsadvisor.com/update.json', 
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
            'hierarchical' => true,
            'cptp_permalink_structure' => "/%procedures%/case-%post_id%",
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
        'rewrite' => array('slug' => 'photo-gallery')
		)

	);
}
add_action( 'init', 'pgsa_create_posttype' );



// Remove custom taxonomy base slug in Permalinks
function taxonomy_slug_rewrite($wp_rewrite) {
    $rules = array();
    // get all custom taxonomies
    $taxonomies = get_taxonomies(array('_builtin' => false), 'objects');
    // get all custom post types
    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
     
    foreach ($post_types as $post_type) {
        foreach ($taxonomies as $taxonomy) {
         
            // go through all post types which this taxonomy is assigned to
            foreach ($taxonomy->object_type as $object_type) {
                 
                // check if taxonomy is registered for this custom type
                if ($object_type == $post_type->rewrite['slug']) {
             
                    // get category objects
                    $terms = get_categories(array('type' => $object_type, 'taxonomy' => $taxonomy->name, 'hide_empty' => 0));
             
                    // make rules
                    foreach ($terms as $term) {
                        $rules[$object_type . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
                    }
                }
            }
        }
    }
    // merge with global rules
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules', 'taxonomy_slug_rewrite');


//Remove post slug box
function pgsa_remove_slug_field() {
	remove_meta_box( 'slugdiv' , 'photo-gallery' , 'normal' ); 
}
add_action( 'admin_menu' , 'pgsa_remove_slug_field' );

// Add Custom css to Admin Área
function pgsa_custom_admin_resources($hook) { 
	if( get_post_type() == 'photo-gallery' ) {
	    wp_enqueue_style( 'photo_gallery_styles', plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' );
       wp_enqueue_script( 'photo_gallery_js', plugin_dir_url( __FILE__ ) . 'js/scripts.js' );
	}
}
add_action( 'admin_enqueue_scripts', 'pgsa_custom_admin_resources' );


// Force use of template
add_filter( 'template_include', 'include_pgsa_template', 1 );
function include_pgsa_template( $template_path ){
    if ( get_post_type() == 'photo-gallery' && !is_tax( 'procedures' )) {
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

    if (is_tax( 'procedures' )) {
    	if ( $theme_file = locate_template( array( 'taxonomy-procedures.php' ) ) ) {
            $template_path = $theme_file;
        } else {
            $template_path = plugin_dir_path( __FILE__ ) . '/templates/taxonomy-procedures.php';
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

    // Doctor Metabox
    $cmb->add_field( array(
        'name'       => __( 'Doctor', 'cmb2' ),
        'desc'       => __( 'Doctor who performed the procedure', 'cmb2' ),
        'id'         => $prefixinfo . 'doctor',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
       'row_classes' => 'col-md-4',
    ) );

    // Location Metabox
    $cmb->add_field( array(
        'name'       => __( 'Location', 'cmb2' ),
        'desc'       => __( 'Location where the procedure was performed', 'cmb2' ),
        'id'         => $prefixinfo . 'location',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', 
        'row_classes' => 'col-md-4',
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
        'Female' => __( 'Female', 'cmb' ),
        'Male'   => __( 'Male', 'cmb' ),
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
        'Caucasian' => __( 'Caucasian', 'cmb' ),
        'African American'   => __( 'African american', 'cmb' ),    
        'Asian'   	=> __( 'Asian', 'cmb' ),
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
	        'Frontal' => __( 'Frontal', 'cmb' ),
	  'Lateral Right' => __( 'Lateral Right', 'cmb' ),
	   'Lateral Left' => __( 'Lateral Left', 'cmb' ),
	  'Oblique Right' => __( 'Oblique Right', 'cmb' ),
	   'Oblique Left' => __( 'Oblique Left', 'cmb' ),
	     'Upper Back' => __( 'Upper Back', 'cmb' ),
	     'Lower Back' => __( 'Lower Back', 'cmb' ),
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
function pgsa_post_update_messages( $bulk_messages, $bulk_counts ) {
	global $post, $post_ID;
	$redirectpg_perma = esc_url( add_query_arg( '', '', get_permalink($post_ID) ) ) ;
	$redirectpg_from = str_replace(site_url(), '', $redirectpg_perma);
	$redirectpg_to = str_replace('/'. basename(get_permalink()), '', $redirectpg_from);

    $bulk_messages['photo-gallery'] = array(
        'updated'   => _n( '%s Patient updated.', '%s Patients updated.', $bulk_counts['updated'] ),
        'locked'    => _n( '%s Patient not updated, somebody is editing it.', '%s Patients not updated, somebody is editing them.', $bulk_counts['locked'] ),
        'deleted'   => _n( '%s Patient permanently deleted.', '%s Patients permanently deleted.', $bulk_counts['deleted'] ),
        'trashed'   => _n( '%s Patient moved to the Trash, <br />Please add this redirect to .htaccess if necessary: <br/> <span class="pgsa-apply-redirect">Redirect 301 '.$redirectpg_from.' '.$redirectpg_to.'</span><br />', 
        				   '%s Patient moved to the Trash, <br />Please add this redirect to .htaccess if necessary: <br/> <span class="pgsa-apply-redirect">Redirect 301 '.$redirectpg_from.' '.$redirectpg_to.'</span><br />', $bulk_counts['trashed'] ),
        'untrashed' => _n( '%s Patient restored from the Trash.', '%s Patients restored from the Trash.', $bulk_counts['untrashed'] ),
    );
    return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', 'pgsa_post_update_messages', 10, 2 );

//Contextual Help
add_action('load-post-new.php', 'pgsa_plugin_help');
add_action('load-post.php', 'pgsa_plugin_help');
 
function pgsa_plugin_help() {
   add_filter('contextual_help','pgsa_plugin_help_tabs');
}
 
function pgsa_plugin_help_tabs($help) {
    get_current_screen()->add_help_tab( array(
        'id'        => 'pgsa_help_main',
        'title'     => __('Overview'),
        'content'   => "<p>Overview info here</p>"
    ) );

    get_current_screen()->add_help_tab( array(
        'id'        => 'pgsa_help_wiki',
        'title'     => __('Resources'),
        'content'   => "<p>Resources info here</p>"
    ) );
}

//Register Sidebar
$templates = wp_get_theme()->get_stylesheet() ; //Get theme slug
add_action( 'widgets_init', 'pgsa_widget_sidebar_taxonomy' );
function pgsa_widget_sidebar_taxonomy() {
    register_sidebar( array(
        'name' => __( 'Main Sidebar', print_r($templates) ),
        'id' => 'sidebar-1',
        'description' => __( 'Photo Gallery Specific Sidebar', print_r($templates) ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget'  => '</li>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>',
    ) );
}

?>