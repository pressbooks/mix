<?php
namespace PressbooksMix;

class Assets {
	/**
   * The plugin or theme slug.
   *
   * @type string
   */
	public static $slug;

	/**
   * The type of component (plugin | theme).
   *
   * @type string
   */
	public static $type;

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
   * @param string $type
   */
	public function __construct( $slug, $dist_directory = 'dist', $type = 'plugin' ) {
		self::$slug = $slug;
		self::$type = $type;
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

		if ( self::$type === 'plugin' ) {
			  $root_dir = trailingslashit( WP_PLUGIN_DIR );
		} elseif ( self::$type === 'theme' ) {
			  $root_dir = trailingslashit( get_theme_root( self::$slug ) );
		}

		if ( ! $manifest ) {
			$manifest_path = $root_dir . trailingslashit( self::$slug ) . self::$distDirectory . '/mix-manifest.json';

			if ( ! file_exists( $manifest_path ) ) {
				if ( self::$type === 'plugin' ) {
					return plugins_url( trailingslashit( self::$slug ) . self::$distDirectory . $path );
				} elseif ( self::$type === 'theme' ) {
					return trailingslashit( get_template_directory_uri() ) . self::$distDirectory . $path;
				}
			}

			$manifest = json_decode( file_get_contents( $manifest_path ), true );
		}

		if ( ! array_key_exists( $path, $manifest ) ) {
			if ( self::$type === 'plugin' ) {
				 return plugins_url( trailingslashit( self::$slug ) . self::$distDirectory . $path );
			} elseif ( self::$type === 'theme' ) {
				return trailingslashit( get_template_directory_uri() ) . self::$distDirectory . $path;
			}
		}

		if ( self::$type === 'plugin' ) {
			  return plugins_url( trailingslashit( self::$slug ) . self::$distDirectory . $manifest[ $path ] );
		} elseif ( self::$type === 'theme' ) {
			return trailingslashit( get_template_directory_uri() ) . self::$distDirectory . $manifest[ $path ];
		}
	}
}
