<?php
/**
 * Plugin Name: Tuts+ CRM
 * Plugin URI: #
 * Version: 1.0
 * Author: Tuts+
 * Author URI: https://code.tutsplus.com
 * Description: A simple CRM system for WordPress
 * License: GPL2
 */

include_once( 'advanced-custom-fields/acf.php' );
define( 'ACF_LITE', true );

class WPTutsCRM {
	
    /**
* Constructor. Called when plugin is initialised
*/
function __construct() {

	add_action( 'init', array( $this, 'register_custom_post_type' ) );
	add_action( 'plugins_loaded', array( $this, 'acf_fields' ) );
	add_filter( 'manage_edit-contact_columns', array( $this, 'add_table_columns' ) );
	add_action( 'manage_contact_posts_custom_column', array( $this, 'output_table_columns_data'), 10, 2 );
	add_filter( 'manage_edit-contact_sortable_columns', array( $this, 'define_sortable_table_columns') );
    
	if ( is_admin() ) {
		add_filter( 'request', array( $this, 'orderby_sortable_table_columns' ) );
		add_filter( 'posts_join', array ( &$this, 'search_meta_data_join' ) );
		add_filter( 'posts_where', array( &$this, 'search_meta_data_where' ) );
	}
    
}
    
	/**
	 * Registers a Custom Post Type called contact
	 */
	function register_custom_post_type() {
		register_post_type( 'contact', array(
			'labels' => array(
				'name'               => _x( 'Contacts', 'post type general name', 'tuts-crm' ),
				'singular_name'      => _x( 'Contact', 'post type singular name', 'tuts-crm' ),
				'menu_name'          => _x( 'Contacts', 'admin menu', 'tuts-crm' ),
				'name_admin_bar'     => _x( 'Contact', 'add new on admin bar', 'tuts-crm' ),
				'add_new'            => _x( 'Add New', 'contact', 'tuts-crm' ),
				'add_new_item'       => __( 'Add New Contact', 'tuts-crm' ),
				'new_item'           => __( 'New Contact', 'tuts-crm' ),
				'edit_item'          => __( 'Edit Contact', 'tuts-crm' ),
				'view_item'          => __( 'View Contact', 'tuts-crm' ),
				'all_items'          => __( 'All Contacts', 'tuts-crm' ),
				'search_items'       => __( 'Search Contacts', 'tuts-crm' ),
				'parent_item_colon'  => __( 'Parent Contacts:', 'tuts-crm' ),
				'not_found'          => __( 'No contacts found.', 'tuts-crm' ),
				'not_found_in_trash' => __( 'No contacts found in Trash.', 'tuts-crm' ),
			),
			
			// Frontend
			'has_archive'        => false,
			'public'             => false,
			'publicly_queryable' => false,
			
			// Admin
			'capability_type' => 'post',
			'menu_icon'     => 'dashicons-businessman',
			'menu_position' => 10,
			'query_var'     => true,
			'show_in_menu'  => true,
			'show_ui'       => true,
			'supports'      => array(
				'title',
				'author',
				'comments',	
			),
		) );	
	}
	/**
	* Registers a Meta Box on our Contact Custom Post Type, called 'Contact Details'
	*/
	function register_meta_boxes() {
		add_meta_box( 'contact-details', 'Contact Details', array( $this, 'output_meta_box' ), 'contact', 'normal', 'high' );	
	}
	/**
	* Output a Contact Details meta box
	*
	* @param WP_Post $post WordPress Post object
	*/
	function output_meta_box( $post ) {
		// Output label and field
		echo ( '<label for="contact_email">' . __( 'Email Address', 'tuts-crm' ) . '</label>'  );
		echo ( '<input type="text" name="contact_email" id="contact_email" value="' . esc_attr( $email ) . '" />'  );
		
	}
	/** 
	* Saves the meta box field data
	*
	* @param int $post_id Post ID
	*/
	function save_meta_boxes( $post_id ) {

		// Check this is the Contact Custom Post Type
		if ( 'contact' != $_POST['post_type'] ) {
			return $post_id;
		}
	
		// Check the logged in user has permission to edit this post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	
		// OK to save meta data
		$email = sanitize_text_field( $_POST['contact_email'] );
		update_post_meta( $post_id, '_contact_email', $email );
		
	}
/**
* Register ACF Field Groups and Fields
*/
function acf_fields() {
	if( function_exists('acf_add_local_field_group') ):

		acf_add_local_field_group(array(
			'key' => 'group_61f259872a532',
			'title' => 'Grupo de campos',
			'fields' => array(
				array(
					'key' => 'field_61f25fc824995',
					'label' => 'Email Address',
					'name' => 'email_address',
					'type' => 'email',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_61f2615c3a80d',
					'label' => 'Phone Number',
					'name' => 'phone_number',
					'type' => 'number',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
				),
				array(
					'key' => 'field_61f2618eca41e',
					'label' => 'Photo',
					'name' => 'photo',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'preview_size' => 'medium',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				array(
					'key' => 'field_61f261ad6eaf7',
					'label' => 'Type',
					'name' => 'type',
					'type' => 'select',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'Prospect' => 'Prospect',
						'Customer' => 'Customer',
					),
					'default_value' => false,
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'contact',
					),
				),
			),
			'menu_order' => 1,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(
				0 => 'permalink',
				1 => 'excerpt',
				2 => 'discussion',
				3 => 'comments',
				4 => 'revisions',
				5 => 'slug',
				6 => 'author',
				7 => 'format',
				8 => 'page_attributes',
				9 => 'featured_image',
				10 => 'categories',
				11 => 'tags',
				12 => 'send-trackbacks',
			),
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		));
		
		endif;		}
/**
* Adds table columns to the Contacts WP_List_Table
*
* @param array $columns Existing Columns
* @return array New Columns
*/
function add_table_columns( $columns ) {

	$columns['email_address'] = __( 'Email Address', 'tuts-crm' );
	$columns['phone_number'] = __( 'Phone Number', 'tuts-crm' );
	$columns['photo'] = __( 'Photo', 'tuts-crm' );
    
	return $columns;
    
}

/**
* Outputs our Contact custom field data, based on the column requested
*
* @param string $columnName Column Key Name
* @param int $post_id Post ID
*/
function output_table_columns_data( $columnName, $post_id ) {

	// Field
	$field = get_field( $columnName, $post_id );
	
	if ( 'photo' == $columnName ) {
		echo '<img src="' . $field['sizes']['thumbnail'].'" width="'.$field['sizes']['thumbnail-width'] . '" height="' . $field['sizes']['thumbnail-height'] . '" />';
	} else {
		// Output field
		echo $field;
	}
    
}

/**
* Defines which Contact columsn are sortable
*
* @param array $columns Existing sortable columns
* @return array New sortable columns
*/
function define_sortable_table_columns( $columns ) {

	$columns['email_address'] = 'email_address';
	$columns['phone_number'] = 'phone_number';
    
	return $columns;
    
}
/**
* Inspect the request to see if we are on the Contacts WP_List_Table and attempting to
* sort by email address or phone number.  If so, amend the Posts query to sort by
* that custom meta key
*
* @param array $vars Request Variables
* @return array New Request Variables
*/
function orderby_sortable_table_columns( $vars ) {

	// Don't do anything if we are not on the Contact Custom Post Type
	if ( 'contact' != $vars['post_type'] ) return $vars;
	
	// Don't do anything if no orderby parameter is set
	if ( ! isset( $vars['orderby'] ) ) return $vars;
	
	// Check if the orderby parameter matches one of our sortable columns
	if ( $vars['orderby'] == 'email_address' OR
		$vars['orderby'] == 'phone_number' ) {
		// Add orderby meta_value and meta_key parameters to the query
		$vars = array_merge( $vars, array(
        	'meta_key' => $vars['orderby'],
			'orderby' => 'meta_value',
		));
	}
	
	return $vars;
    
}
/**
* Adds a join to the WordPress meta table for license key searches in the WordPress Administration
*
* @param string $join SQL JOIN statement
* @return string SQL JOIN statement
*/
function search_meta_data_join($join) {
	global $wpdb;
		
	// Only join the post meta table if we are performing a search
	if ( empty ( get_query_var( 's' ) ) ) {
		return $join;
	}
	    
	// Only join the post meta table if we are on the Contacts Custom Post Type
	if ( 'contact' != get_query_var( 'post_type' ) ) {
		return $join;
	}
		
	// Join the post meta table
	$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
		
	return $join;
}
/**
* Adds a where clause to the WordPress meta table for license key searches in the WordPress Administration
*
* @param string $where SQL WHERE clause(s)
* @return string SQL WHERE clauses
*/
function search_meta_data_where($where) {
	global $wpdb;

	// Only join the post meta table if we are performing a search
	if ( empty ( get_query_var( 's' ) ) ) {
    		return $where;
    	}
    
    	// Only join the post meta table if we are on the Contacts Custom Post Type
	if ( 'contact' != get_query_var( 'post_type' ) ) {
		return $where;
	}
	
	// Get the start of the query, which is ' AND ((', and the rest of the query
	$startOfQuery = substr( $where, 0, 7 );
	$restOfQuery = substr( $where ,7 );
	
	// Inject our WHERE clause in between the start of the query and the rest of the query
	$where = $startOfQuery . 
			"(" . $wpdb->postmeta . ".meta_value LIKE '%" . get_query_var( 's' ) . "%' OR " . $restOfQuery .
			"GROUP BY " . $wpdb->posts . ".id";
	
	// Return revised WHERE clause
	return $where;
}
}

$wpTutsCRM = new WPTutsCRM;