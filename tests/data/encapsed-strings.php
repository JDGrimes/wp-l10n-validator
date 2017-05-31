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
$ignore = '_underscore_in_front';
$catch_me = "Don't $ignore this!";
$file = 'file/to/ignore.php';
$path = '/includes';
$other_path = 'src/includes/';
$maybe = 'and/or';

// This was giving an error for the '-' in the first arg, even though the first arg
// was supposed to be ignored.
add_meta_box(
	"{$this->current_points_type}-{$slug}"
	, $ignored->get_title()
	, array( $this, 'display_event_meta_box' )
	, $this->id
	, 'events'
	, 'default'
	, array(
		'points_type' => $this->current_points_type,
		'slug'        => $slug,
	)
);

$class_name = 'Some_Class_To_Ignore';
$constant_name = 'SOMETHING_IGNORED';
