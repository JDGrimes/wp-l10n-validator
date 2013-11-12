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

	//
	// Private Vars.
	//

	/**
	 * Holds the generated list of ignores.
	 *
	 * @since 0.1.0
	 *
	 * @type array $ignores
	 */
	private $ignores = array();

	//
	// Public Methods.
	//

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

		if ( empty( $file ) ) {

			if ( ! empty( self::$config['ignores-cache'] ) )
				$file = self::resolve_path( self::$config['ignores-cache'] );
			else
				$file = $this->basedir . '/wp-l10n-validator-ignores.cache';
		}

		return parent::save_json_file( $file, $this->ignores );
	}

	//
	// Protected Methods.
	//

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

	/**#@+
	 * Ignore reports for anything other than non-gettexted strings.
	 *
	 * @since 0.1.0
	 */
	protected function report_unexpected_textdomain( $text ) {}
	protected function report_required_args( $required_args ) {}
	protected function report_deprecated_l10n_function( $function ) {}
	protected function debug_callback() {}
	/**#@-*/

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
