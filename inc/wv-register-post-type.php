<?php
/**
 * %NAME% register post type
 *
 * @author %AUTHOR%
 * @category Core
 * @package %PACKAGENAME%/Admin
 * @version %VERSION%
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$labels = array(
	'name' => esc_html__( 'Videos', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Video', '%TEXTDOMAIN%' ),
	'add_new' => esc_html__( 'Add New', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Video', '%TEXTDOMAIN%' ),
	'all_items'  => esc_html__( 'All Videos', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Video', '%TEXTDOMAIN%' ),
	'new_item' => esc_html__( 'New Video', '%TEXTDOMAIN%' ),
	'view_item' => esc_html__( 'View Video', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Videos', '%TEXTDOMAIN%' ),
	'not_found' => esc_html__( 'No Videos found', '%TEXTDOMAIN%' ),
	'not_found_in_trash' => esc_html__( 'No Videos found in Trash', '%TEXTDOMAIN%' ),
	'parent_item_colon' => '',
	'menu_name' => esc_html__( 'Videos', '%TEXTDOMAIN%' ),
);

$args = array(

	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'show_ui' => true,
	'show_in_menu' => true,
	'query_var' => false,
	'rewrite' => array( 'slug' => 'video' ),
	'capability_type' => 'post',
	'has_archive' => false,
	'hierarchical' => false,
	'menu_position' => 5,
	'taxonomies' => array(),
	'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields', 'comments' ),
	'exclude_from_search' => false,

	'description' => esc_html__( 'Present your video', '%TEXTDOMAIN%' ),
	'menu_icon' => 'dashicons-format-video',
);

register_post_type( 'video', $args );