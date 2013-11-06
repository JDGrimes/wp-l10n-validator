<?php

/**
 * Test that all untranslated encapsed strings are caught.
 *
 * At the same time it is used to test that strings are properly ignored.
 *
 * @package WP_L10n_Validator\Tests
 * @since 0.1.0
 */

$unstranslated = 'translate me!';

$untranslated = some_function( 'I should be translated' );

echo 'l10n is fun.';

$ignored = ignored_function( 'ignored string' );

include 'some_file.php';
require_once MY_CONSTANT . '/another_file.php';

switch ( $var ) {

	case 'bob';
	case 'joe':
		do_stuff();
	break;
}

$url = 'http://example.com/';
$not_url = 'http is a web protocol';
$ignore = 'all_lowercase_with_underscores';