<?php
/**
 * Videos register post type
 *
 * @author WolfThemes
 * @category Core
 * @package WolfVideos/Admin
 * @version 1.2.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$labels = array(
	'name' => esc_html__( 'Videos', 'wolf-videos' ),
	'singular_name' => esc_html__( 'Video', 'wolf-videos' ),
	'add_new' => esc_html__( 'Add New', 'wolf-videos' ),
	'add_new_item' => esc_html__( 'Add New Video', 'wolf-videos' ),
	'all_items'  => esc_html__( 'All Videos', 'wolf-videos' ),
	'edit_item' => esc_html__( 'Edit Video', 'wolf-videos' ),
	'new_item' => esc_html__( 'New Video', 'wolf-videos' ),
	'view_item' => esc_html__( 'View Video', 'wolf-videos' ),
	'search_items' => esc_html__( 'Search Videos', 'wolf-videos' ),
	'not_found' => esc_html__( 'No Videos found', 'wolf-videos' ),
	'not_found_in_trash' => esc_html__( 'No Videos found in Trash', 'wolf-videos' ),
	'parent_item_colon' => '',
	'menu_name' => esc_html__( 'Videos', 'wolf-videos' ),
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

	'description' => esc_html__( 'Present your video', 'wolf-videos' ),
	'menu_icon' => 'dashicons-format-video',
);

register_post_type( 'video', $args );