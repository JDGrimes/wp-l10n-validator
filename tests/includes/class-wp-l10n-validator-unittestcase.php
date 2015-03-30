<?php

/**
 * Add custom text case with custom assertions.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * A test case for the validator, including custom assertions.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_UnitTestCase extends PHPUnit_Framework_TestCase {

	//
	// Private Vars.
	//

	/**
	 * The errors that were found by the parser.
	 *
	 * @see Test_L10n_Validator::$results
	 * @see WP_L10n_Validator_UnitTestCase::setUpBeforeClass()
	 *
	 * @since 0.1.0
	 *
	 * @type array $errors
	 */
	private static $errors;

	/**
	 * The errors the test case expected.
	 *
	 * This array of errors is built up based on the assertions.
	 *
	 * @since 0.1.0
	 *
	 * @type array $expected_errors
	 */
	private static $expected_errors;

	//
	// Protected Vars.
	//

	/**
	 * The file to parse.
	 *
	 * This should be set in the child class.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file;

	/**
	 * The parser statuses returned for any debug tokens found.
	 *
	 * @since 0.1.0
	 *
	 * @type array $debugs
	 */
	protected static $debugs;

	//
	// Public Methods.
	//

	/**
	 * Set up before the class.
	 *
	 * Parses the file that is used in the test, and sets up the $erors member var.
	 *
	 * @since 0.1.0
	 */
	public static function setUpBeforeClass() {

		$parser = new Test_L10n_Validator( WP_L10N_VALIDATOR_DIR . '/tests/data/', 'wp-l10n-validator-tests' );

		$parser->load_config( 'wordpress' );
		$parser->load_config( 'default', array( 'ignores' ) );

		$parser->add_ignored_functions( array( 'ignored_function' => true ) );

		$parser->save_cahce = false;

		static::configure_parser( $parser );

		$parser->parse_file( static::$file );

		self::$errors = $parser->get_results();
		self::$expected_errors = array();
		self::$debugs = $parser->get_debugs();
	}

	/**
	 * Tear down after the class.
	 *
	 * Checks that all unexpected error occurred.
	 *
	 * @since 0.1.0
	 */
	public static function tearDownAfterClass() {

		self::check_for_unexpected_errors();

		if ( ! empty( self::$debugs ) )
			var_dump( self::$debugs );
	}

	/**
	 * Configure the parser.
	 *
	 * @since 0.2.0
	 */
	public static function configure_parser( $parser ) {}

	/**
	 * Mark an error as expected.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $error The error.
	 * @param string $type The type of error.
	 */
	public static function mark_error_expected( $error, $type ) {

		self::$expected_errors[ $type ][] = $error;
	}

	/**
	 * Check that all of the errors were expected.
	 *
	 * @since 0.1.0
	 */
	private static function check_for_unexpected_errors() {

		$unexpected_errors = array();
		if ( self::$errors == self::$expected_errors )
			return;

		foreach ( self::$errors as $type => $errors ) {

			if ( empty( $errors ) )
				continue;

			if ( ! isset( self::$expected_errors[ $type ] ) ) {

				$unexpected_errors[ $type ] = $errors;
				continue;
			}

			if ( $errors == self::$expected_errors[ $type ] )
				continue;

			foreach ( $errors as $error ) {

				if ( array_search( $error, self::$expected_errors[ $type ] ) === false )
					$unexpected_errors[ $type ][] = $error;
			}
		}

		if ( count( $unexpected_errors ) > 0 ) {

			self::fail( "\n\nSome unexpected errors occured: " . print_r( $unexpected_errors, true ) );
		}
	}

	//
	// - Assertions.
	//

	/**
	 * Assert that the parser picked up a particular error.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $error   The error data to check for.
	 * @param string $type    The type of $error.
	 * @param string $message An optional message.
	 *
	 * @throws PHPUnit_Framework_AssertionFailedError
	 */
	public static function assertFoundError( $error, $type, $message = '' ) {

		self::mark_error_expected( $error, $type );

		self::assertThat( $error, self::wasCaughtByParser( $type ), $message );
	}

	/**
	 * Assert that an unexpected textdomain was found.
	 *
	 * @since 0.1.0
	 *
	 * @param string $textdomain The textdomain that was not expected.
	 * @param string $message An optional message.
	 *
	 * @throws PHPUnit_Framework_AssertionFailedError
	 */
	public static function assertFoundUnexpectedTextdomain( $textdomain, $message = '' ) {

		self::assertThat( $textdomain, self::wasCaughtByParser( 'unexpected_textdomain', 'textdomain', 'was an unexpcted textdomain' ), $message );
	}

	/**
	 * Assert that a deprecated l10n function was found.
	 *
	 * @since 0.1.0
	 *
	 * @param string $function The name of the function.
	 * @param string $message An optional message.
	 *
	 * @throws PHPUnit_Framework_AssertionFailedError
	 */
	public static function assertFoundDeprecatedFunction( $function, $message = '' ) {

		self::assertThat( $function, self::wasCaughtByParser( 'deprecated_function', 'function', 'was an unexpcted function' ), $message );
	}

	/**
	 * Assert that a non-gettexted string was found.
	 *
	 * @since 0.1.0
	 *
	 * @param string $string  The string that should have been found.
	 * @param string $message An optional message.
	 *
	 * @throws PHPUnit_Framework_AssertionFailedError
	 */
	public static function assertFoundNonGettextedString( $string, $message = '' ) {

		self::assertThat( $string, self::wasCaughtByParser( 'non_gettext', 'text', 'was a non-gettexted string' ), $message );
	}

	/**
	 * Assert that an invalid l10n arg was found.
	 *
	 * @since 0.1.0
	 *
	 * @param string $arg_number The number of the argument that was invalid.
	 * @param string $function   The function with the invalid arg.
	 * @parma string $line       The line number.
	 * @param string $message    An optional message.
	 *
	 * @throws PHPUnit_Framework_AssertionFailedError
	 */
	public static function assertFoundInvalidL10nArg( $arg_number, $function, $line, $message = '' ) {

		$error = array(
			'line'      => $line,
			'function'  => $function,
			'arg_count' => $arg_number,
		);

		self::mark_error_expected( $error, 'invalid_l10n_arg' );

		self::assertThat( $error, self::wasCaughtByParser( 'invalid_l10n_arg' ), $message );
	}

	//
	// - Conditions.
	//

	/**
	 * Check that an error was caught by the parser.
	 *
	 * @since 0.1.0
	 *
	 * @param string $type      The type of error to check for.
	 * @param string $data      The data key to check for the error data in.
	 * @param string $to_string A string representation of the constraint.
	 *
	 * @return WP_L10n_Validator_PHPUnit_Constraint_WasCaughtByParser
	 */
	public static function wasCaughtByParser( $type, $data = false, $to_string = 'was caught by the parser' ) {

		return new WP_L10n_Validator_PHPUnit_Constraint_WasCaughtByParser( $type, $data, $to_string, self::$errors );
	}

} // class WP_L10n_Validator_UnitTestCase

/**
 * Error was caught by parser constraint.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_PHPUnit_Constraint_WasCaughtByParser extends PHPUnit_Framework_Constraint {

	//
	// Private Vars.
	//

	/**
	 * The type of error to check for.
	 *
	 * @since 0.1.0
	 *
	 * @type string $type
	 */
	private $type;

	/**
	 * The error data key to look in.
	 *
	 * @since 0.1.0
	 *
	 * @type string $data
	 */
	private $data;

	/**
	 * A string representation of the constraint.
	 *
	 * @since 0.1.0
	 *
	 * @type string $to_string
	 */
	private $to_string;

	/**
	 * The errors that the validator threw.
	 *
	 * @since 0.1.0
	 *
	 * @type array $errors
	 */
	private $errors;

	//
	// Public Methods.
	//

	/**
	 * Construct the class with the error type and found errors.
	 *
	 * @since 0.1.0
	 *
	 * @param string $type   The type of error that we'll check for.
	 * @param string $data   The error data key.
	 * @param array  $errors The errors that the parser returned.
	 */
	public function __construct( $type, $data, $to_string, $errors ) {

		$this->type = $type;
		$this->data = $data;
		$this->to_string = $to_string;
		$this->errors = $errors;
	}

	/**
	 * Checks if any of the errors thrown by the parser match the $data.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $data The error data to check for.
	 *
	 * @return bool Whether the error was thrown.
	 */
	public function matches( $data ) {

		if ( ! isset( $this->errors[ $this->type ] ) )
			return false;

		if ( $this->data ) {

			foreach ( $this->errors[ $this->type ] as $error_data ) {

				if ( $error_data[ $this->data ] == $data ) {

					WP_L10n_Validator_UnitTestCase::mark_error_expected( $error_data, $this->type );
					return true;
				}
			}

			return false;

		} else {

			return ( array_search( $data, $this->errors[ $this->type ] ) !== false );
		}
	}

	/**
	 * Returns a string representation of the constraint.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function toString() {

		return $this->to_string;
	}

} // class WP_L10n_Validator_PHPUnit_Constraint_WasCaughtByParser
