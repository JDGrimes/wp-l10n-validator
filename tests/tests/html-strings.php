<?php

/**
 * Finds untranslated HTML strings test case.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

/**
 * Make sure the validator catches untranslated strings in HTML.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator_Untranslated_HTML_String_Test extends WP_L10n_Validator_UnitTestCase {

	/**
	 * The file to parse for the test.
	 *
	 * @since 0.1.0
	 *
	 * @type string $file
	 */
	protected static $file = 'html-strings.php';

	/**
	 * Test that the parser found the non-gettexted HTML strings.
	 *
	 * @since 0.1.0
	 */
	public function test_nongettexted_encapsed_strings_found() {

		$this->assertFoundNonGettextedString( 'Catch me!' );
		$this->assertFoundNonGettextedString( 'bob' );
		$this->assertFoundNonGettextedString( 'catch me' );
	}
}
