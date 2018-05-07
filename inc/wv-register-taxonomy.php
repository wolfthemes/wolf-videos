<?php
/**
 * %NAME% register taxonomy
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
	'name' => esc_html__( 'Videos Categories', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Videos Category', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Videos Categories', '%TEXTDOMAIN%' ),
	'popular_items' => esc_html__( 'Popular Videos Categories', '%TEXTDOMAIN%' ),
	'all_items' => esc_html__( 'All Videos Categories', '%TEXTDOMAIN%' ),
	'parent_item' => esc_html__( 'Parent Videos Category', '%TEXTDOMAIN%' ),
	'parent_item_colon' => esc_html__( 'Parent Videos Category:', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Videos Category', '%TEXTDOMAIN%' ),
	'update_item' => esc_html__( 'Update Videos Category', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Videos Category', '%TEXTDOMAIN%' ),
	'new_item_name' => esc_html__( 'New Videos Category', '%TEXTDOMAIN%' ),
	'separate_items_with_commas' => esc_html__( 'Separate videos categories with commas', '%TEXTDOMAIN%' ),
	'add_or_remove_items' => esc_html__( 'Add or remove videos categories', '%TEXTDOMAIN%' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used videos categories', '%TEXTDOMAIN%' ),
	'not_found' => esc_html__( 'No categories found', '%TEXTDOMAIN%' ),
	'menu_name' => esc_html__( 'Categories', '%TEXTDOMAIN%' ),
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
	'name' => esc_html__( 'Tags', '%TEXTDOMAIN%' ),
	'singular_name' => esc_html__( 'Tag', '%TEXTDOMAIN%' ),
	'search_items' => esc_html__( 'Search Tags', '%TEXTDOMAIN%' ),
	'popular_items' => esc_html__( 'Popular Tags', '%TEXTDOMAIN%' ),
	'all_items' => esc_html__( 'All Tags', '%TEXTDOMAIN%' ),
	'parent_item' => esc_html__( 'Parent Tag', '%TEXTDOMAIN%' ),
	'parent_item_colon' => esc_html__( 'Parent Tag:', '%TEXTDOMAIN%' ),
	'edit_item' => esc_html__( 'Edit Tag', '%TEXTDOMAIN%' ),
	'update_item' => esc_html__( 'Update Tag', '%TEXTDOMAIN%' ),
	'add_new_item' => esc_html__( 'Add New Tag', '%TEXTDOMAIN%' ),
	'new_item_name' => esc_html__( 'New Tag', '%TEXTDOMAIN%' ),
	'separate_items_with_commas' => esc_html__( 'Separate tags with commas', '%TEXTDOMAIN%' ),
	'add_or_remove_items' => esc_html__( 'Add or remove tags', '%TEXTDOMAIN%' ),
	'choose_from_most_used' => esc_html__( 'Choose from the most used tags', '%TEXTDOMAIN%' ),
	'not_found' => esc_html__( 'No tags found', '%TEXTDOMAIN%' ),
	'menu_name' => esc_html__( 'Tags', '%TEXTDOMAIN%' ),
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