<?php

/**
 * L10n WordPress configuration.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

$parser->add_l10n_functions(
	array(
		'__'              => array( 'status' => 'supported', 'textdomain' => 1, 'required' => 0 ),
		'_e'              => array( 'status' => 'supported', 'textdomain' => 1, 'required' => 0 ),
		'_c'              => array( 'status' => 'deprecated', 'textdomain' => 1, 'required' => 0 ),
		'_nc'             => array( 'status' => 'deprecated', 'textdomain' => 3, 'required' => 2 ),
		'__ngettext'      => array( 'status' => 'deprecated', 'textdomain' => 3, 'required' => 2 ),
		'_n'              => array( 'status' => 'supported', 'textdomain' => 3, 'required' => 2 ),
		'__ngettext_noop' => array( 'status' => 'deprecated', 'textdomain' => 2, 'required' => 1 ),
		'_n_noop'         => array( 'status' => 'supported', 'textdomain' => 2, 'required' => 1 ),
		'_x'              => array( 'status' => 'supported', 'textdomain' => 2, 'required' => 1 ),
		'_ex'             => array( 'status' => 'supported', 'textdomain' => 2, 'required' => 1 ),
		'_nx'             => array( 'status' => 'supported', 'textdomain' => 4, 'required' => 3 ),
		'_nx_noop'        => array( 'status' => 'supported', 'textdomain' => 3, 'required' => 2 ),
		'esc_attr__'      => array( 'status' => 'supported', 'textdomain' => 1, 'required' => 0 ),
		'esc_html__'      => array( 'status' => 'supported', 'textdomain' => 1, 'required' => 0 ),
		'esc_attr_e'      => array( 'status' => 'supported', 'textdomain' => 1, 'required' => 0 ),
		'esc_html_e'      => array( 'status' => 'supported', 'textdomain' => 1, 'required' => 0 ),
		'esc_attr_x'      => array( 'status' => 'supported', 'textdomain' => 2, 'required' => 1 ),
		'esc_html_x'      => array( 'status' => 'supported', 'textdomain' => 2, 'required' => 1 ),
	)
);

$parser->add_non_string_l10n_args(
	array(
		2 => array(
			'__ngettext' => true,
			'_n'         => true,
			'_nc'        => true,
			'_nx'        => true,
		),
	)
);
