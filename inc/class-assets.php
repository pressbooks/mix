<?php
namespace Pressbooks_Mix;

class Assets {
  public static $pluginSlug;

  public function __construct( $slug ) {
    self::$pluginSlug = $slug;
  }

  public function getPath( $path, $dist_dir = 'dist' ) {
    static $manifest;

    if ( substr( $path, 0, 1 ) === '/' ) {
      $path = "/{$path}";
    }

   	$root_dir = trailingslashit( WP_PLUGIN_DIR );

   	if ( file_exists( $root_dir . self::$pluginSlug . '/hot' ) ) {
   		return 'http://localhost:8080' . $path;
   	}

   	if ( ! $manifest ) {
   		$manifest_path = $root_dir . trailingslashit( self::$pluginSlug ) . $dist_path . '/mix-manifest.json';

   		if ( ! file_exists( $manifest_path ) ) {
  			return plugins_url( trailingslashit( self::$pluginSlug ) . trailingslashit( $dist_path ) . $path );
   		}

   		$manifest = json_decode( file_get_contents( $manifest_path ), true );
   	}

   	if ( ! array_key_exists( $path, $manifest ) ) {
   		return plugins_url( trailingslashit( self::$pluginSlug ) . trailingslashit( $dist_path ) . $path );
   	}

   	return plugins_url( trailingslashit( self::$pluginSlug ) . trailingslashit( $dist_path ) . $manifest[ $path ] );

  }
}
