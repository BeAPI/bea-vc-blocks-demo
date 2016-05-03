<?php
namespace BEA\VC_BD;
/**
 * The purpose of the API class is to have the basic reusable methods like :
 *  - Template include
 *  - Template searcher
 *  - Date formatting
 *
 * You can put here all of the tools you use in the project but not
 * limited to an object or a context.
 * It's recommended to use static methods for simple accessing to the methods
 * and stick to the non context methods
 *
 * Class API
 * @package BEA\VC_BD
 */
class Helpers {

	/**
	 * Use the trait
	 */
	use Singleton;

	/**
	 * Return VC Templates
	 * @return array VC Templates
	 */
	public static function get_vc_templates() {

		$files = (array) self::get_files( BEA_VC_BD_VIEWS_FOLDER_NAME, 'php', true, true );

		if ( empty( $files ) ) {
			return false;
		}

		$vc_templates = array();
		foreach ( $files as $file => $full_path ) {
			if ( ! preg_match( '|VC Template Name:(.*)$|mi', file_get_contents( $full_path ), $header ) ) {
				continue;
			}
			$vc_templates[ $full_path ] = $header[1];
		}

		return $vc_templates;
	}

	/**
	 * Return files in the theme's directory.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $dir Required. Directory to search files.
	 * @param mixed $type Optional. Array of extensions to return. Defaults to all files (null).
	 * @param int $depth Optional. How deep to search for files. Defaults to a flat scan (0 depth). -1 depth is infinite.
	 * @param bool $search_parent Optional. Whether to return parent files. Defaults to false.
	 * @return array Array of files, keyed by the path to the file relative to the theme's directory, with the values
	 * 	             being absolute paths.
	 */
	public static function get_files( $dir, $type = null, $depth = 0, $search_parent = false ) {
		if ( empty( $dir ) ) {
			return false;
		}

		$files = (array) self::scandir( trailingslashit( get_stylesheet_directory() ) . $dir, $type, $depth );

		if ( $search_parent ) {
			$files += (array) self::scandir( trailingslashit( get_template_directory() ) . $dir, $type, $depth );
		}

		return $files;
	}

	/**
	 * Scans a directory for files of a certain extension.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string            $path          Absolute path to search.
	 * @param array|string|null $extensions    Optional. Array of extensions to find, string of a single extension,
	 *                                         or null for all extensions. Default null.
	 * @param int               $depth         Optional. How many levels deep to search for files. Accepts 0, 1+, or
	 *                                         -1 (infinite depth). Default 0.
	 * @param string            $relative_path Optional. The basename of the absolute path. Used to control the
	 *                                         returned path for the found files, particularly when this function
	 *                                         recurses to lower depths. Default empty.
	 * @return array|false Array of files, keyed by the path to the file relative to the `$path` directory prepended
	 *                     with `$relative_path`, with the values being absolute paths. False otherwise.
	 */
	private static function scandir( $path, $extensions = null, $depth = 0, $relative_path = '' ) {
		if ( ! is_dir( $path ) ) {
			return array();
		}

		if ( $extensions ) {
			$extensions = (array) $extensions;
			$_extensions = implode( '|', $extensions );
		}

		$relative_path = trailingslashit( $relative_path );
		if ( '/' == $relative_path ) {
			$relative_path = '';
		}

		$results = scandir( $path );
		$files = array();

		foreach ( $results as $result ) {
			if ( '.' == $result[0] ) {
				continue;
			}
			if ( is_dir( $path . '/' . $result ) ) {
				if ( ! $depth || 'CVS' == $result ) {
					continue;
				}
				$found = self::scandir( $path . '/' . $result, $extensions, $depth - 1 , $relative_path . $result );
				$files = array_merge_recursive( $files, $found );
			} elseif ( ! $extensions || preg_match( '~\.(' . $_extensions . ')$~', $result ) ) {
				$files[ $relative_path . $result ] = $path . '/' . $result;
			}
		}

		return $files;
	}

	/**
	 * Locate template in the theme or plugin if needed
	 *
	 * @param string $tpl : the tpl name, add automatically .php at the end of the file
	 *
	 * @return bool|string
	 */
	public static function locate_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$path = apply_filters( 'BEA/Helpers/locate_template/templates', array( 'views/' . BEA_VC_BD_VIEWS_FOLDER_NAME . '/' . $tpl . '.php' ), $tpl, __NAMESPACE__ );

		// Locate from the theme
		$located = locate_template( $path, false, false );
		if ( ! empty( $located ) ) {
			return $located;
		}

		// Locate on the files
		if ( is_file( BEA_VC_BD_DIR . 'views/' . $tpl . '.php' ) ) {// Use builtin template
			return ( BEA_VC_BD_DIR . 'views/' . $tpl . '.php' );
		}

		return false;
	}

	/**
	 * Include the template given
	 *
	 * @param string $tpl : the template name to load
	 *
	 * @return bool
	 */
	public static function include_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( false === $tpl_path ) {
			return false;
		}

		include( $tpl_path );

		return true;
	}

	/**
	 * Load the template given and return a view to be render
	 *
	 * @param string $tpl : the template name to load
	 *
	 * @return \Closure|false
	 */
	public static function load_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( false === $tpl_path ) {
			return false;
		}

		return function( $data ) use ( $tpl_path ) {
			if ( ! is_array( $data ) ) {
				$data = array( 'data' => $data );
			}
			extract( $data,  EXTR_OVERWRITE );
			include( $tpl_path );
		};
	}

	/**
	 * Render a view
	 *
	 * @param string $tpl : the template's name
	 * @param array  $data : the template's data
	 */
	public static function render( $tpl, $data = array() ) {
		$view = self::load_template( $tpl );
		false !== $view ? $view( $data ) : '';
	}

	/**
	 * Transform a date to a given format if possible
	 *
	 * @param string $date : date to transform
	 * @param $from_format : the from date format
	 * @param $to_format : the format to transform in
	 *
	 * @return string the date formatted
	 */
	public static function format_date( $date, $from_format, $to_format ) {
		$date = \DateTime::createFromFormat( $from_format, $date );
		if ( false == $date ) {
			return '';
		}

		return self::datetime_i18n( $to_format, $date );
	}

	/**
	 * Format on i18n
	 *
	 * @param string $format
	 * @param \DateTime $date
	 *
	 * @return string
	 */
	public static function datetime_i18n( $format, \DateTime $date ) {
		return date_i18n( $format, $date->format( 'U' ) );
	}
}
