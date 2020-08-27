<?php
/**
 * Videos Options.
 *
 * @class WV_Options
 * @author WolfThemes
 * @category Admin
 * @package WolfVideos/Admin
 * @version 1.2.8
 */

defined( 'ABSPATH' ) || exit;

/**
 * WV_Options class.
 */
class WV_Options {
	/**
	 * Constructor
	 */
	public function __construct() {
		// register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// set default options
		add_action( 'admin_init', array( $this, 'default_options' ) );

		// add option sub-menu
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
	}

	/**
	 * Add options menu
	 */
	public function add_settings_menu() {

		add_submenu_page( 'edit.php?post_type=video', esc_html__( 'Settings', 'wolf-videos' ), esc_html__( 'Settings', 'wolf-videos' ), 'edit_plugins', 'wolf-videos-settings', array( $this, 'options_form' ) );
		add_submenu_page( 'edit.php?post_type=video', esc_html__( 'Shortcode', 'wolf-videos' ), esc_html__( 'Shortcode', 'wolf-videos' ), 'edit_plugins', 'wolf-videos-shortcode', array( $this, 'help' ) );
	}

	/**
	 * Set default options
	 */
	public function default_options() {

		global $options;

		if ( false ===  get_option( 'wolf_videos_settings' )  ) {

			$default = array(
				'isotope' => 1,
				'col' => 4,
			);

			add_option( 'wolf_videos_settings', $default );
		}
	}

	/**
	 * Register options
	 */
	public function register_settings() {

		register_setting( 'wolf-videos-settings', 'wolf_videos_settings', array( $this, 'settings_validate' ) );
		add_settings_section( 'wolf-videos-settings', '', array( $this, 'section_intro' ), 'wolf-videos-settings' );
		add_settings_field( 'page_id', esc_html__( 'Videos page', 'wolf-videos' ), array( $this, 'setting_page_id' ), 'wolf-videos-settings', 'wolf-videos-settings' );
		add_settings_field( 'columns', esc_html__( 'Max number of column', 'wolf-videos' ), array( $this, 'setting_columns' ), 'wolf-videos-settings', 'wolf-videos-settings', array( 'class' => 'wolf-videos-settings-columns' ) );
		add_settings_field( 'isotope', esc_html__( 'Use Isotope filter', 'wolf-videos' ), array( $this, 'setting_isotope' ), 'wolf-videos-settings', 'wolf-videos-settings', array( 'class' => 'wolf-videos-settings-isotope' ) );
	}

	/**
	 * Validate options
	 *
	 * @param array $input
	 * @return array $input
	 */
	public function settings_validate( $input ) {

		if ( isset( $input['page_id'] ) ) {
			update_option( '_wolf_videos_page_id', intval( $input['page_id'] ) );
			unset( $input['page_id'] );
		}

		$input['columns']= absint( $input['col'] );
		$input['isotope'] = boolval( $input['isotope'] );

		return $input;
	}

	/**
	 * Debug section
	 *
	 */
	public function section_intro() {
		// debug
		// var_dump(get_option('_wolf_videos_page_id'));
	}

	/**
	 * Page settings
	 *
	 */
	public function setting_page_id() {
		$page_option = array( '' => esc_html__( '- Disabled -', 'wolf-videos' ) );
		$pages = get_pages();

		foreach ( $pages as $page ) {

			if ( get_post_field( 'post_parent', $page->ID ) ) {
				$page_option[ absint( $page->ID ) ] = '&nbsp;&nbsp;&nbsp; ' . sanitize_text_field( $page->post_title );
			} else {
				$page_option[ absint( $page->ID ) ] = sanitize_text_field( $page->post_title );
			}
		}
		?>
		<select name="wolf_videos_settings[page_id]">
			<option value="-1"><?php esc_html_e( 'Select a page...', 'wolf-videos' ); ?></option>
			<?php foreach ( $page_option as $k => $v ) : ?>
				<option value="<?php echo absint( $k ); ?>" <?php selected( absint( $k ), get_option( '_wolf_videos_page_id' ) ); ?>><?php echo sanitize_text_field( $v ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Use custom style
	 */
	public function setting_columns() {
		$columns = array( 1, 2, 3, 4, 5, 6 );
		?>
		<select name="wolf_videos_settings[col]">
			<?php foreach ( $columns as $column ) : ?>
			<option <?php selected( $column, wolf_videos_get_option( 'col', 4 ) ); ?>><?php echo absint( $column ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php esc_html_e( 'Number of column on desktop screen', 'wolf-videos' ); ?>
		<?php
	}

	/**
	 * Use isotope filter
	 */
	public function setting_isotope() {
		?>
		<input type="hidden" name="wolf_videos_settings[isotope]" value="0">
		<label><input type="checkbox" name="wolf_videos_settings[isotope]" value="1" <?php echo ( ( wolf_videos_get_option( 'isotope' ) == 1) ? ' checked="checked"' : '' ); ?>>
		</label>
		<?php
	}

	/**
	 * Displays Shortcode help
	 */
	public function help() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Videos shortcode', 'wolf-videos' ) ?></h2>
			<p><?php esc_html_e( 'To display your last videos in your post or page you can use the following shortcode.', 'wolf-videos' ); ?></p>
			<p><code>[wolf_last_videos]</code></p>
			<p><?php esc_html_e( 'Additionally, you can add a count, columns, tag and category attributes.', 'wolf-videos' ); ?></p>
			<p><code>[wolf_last_videos col="2|3|4" count="4" category="my-category" tag="my-tag"]</code></p>
		</div>
		<?php
	}

	/**
	 * Options form
	 *
	 */
	public function options_form() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php esc_html_e( 'Videos options', 'wolf-videos' ); ?></h2>
			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error">
				<p><strong><?php esc_html_e( 'Settings saved.', 'wolf-videos' ); ?></strong></p>
			</div>
			<?php } ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'wolf-videos-settings' ); ?>
				<?php do_settings_sections( 'wolf-videos-settings' ); ?>
				<p class="submit"><input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf-videos' ); ?>" /></p>
			</form>
		</div>
		<?php
	}
}

return new WV_Options();