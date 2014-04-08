<?php
/**
 * Plugin Name: Wolf Videos
 * Plugin URI: http://wpwolf.com/plugin/wolf-videos
 * Description: A ready-to-use video gallery custom post type with Isotope filter.
 * Version: 1.0.4
 * Author: WpWolf
 * Author URI: http://wpwolf.com
 * Requires at least: 3.5
 * Tested up to: 3.8.1
 *
 * Text Domain: wolf
 * Domain Path: /lang/
 *
 * @package WolfVideos
 * @author WpWolf
 *
 * Being a free product, this plugin is distributed as-is without official support. 
 * Verified customers however, who have purchased a premium theme
 * at http://themeforest.net/user/BrutalDesign/portfolio?ref=BrutalDesign
 * will have access to support for this plugin in the forums
 * http://help.wpwolf.com/
 *
 * Copyright (C) 2014 Constantin Saguin
 * This WordPress Plugin is a free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * See http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Videos' ) ) {
	/**
	 * Main Wolf_Videos Class
	 *
	 * Contains the main functions for Wolf_Videos
	 *
	 * @class Wolf_Videos
	 * @version 1.0.4
	 * @since 1.0.0
	 * @package WolfVideos
	 * @author WpWolf
	 */
	class Wolf_Videos {

		/**
		 * @var string
		 */
		public $version = '1.0.4';

		/**
		 * @var string
		 */
		private $update_url = 'http://plugins.wpwolf.com/update';

		/**
		 * @var string
		 */
		public $plugin_url;

		/**
		 * @var string
		 */
		public $plugin_path;

		/**
		 * @var string
		 */
		public $template_url;

		/**
		 * Wolf_Videos Constructor.
		 */
		public function __construct() {

			// Flush rewrite rules on activation
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			// Shortcode help menu
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			
			// add options page
			add_action( 'admin_init', array( $this, 'options' ) );

			// check if videos page exists
			add_action( 'admin_notices', array( $this, 'check_page' ) );
			add_action( 'admin_notices', array( $this, 'create_page' ) );

			// add video thumbnail image sizes
			add_image_size( 'video-cover', 450, 253, true );

			// Include required files
			$this->includes();

			// init
			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'init', array( $this, 'include_template_functions' ), 25 );

			// register shortcode
			add_shortcode( 'wolf_last_videos', array( $this, 'shortcode' ) );

			// styles
			add_action( 'wp_print_styles', array( $this, 'print_styles' ) );

			// scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// set default options
			add_action( 'after_setup_theme', array( $this, 'default_options' ) );
		}

		/**
		 * Activation function
		 */
		public function activate( $network_wide ) {
			
			// do stuff
		}

		/**
		 * plugin update notification.
		 */
		public function update() {
			
			$plugin_data     = get_plugin_data( __FILE__ );
			$current_version = $plugin_data['Version'];
			$plugin_slug     = plugin_basename( dirname( __FILE__ ) );
			$plugin_path     = plugin_basename( __FILE__ );
			$remote_path     = $this->update_url . '/' . $plugin_slug;
			
			if ( ! class_exists( 'Wolf_WP_Update' ) )
				include_once( 'classes/class-wp-update.php' );
			
			$wolf_plugin_update = new Wolf_WP_Update( $current_version, $remote_path, $plugin_path );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			if ( is_admin() )
				$this->admin_includes();

			if ( ! is_admin() || defined( 'DOING_AJAX' ) )
				$this->frontend_includes();

			// Core functions
			include_once( 'includes/core-functions.php' );

		}

		/**
		 * Include required admin files.
		 */
		public function admin_includes() {
			include_once( 'classes/class-video-thumbnails.php' ); // Video Thumbnail Generator
		}

		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			
			// Functions
			include_once( 'includes/hooks.php' ); // Template hooks used on the front-end
			include_once( 'includes/functions.php' ); // Contains functions for various front-end events
			
		}

		/**
		 * Function used to Init WolfVideos Template Functions - This makes them pluggable by plugins and themes.
		 */
		public function include_template_functions() {
			
			include_once( 'includes/template.php' );

		}

		/**
		 * Check videos page
		 *
		 * Display a notification if we can't get the videos page id
		 *
		 */
		public function check_page() {

			// delete_option( '_wolf_videos_page_id' );
			// delete_option( 'wolf_video_settings' );
			// var_dump( get_option('wolf_video_settings') );

			$output    = '';
			$theme_dir = get_template_directory();

			if ( -1 == wolf_videos_get_page_id() && ! isset( $_GET['wolf_videos_create_page'] ) ) {

				$message = '<strong>Wolf Videos</strong> ' . sprintf(
					__( 'says : <em>Almost done! you need to <a href="%1$s">create a page</a> for your videos or <a href="%2$s">select an existing page</a> in the plugin settings</em>', 'wolf' ), 
						esc_url( admin_url( '?wolf_videos_create_page=true' ) ),
						esc_url( admin_url( 'edit.php?post_type=video&page=wolf-video-settings' ) )
				);

				$output = '<div class="updated"><p>';

				$output .= $message;

				$output .= '</p></div>';

				echo $output;

			}

			return false;
		}

		/**
		 * Create videos page
		 */
		public function create_page() {

			if ( isset( $_GET['wolf_videos_create_page'] ) && $_GET['wolf_videos_create_page'] == 'true' ) {
				
				$output = '';

				// Create post object
				$post = array(
					'post_title'  => 'Videos',
					'post_type'   => 'page',
					'post_status' => 'publish',
				);

				// Insert the post into the database
				$post_id = wp_insert_post( $post );

				if ( $post_id ) {
					
					update_option( '_wolf_videos_page_id', $post_id );
					
					$message = __( 'Your videos page has been created succesfully', 'wolf' );

					$output = '<div class="updated"><p>';

					$output .= $message;

					$output .= '</p></div>';

					echo $output;
				}

			}

			return false;

		}

		/**
		 * Init WolfVideos when WordPress Initialises.
		 */
		public function init() {

			// Set up localisation
			$this->load_plugin_textdomain();

			// Variables
			$this->template_url = apply_filters( 'wolf_videos_template_url', 'wolf-videos/' );

			// Classes/actions loaded for the frontend and for ajax requests
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {

				// Hooks
				add_filter( 'template_include', array( $this, 'template_loader' ) );

			}

			// register post type
			$this->register_post_type();

			// register post type
			$this->register_taxonomy();

		}

		/**
		 * Load Localisation files.
		 */
		public function load_plugin_textdomain() {

			$domain = 'wolf';
			$locale = apply_filters( 'wolf', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		}

		/**
		 * Load a template.
		 *
		 * Handles template usage so that we can use our own templates instead of the themes.
		 *
		 * @param mixed $template
		 * @return string
		 */
		public function template_loader( $template ) {

			$find = array();
			$file = '';

			if ( is_tax( 'video_type' ) ) {

				$term = get_queried_object();

				$file   = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = $this->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;

			}

			if ( $file ) {
				$template = locate_template( $find );
				if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
			}

			return $template;
		}

		/**
		 * Print CSS styles
		 */
		public function print_styles() {

			wp_enqueue_style( 'wolf-videos', $this->plugin_url() . '/assets/css/videos.min.css', array(), $this->version, 'all' );

		}

		/**
		 * Enqueue JS script in footer
		 */
		public function enqueue_scripts() {

			if ( $this->get_option( 'isotope' ) && is_page( wolf_videos_get_page_id() ) ) {

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'isotope', $this->plugin_url() . '/assets/js/lib/jquery.isotope.min.js', 'jquery', '1.5.25', true );
				wp_enqueue_script( 'wolf-videos', $this->plugin_url() . '/assets/js/app.min.js', 'jquery', $this->version, true );
				wp_localize_script(
						'wolf-videos', 'WolfVideosParams', array(
							'columns' => $this->get_option( 'col', 3 ),
						)
				);
			}
		}
		
		/**
		 * Register post type
		 */
		public function register_post_type() {


			$labels = array( 
				'name' => __( 'Videos', 'wolf' ),
				'singular_name' => __( 'Video', 'wolf' ),
				'add_new' => __( 'Add New', 'wolf' ),
				'add_new_item' => __( 'Add New Video', 'wolf' ),
				'all_items'  => __( 'All Videos', 'wolf' ),
				'edit_item' => __( 'Edit Video', 'wolf' ),
				'new_item' => __( 'New Video', 'wolf' ),
				'view_item' => __( 'View Video', 'wolf' ),
				'search_items' => __( 'Search Videos', 'wolf' ),
				'not_found' => __( 'No Videos found', 'wolf' ),
				'not_found_in_trash' => __( 'No Videos found in Trash', 'wolf' ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Videos', 'wolf' ),
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

				'description' => __( 'Present your video', 'wolf' ),
				'menu_icon' => 'dashicons-format-video',
			);

			register_post_type( 'video', $args );
		}

		/**
		 * Register taxonomy
		 */
		public function register_taxonomy() {

			$labels = array( 
				'name' => __( 'Videos Categories', 'wolf' ),
				'singular_name' => __( 'Videos Type', 'wolf' ),
				'search_items' => __( 'Search Videos Categories', 'wolf' ),
				'popular_items' => __( 'Popular Videos Categories', 'wolf' ),
				'all_items' => __( 'All Videos Categories', 'wolf' ),
				'parent_item' => __( 'Parent Videos Type', 'wolf' ),
				'parent_item_colon' => __( 'Parent Videos Type:', 'wolf' ),
				'edit_item' => __( 'Edit Videos Type', 'wolf' ),
				'update_item' => __( 'Update Videos Type', 'wolf' ),
				'add_new_item' => __( 'Add New Videos Type', 'wolf' ),
				'new_item_name' => __( 'New Videos Type', 'wolf' ),
				'separate_items_with_commas' => __( 'Separate videos categories with commas', 'wolf' ),
				'add_or_remove_items' => __( 'Add or remove videos categories', 'wolf' ),
				'choose_from_most_used' => __( 'Choose from the most used videos categories', 'wolf' ),
				'menu_name' => __( 'Categories', 'wolf' ),
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
		}

		
		/**
		 * Add submenu with settings and shortcode help
		 */
		public function add_menu() {

			add_submenu_page( 'edit.php?post_type=video', __( 'Settings', 'wolf' ), __( 'Settings', 'wolf' ), 'edit_plugins', 'wolf-video-settings', array( $this, 'options_form' ) );
			add_submenu_page( 'edit.php?post_type=video', __( 'Shortcode', 'wolf' ), __( 'Shortcode', 'wolf' ), 'edit_plugins', 'wolf-video-shortcode', array( $this, 'help' ) );
		}

		/**
		 * Set default settings
		 */
		public function default_options() {
			
			global $options;

			if ( false === get_option( 'wolf_video_settings' ) ) {

				$default = array(
					'isotope' => 1,
					'col' => 3,
				);

				add_option( 'wolf_video_settings', $default );
			}
		}

		/**
		 * Get options
		 */
		public function get_option( $value, $default = null ) {
			
			global $options;

			$wolf_video_settings = get_option( 'wolf_video_settings' );

			if ( isset( $wolf_video_settings[$value] ) ) {
				
				return $wolf_video_settings[$value];

			} elseif ( $default ) {

				return $default;

			}
				
		}

		/**
		 * Init Settings
		 */
		public function options() {

			register_setting( 'wolf-video-settings', 'wolf_video_settings', array( $this, 'settings_validate' ) );
			add_settings_section( 'wolf-video-settings', '', array( $this, 'section_intro' ), 'wolf-video-settings' );
			add_settings_field( 'page_id', __( 'Videos Page', 'wolf' ), array( $this, 'setting_page_id' ), 'wolf-video-settings', 'wolf-video-settings' );
			add_settings_field( 'columns', __( 'Max number of column', 'wolf' ), array( $this, 'setting_columns' ), 'wolf-video-settings', 'wolf-video-settings' );
			add_settings_field( 'isotope', __( 'Use Isotope filter', 'wolf' ), array( $this, 'setting_isotope' ), 'wolf-video-settings', 'wolf-video-settings' );

		}

		/**
		 * Validate settings
		 */
		public function settings_validate( $input ) {
			
			$input['col']     = intval( $input['col'] );
			$input['isotope'] = intval( $input['isotope'] );

			if ( isset( $input['page_id'] ) ) {
				update_option( '_wolf_videos_page_id', intval( $input['page_id'] ) );
				unset( $input['page_id'] );
			}

			return $input;
		}

		/**
		 * Intro section
		 *
		 * @return string
		 */
		public function section_intro() {
			
			// add instructions
		}

		/**
		 * Page settings
		 *
		 * @return string
		 */
		public function setting_page_id() {
			$pages = get_pages();
			?>
			<select name="wolf_video_settings[page_id]">
				<option value="-1"><?php _e( 'Select a page...', 'wolf' ); ?></option>
				<?php foreach ( $pages as $page ) : ?>
					<option <?php if ( intval( $page->ID ) == get_option( '_wolf_videos_page_id' ) ) echo 'selected="selected"'; ?> value="<?php echo intval( $page->ID ); ?>"><?php echo sanitize_text_field( $page->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php
		}

		/**
		 * Use custom style
		 */
		public function setting_columns() {
			$columns = array( 5, 4, 3 );
			?>
			<select name="wolf_video_settings[col]">
				<?php foreach ( $columns as $column ) : ?>
				<option <?php if ( $column == $this->get_option( 'col', 4 ) ) echo 'selected="selected"'; ?>><?php echo intval( $column ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php _e( 'Number of column on desktop screen', 'wolf' ); ?>
			<?php
		}

		/**
		 * Use isotope filter
		 */
		public function setting_isotope() {
			?>
			<input type="hidden" name="wolf_video_settings[isotope]" value="0">
			<label><input type="checkbox" name="wolf_video_settings[isotope]" value="1" <?php echo ( ( $this->get_option( 'isotope' ) == 1) ? ' checked="checked"' : '' ); ?>>
			</label>
			<?php
		}

		/**
		 * Display options form
		 */
		public function options_form() {
			?>
			<div class="wrap">
				<h2><?php _e( 'Videos Settings' ) ?></h2>
				<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
					<div id="setting-error-settings_updated" class="updated settings-error"> 
						<p><strong><?php _e( 'Settings saved.', 'wolf' ); ?></strong></p>
					</div>
				<?php } ?>
				<form action="options.php" method="post">
					<?php settings_fields( 'wolf-video-settings' ); ?>
					<?php do_settings_sections( 'wolf-video-settings' ); ?>
					<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>" /></p>
				</form>
			</div>
			<?php
		}

		/**
		 * Displays Shortcode help
		 */
		public function help() {
			?>
			<div class="wrap">
				<h2><?php _e( 'Videos Shortcode', 'wolf' ) ?></h2>
				<p><?php _e( 'To display your last videos in your post or page you can use the following shortcode.', 'wolf' ); ?></p>
				<p><code>[wolf_last_videos]</code></p>
				<p><?php _e( 'Additionally, you can add a count and/or a category attribute.', 'wolf' ); ?></p>
				<p><code>[wolf_last_videos count="6" category="my-category"]</code></p>

				<p><?php _e( 'You can also add a column count attribute.', 'wolf' ); ?></p>
				<p><code>[wolf_last_videos col="2|3|4" category="my-category"]</code></p>
			</div>
			<?php
		}

		/**
		 * Shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
		function shortcode( $atts ) {

			extract(
				shortcode_atts(
					array(
						'count' => 3,
						'category' => null,
						'col' => $this->get_option( 'col', 3 ),
					), $atts
				)
			);

			ob_start();

			$args = array(
				'post_type' => array( 'video' ),
				'posts_per_page' => absint( $count ),
			);

			if ( $category ) {
				$args['video_type'] = $category;
			}

			$loop = new WP_Query( $args );
			if ( $loop->have_posts() ) : ?>
				<ul class="shortcode-videos-grid video-grid-col-<?php echo absint( $count ); ?>">
					<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

						<?php wolf_videos_get_template_part( 'content', 'video' ); ?>

					<?php endwhile; ?>
				</ul><!-- .shortcode-videos-grid -->
			<?php else : // no video ?>
				<?php wolf_videos_get_template( 'loop/no-video-found.php' ); ?>
			<?php endif;
			wp_reset_postdata();

			$html = ob_get_contents();
			ob_end_clean();
			return $html;

		}

		/**
		 * Single video post navigation
		 *
		 * @return string
		 */
		public function navigation() {
			
			global $post;

			// Don't print empty markup if there's nowhere to navigate.
			$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
			$next     = get_adjacent_post( false, '', false );

			if ( ! $next && ! $previous )
				return;
			?>
			<nav class="video-navigation" role="navigation">
				<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'wolf' ) ); ?>
				<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'wolf' ) ); ?>
			</nav><!-- .navigation -->
			<?php
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) return $this->plugin_url;
			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			if ( $this->plugin_path ) return $this->plugin_path;

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	} // end class

	/**
	 * Init Wolf_Videos class
	 */
	$GLOBALS['wolf_videos'] = new Wolf_Videos();

} // end class exists check