<?php
namespace PressbooksMix;

class Assets {
	/**
   * The plugin slug.
   *
   * @type string
   */
	public static $pluginSlug;

	/**
   * The directory within the plugin where built assets are located.
   *
   * @type string
   */
	public static $distDirectory;

	/**
   * Constructor.
   *
   * @param string $plugin_slug
   * @param string $dist_directory
   */
	public function __construct( $plugin_slug, $dist_directory = 'dist' ) {
		self::$pluginSlug = $plugin_slug;
		self::$distDirectory = $dist_directory;
	}

	/**
   * Loads the mix-manifest.json file and returns the versioned URL of an asset located at $path.
   *
   * @param string $path
   * @return string
   */
	public function getPath( $path ) {
		static $manifest;

		if ( substr( $path, 0, 1 ) !== '/' ) {
			$path = "/{$path}";
		}

		$root_dir = trailingslashit( WP_PLUGIN_DIR );

		if ( ! $manifest ) {
			$manifest_path = $root_dir . trailingslashit( self::$pluginSlug ) . self::$distDirectory . '/mix-manifest.json';

			if ( ! file_exists( $manifest_path ) ) {
				return plugins_url( trailingslashit( self::$pluginSlug ) . self::$distDirectory . $path );
			}

			$manifest = json_decode( file_get_contents( $manifest_path ), true );
		}

		if ( ! array_key_exists( $path, $manifest ) ) {
			return plugins_url( trailingslashit( self::$pluginSlug ) . self::$distDirectory . $path );
		}

		return plugins_url( trailingslashit( self::$pluginSlug ) . self::$distDirectory . $manifest[ $path ] );
	}
}
