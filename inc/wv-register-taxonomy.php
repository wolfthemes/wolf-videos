<?php
/**
 * Videos register taxonomy
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
	'name' => esc_html__( 'Videos Categories', 'wolf-videos' ),
	'singular_name' => esc_html__( 'Videos Category', 'wolf-videos' ),
	'search_items' => esc_html__( 'Search Videos Categories', 'wolf-videos' ),
	'popular_items' => esc_html__( 'Popular Videos Categories', 'wolf-videos' ),
	'all_items' => esc_html__( 'All Videos Categories', 'wolf-videos' ),
	'parent_item' => esc_html__( 'Parent Videos Category', 'wolf-videos' ),
	'parent_item_colon' => esc_html__( 'Parent Videos Category:', 'wolf-videos' ),
	'edit_item' => esc_html__( 'Edit Videos Category', 'wolf-videos' ),
	'update_item' => esc_html__( 'Update Videos Category', 'wolf-videos' ),
	'add_new_item' => esc_html__( 'Add New Videos Category', 'wolf-videos' ),
	'new_item_name' => esc_html__( 'New Videos Category', 'wolf-videos' ),
	'separate_items_with_commas' => esc_html__( 'Separate videos categories with commas', 'wolf-videos' ),
	'add_or_remove_items' => esc_html__( 'Add or remove videos categories', 'wolf-videos' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used videos categories', 'wolf-videos' ),
	'not_found' => esc_html__( 'No categories found', 'wolf-videos' ),
	'menu_name' => esc_html__( 'Categories', 'wolf-videos' ),
);

$args = array(

	'labels' => $labels,
	'hierarchical' => true,
	'public' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'video-type', 'with_front' => false ),
);

register_taxonomy( 'video_type', array( 'video' ), $args );

$labels = array(
	'name' => esc_html__( 'Tags', 'wolf-videos' ),
	'singular_name' => esc_html__( 'Tag', 'wolf-videos' ),
	'search_items' => esc_html__( 'Search Tags', 'wolf-videos' ),
	'popular_items' => esc_html__( 'Popular Tags', 'wolf-videos' ),
	'all_items' => esc_html__( 'All Tags', 'wolf-videos' ),
	'parent_item' => esc_html__( 'Parent Tag', 'wolf-videos' ),
	'parent_item_colon' => esc_html__( 'Parent Tag:', 'wolf-videos' ),
	'edit_item' => esc_html__( 'Edit Tag', 'wolf-videos' ),
	'update_item' => esc_html__( 'Update Tag', 'wolf-videos' ),
	'add_new_item' => esc_html__( 'Add New Tag', 'wolf-videos' ),
	'new_item_name' => esc_html__( 'New Tag', 'wolf-videos' ),
	'separate_items_with_commas' => esc_html__( 'Separate tags with commas', 'wolf-videos' ),
	'add_or_remove_items' => esc_html__( 'Add or remove tags', 'wolf-videos' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used tags', 'wolf-videos' ),
	'not_found' => esc_html__( 'No tags found', 'wolf-videos' ),
	'menu_name' => esc_html__( 'Tags', 'wolf-videos' ),
);

$args = array(
	'hierarchical' => false,
	'labels' => $labels,
	'show_ui' => true,
	'update_count_callback' => '_update_post_term_count',
	'query_var' => true,
	'rewrite' => array( 'slug' => 'video-tag', 'with_front' => false),
);

register_taxonomy( 'video_tag', array( 'video' ), $args );