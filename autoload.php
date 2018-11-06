<?php
/**
 * This autoloader looks for class declarations in the
 * /classes/ folder.
 *
 * @package pmpro-sitewide-sales
 */

namespace PMPro_Sitewide_Sales;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

/**
 * [autoload] This autoloader looks for class declarations as follows:
 *
 * PMPro_Sitewide_Sales\includes\classes\PMPro_SWS_Settings
 * ^          ^   ^
 * ^          ^   class-pmpro-sws-settings.php
 * ^          in the /classes/ folder
 * Project root
 *
 * @param  object $class Input is the set of classes found in
 * PMPro_Sitewide_Sales\includes\classes\PMPro_SWS_Settings.
 *
 * @return void Loads the class for inclusion.
 */
function autoload( $class ) {

	// project-specific namespace prefix.
	$prefix = 'PMPro_Sitewide_Sales\\';

	// base directory for the namespace prefix.
	$base_dir = __DIR__ . '/';

	// does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( 0 !== strncmp( $prefix, $class, $len ) ) {
		// no, move to the next registered autoloader.
		return;
	}

	// get the relative class name.
	$relative_class = substr( $class, $len );
	$relative_class = strtolower( $relative_class );
	$relative_class = str_replace( '_', '-', $relative_class );
	$relative_class = prepend_class( $relative_class );

	// replace the namespace prefix with the base directory,
	// replace namespace separators with directory separators
	// in the relative class name, append with .php.
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// if the file exists, require it.
	if ( file_exists( $file ) ) {
		require $file;
	}
}
/**
 * prepend_class This function ensures that the class follows
 * the naming conventions of prepending class filenames with
 * the prefix class-
 *
 * @param  string $string The filename.
 * @return string         The filename.
 */
function prepend_class( $string ) {
	$parts    = explode( '\\', $string );
	$new_name = '';

	for ( $i = 0; $i < count( $parts ); $i++ ) {

		if ( count( $parts ) - 1 === $i ) {
			$new_name .= 'class-';
			$new_name .= $parts[ $i ];
		} else {
			$new_name .= $parts[ $i ];
			$new_name .= '\\';
		}
	}

	return $new_name;
}

if ( function_exists( 'spl_autoload_register' ) ) {
	spl_autoload_register( __NAMESPACE__ . '\\autoload' );
}
