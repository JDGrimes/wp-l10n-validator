<?php

/**
 * An extension of the validator specifically for use with the unit tests.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * A parser for the tests, that stores its results in a member variable.
 *
 * @since 0.1.0
 */
class Test_L10n_Validator extends WP_L10n_Validator {

	//
	// Private Vars.
	//

	/**
	 * The results of the parser.
	 *
	 * @since 0.1.0
	 *
	 * @type array $results {
	 *       The error results of parsing the file(s), if any.
	 *
	 *       @type array $non_gettext           Errors from non-gettexted strings.
	 *       @type array $invalid_l10n_args     Errors from invalid l10n function arguments.
	 *       @type array $unexpected_textdomain Errors from unexpected textdomains.
	 *       @type array $required_args         Errors from missing required function arguments.
	 *       @type array $deprecated_function   Errors from deprecated l10n functions.
	 * }
	 */
	private $results = array(
		'non_gettext'           => array(),
		'invalid_l10n_arg'      => array(),
		'unexpected_textdomain' => array(),
		'required_args'         => array(),
		'deprecated_function'   => array(),
	);

	/**
	 * The debug callback states.
	 *
	 * @since 0.1.0
	 *
	 * @type array $debugs
	 */
	private $debugs = array();

	//
	// Public Methods.
	//

	/**
	 * Retrieve the results of the parser.
	 *
	 * @since 0.1.0
	 *
	 * @return array {@see WP_L10n_Test_Validator::$results The resulting errors}.
	 */
	public function get_results() {

		return $this->results;
	}

	/**
	 * Retrieve the debugs.
	 *
	 * @since 0.1.0
	 *
	 * @return array The parser states for any debug calls.
	 */
	public function get_debugs() {

		return $this->debugs;
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

		$result = array(
			'line' => $this->line_number,
			'text' => $text,
		);

		if ( $this->cur_func ) {

			$result['function']  = $this->cur_func['name'];
			$result['arg_count'] = $this->cur_func['arg_count'] + 1;
		}

		$this->results['non_gettext'][] = $result;
	}

	/**
	 * Report an invalid l10n function argument.
	 *
	 * @since 0.1.0
	 */
	protected function report_invalid_l10n_arg() {

		$this->results['invalid_l10n_arg'][] = array(
			'line'      => $this->line_number,
			'function'  => $this->cur_func['name'],
			'arg_count' => $this->cur_func['arg_count'] + 1,
		);
	}

	/**
	 * Report an unexpected textdomain.
	 *
	 * @since 0.1.0
	 *
	 * @param string $text The unexpected textdomain.
	 */
	protected function report_unexpected_textdomain( $text ) {

		$this->results['unexpected_textdomain'][] = array(
			'line'       => $this->line_number,
			'function'   => $this->cur_func['name'],
			'arg_count'  => $this->cur_func['arg_count'] + 1,
			'textdomain' => $text,
		);
	}

	/**
	 * Report a function that has less than the required arguments.
	 *
	 * @since 0.1.0
	 *
	 * @param int $required_args The number of arguments required by the function.
	 */
	protected function report_required_args( $required_args ) {

		$this->results['required_args'][] = array(
			'line'      => $this->line_number,
			'function'  => $this->cur_func['name'],
			'arg_count' => $this->cur_func['arg_count'] + 1,
			'required'  => $required_args,
		);
	}

	/**
	 * Report the use of a deprecated l10n function.
	 *
	 * @since 0.1.0
	 *
	 * @param string $function The name of the deprecated function.
	 */
	protected function report_deprecated_l10n_function( $function ) {

		$this->results['deprecated_function'][] = array(
			'line'       => $this->line_number,
			'function'   => $this->cur_func['name'],
		);
	}

	/**
	 * Callback for debugging.
	 *
	 * @since 0.1.0
	 */
	protected function debug_callback() {

		$this->debugs[] = $this->get_parser_state();
	}
}
