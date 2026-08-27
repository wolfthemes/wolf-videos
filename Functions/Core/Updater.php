<?php
/**
 * Updater
 *
 * Hooks into WordPress update system to check for new versions
 * of this plugin from downloads.wolfthemes.cloud.
 *
 * @package WolfVideos\Core
 */

namespace WolfVideos\Core;

defined( 'ABSPATH' ) || exit;

class Updater {

	const INFO_URL = 'https://downloads.wolfthemes.cloud/plugins/wolf-videos/info.json';
	const SLUG     = 'wolf-videos';
	const FILE     = 'wolf-videos/wolf-videos.php';

	public function __construct() {
		// Priority 20 so this wins over the legacy GitHub updater during the 1.4.0 transition.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 20 );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		add_filter( 'upgrader_post_install', array( $this, 'post_install' ), 10, 3 );
	}

	/**
	 * Fetch and cache info.json from the distribution server.
	 *
	 * @return object|false
	 */
	private function get_remote_info() {
		$transient_key = 'wv_remote_info';
		$data          = get_site_transient( $transient_key );

		if ( ! $data ) {
			$response = wp_remote_get(
				self::INFO_URL,
				array( 'timeout' => 10 )
			);

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$data = json_decode( wp_remote_retrieve_body( $response ) );

			// Validate the payload shape before trusting it as an update source.
			if (
				empty( $data->version ) || ! preg_match( '/^[0-9][0-9A-Za-z.\-]*$/', $data->version )
				|| empty( $data->download_url ) || ! wp_http_validate_url( $data->download_url )
				|| 'https' !== wp_parse_url( $data->download_url, PHP_URL_SCHEME )
			) {
				return false;
			}

			set_site_transient( $transient_key, $data, 6 * HOUR_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Inject update info into the WordPress update transient.
	 *
	 * @param object $transient
	 * @return object
	 */
	public function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$info            = $this->get_remote_info();
		$current_version = $transient->checked[ self::FILE ] ?? null;

		if ( $info && $current_version && version_compare( $info->version, $current_version, '>' ) ) {
			$transient->response[ self::FILE ] = (object) array(
				'slug'        => self::SLUG,
				'plugin'      => self::FILE,
				'new_version' => $info->version,
				'package'     => esc_url_raw( $info->download_url ),
				'url'         => isset( $info->homepage ) ? esc_url_raw( $info->homepage ) : '',
			);
		}

		return $transient;
	}

	/**
	 * Power the "View details" popup in wp-admin.
	 *
	 * @param false|object $plugin_info
	 * @param string       $action
	 * @param object       $args
	 * @return false|object
	 */
	public function plugin_info( $plugin_info, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $plugin_info;
		}

		if ( ! isset( $args->slug ) || self::SLUG !== $args->slug ) {
			return $plugin_info;
		}

		$info = $this->get_remote_info();

		if ( ! $info ) {
			return $plugin_info;
		}

		// Everything below is rendered as HTML in the wp-admin "View details" iframe —
		// sanitize the remote payload rather than trusting it verbatim.
		return (object) array(
			'name'          => isset( $info->name ) ? wp_strip_all_tags( $info->name ) : 'Videos',
			'slug'          => self::SLUG,
			'version'       => $info->version,
			'requires'      => isset( $info->requires ) ? wp_strip_all_tags( $info->requires ) : '6.0',
			'requires_php'  => isset( $info->requires_php ) ? wp_strip_all_tags( $info->requires_php ) : '7.4',
			'tested'        => isset( $info->tested ) ? wp_strip_all_tags( $info->tested ) : '',
			'author'        => isset( $info->author ) ? wp_kses_post( $info->author ) : 'WolfThemes',
			'homepage'      => isset( $info->homepage ) ? esc_url_raw( $info->homepage ) : '',
			'download_link' => esc_url_raw( $info->download_url ),
			'sections'      => array(
				'description' => isset( $info->description ) ? wp_kses_post( $info->description ) : '',
				'changelog'   => isset( $info->changelog ) ? wp_kses_post( $info->changelog ) : '',
			),
		);
	}

	/**
	 * After install: move the plugin to the correct folder name.
	 * WordPress extracts zips into a temp folder — this renames it properly.
	 *
	 * @param bool  $response
	 * @param mixed $hook_extra
	 * @param array $result
	 * @return array
	 */
	public function post_install( $response, $hook_extra, $result ) {
		unset( $response );

		if (
			! isset( $hook_extra['plugin'] ) ||
			self::FILE !== $hook_extra['plugin']
		) {
			return $result;
		}

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) || empty( $result['destination'] ) ) {
			return $result;
		}

		$proper = WP_PLUGIN_DIR . '/' . self::SLUG;
		$wp_filesystem->move( $result['destination'], $proper );
		$result['destination'] = $proper;

		if ( is_plugin_active( self::FILE ) ) {
			activate_plugin( self::FILE );
		}

		return $result;
	}
}
