<?php

/**
 * Finds untranslated encapsed strings test case.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * Make sure the validator catches untranslated encapsed strings.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_Untranslated_Encapsed_String_Test extends WP_L10n_Validator_UnitTestCase {

	/**
	 * The file to parse for the test.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file = '/encapsed-strings.php';

	/**
	 * Test that the parser found the non-gettexted encapsed strings.
	 *
	 * @since 0.1.0
	 */
	public function test_nongettexted_encapsed_strings_found() {

		$this->assertFoundNonGettextedString( 'translate me!' );
		$this->assertFoundNonGettextedString( 'I should be translated' );
		$this->assertFoundNonGettextedString( 'l10n is fun.' );
		$this->assertFoundNonGettextedString( 'http is a web protocol' );
		$this->assertFoundNonGettextedString( 'Don\'t' );
		$this->assertFoundNonGettextedString( 'this!' );
		$this->assertFoundNonGettextedString( 'and/or' );
	}
}
