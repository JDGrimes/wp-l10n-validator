<?php

/**
 * L10n validator specific ignores generator class.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

/**
 * The l10n validator.
 */
include_once __DIR__ . '/wp-l10n-validator.php';

/**
 * Generate a list of instance specific ignores.
 *
 * @since 0.1.0
 */
class WP_L10n_Specific_Ignores_Generator extends WP_L10n_Validator {

	/**
	 * The path to the cache file.
	 *
	 * @since 0.1.0
	 *
	 * @type string $cache_file
	 */
	private static $cache_file;

	/**
	 * Holds the generated list of ignores.
	 *
	 * @since 0.1.0
	 *
	 * @type array $ignores
	 */
	private $ignores = array();

	/**
	 * Cache the specific ignores to a file.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file The full path of the file to write the cache to.
	 *
	 * @return bool Whether the cache was written successfully.
	 */
	public function write_cache( $file ) {

		if ( empty( $file ) )
			$file = $this->basedir . 'wp-l10n-validator-cache.json';

		return parent::save_json_file( $file, $this->ignores );
	}

	/**
	 * Report some non-gettexted text.
	 *
	 * @since 0.1.0
	 *
	 * @param string $text The text of the non-gettexted string.
	 */
	protected function report_non_gettext( $text ) {

		$this->ignores[ $this->filename ][ $text ][ $this->line_number ] = $this->cur_func;
	}

	/**
	 * Report an invalid l10n function argument.
	 *
	 * @since 0.1.0
	 */
	protected function report_invalid_l10n_arg() {}

	/**
	 * Report an unexpected textdomain.
	 *
	 * @since 0.1.0
	 *
	 * @param string $text The unexpected textdomain.
	 */
	protected function report_unexpected_textdomain( $text ) {}

	/**
	 * Report a function that has less than the required arguments.
	 *
	 * @since 0.1.0
	 *
	 * @param int $required_args The number of arguments required by the function.
	 */
	protected function report_required_args( $required_args ) {}

	/**
	 * Report the use of a deprecated l10n function.
	 *
	 * @since 0.1.0
	 *
	 * @param string $function The name of the deprecated function.
	 */
	protected function report_deprecated_l10n_function( $function ) {}

	/**
	 * Callback for debugging.
	 *
	 * This callback is triggered by the debug token.
	 *
	 * @since 0.1.0
	 */
	protected function debug_callback() {}

	//
	// Public Static Methods.
	//

	/**
	 * Run from the CLI.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file The file that should have been called from the CLI.
	 *
	 * @return WP_L10n_Specific_Ignores_Generator|bool The parser instance or false.
	 */
	public static function cli() {

		$parser = parent::cli();
		$parser->write_cache();
		return $parser;
	}

} // class WP_L10n_Specific_Ignores_Generator
