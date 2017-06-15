<?php

namespace PressbooksMix;

class Assets {

	/**
	 * The plugin or theme slug.
	 *
	 * @type string
	 */
	protected $slug;

	/**
	 * The type of component (plugin | theme).
	 *
	 * @type string
	 */
	protected $type;

	/**
	 * The directory where source assets are located.
	 *
	 * @type string
	 */
	protected $srcDirectory = 'assets/src';

	/**
	 * The directory where built assets are located.
	 *
	 * @type string
	 */
	protected $distDirectory = 'assets/dist';

	/**
	 * Store manifests statically
	 *
	 * @var array
	 */
	protected static $manifest = [];

	/**
	 * Constructor.
	 *
	 * @param string $slug The plugin or theme slug
	 * @param string $type The type of component (plugin | theme)
	 */
	public function __construct( $slug, $type ) {
		$this->setSlug( $slug );
		$this->setType( $type );
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * @param string $slug
	 *
	 * @return Assets
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @throws \Exception
	 *
	 * @return Assets
	 */
	public function setType( $type ) {
		if ( ! in_array( $type, [ 'plugin', 'theme' ], true ) ) {
			throw new \Exception( $type . ' not supported.' );
		}
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSrcDirectory() {
		return $this->srcDirectory;
	}

	/**
	 * @param string $srcDirectory
	 *
	 * @return Assets
	 */
	public function setSrcDirectory( $srcDirectory ) {
		$this->srcDirectory = $srcDirectory;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDistDirectory() {
		return $this->distDirectory;
	}

	/**
	 * @param string $distDirectory
	 *
	 * @return Assets
	 */
	public function setDistDirectory( $distDirectory ) {
		$this->distDirectory = $distDirectory;
		return $this;
	}

	/**
	 * Loads the mix-manifest.json file and returns the versioned URL of an asset located at $path.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function getPath( $path ) {

		$path = trim( $path );
		if ( substr( $path, 0, 1 ) !== '/' ) {
			$path = "/{$path}";
		}

		if ( $this->type === 'theme' ) {
			return $this->getThemePath( $path );
		} else {
			return $this->getPluginPath( $path );
		}
	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	public function getPluginPath( $path ) {

		$root_dir = trailingslashit( WP_PLUGIN_DIR );
		$fallback = plugins_url( trailingslashit( $this->slug ) . $this->srcDirectory . $path );
		$hash = md5( $this->slug . $this->type . $this->distDirectory );

		if ( ! isset( self::$manifest[ $hash ] ) ) {
			$manifest_path = $root_dir . trailingslashit( $this->slug ) . $this->distDirectory . '/mix-manifest.json';
			if ( ! file_exists( $manifest_path ) ) {
				self::$manifest[ $hash ] = [];
				return $fallback;
			}
			self::$manifest[ $hash ] = json_decode( file_get_contents( $manifest_path ), true );
		}

		if ( ! array_key_exists( $path, self::$manifest[ $hash ] ) ) {
			return $fallback;
		}

		return plugins_url( trailingslashit( $this->slug ) . $this->distDirectory . self::$manifest[ $hash ][ $path ] );
	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	public function getThemePath( $path ) {

		$root_dir = trailingslashit( get_theme_root( $this->slug ) );
		$fallback = trailingslashit( get_template_directory_uri() ) . $this->srcDirectory . $path;
		$hash = md5( $this->slug . $this->type . $this->distDirectory );

		if ( ! isset( self::$manifest[ $hash ] ) ) {
			$manifest_path = $root_dir . trailingslashit( $this->slug ) . $this->distDirectory . '/mix-manifest.json';
			if ( ! file_exists( $manifest_path ) ) {
				self::$manifest[ $hash ] = [];
				return $fallback;

			}
			self::$manifest[ $hash ] = json_decode( file_get_contents( $manifest_path ), true );
		}

		if ( ! array_key_exists( $path, self::$manifest[ $hash ] ) ) {
			return $fallback;
		}

		return trailingslashit( get_template_directory_uri() ) . $this->distDirectory . self::$manifest[ $hash ][ $path ];
	}
}
