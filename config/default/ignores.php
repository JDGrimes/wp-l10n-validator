<?php

/**
 * Default ignores configuration for PHP and HTML.
 *
 * These are default ignores of PHP language contructs and predifined functions, as
 * well as HTMl attributes.
 *
 * Note taht although the include/require constucts are always ignored, they are
 * included in the list below so that function calls within an include statement will
 * be ignored.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

$parser->add_ignored_functions(
	array(
		// Language constructs.
		'elseif'       => true,
		'empty'        => true,
		'for'          => true,
		'foreach'      => true,
		'if'           => true,
		'include'      => true,
		'include_once' => true,
		'isset'        => true,
		'require'      => true,
		'require_once' => true,
		'switch'       => true,
		'unset'        => true,
		'while'        => true,

		// Predefined functions.
		'array_filter' => true,
		'array_map'    => true,
		'compact'      => true,
		'define'       => array( 1 ),
		'defined'      => true,
		'explode'      => true,
		'file_exists'  => true,
		'glob'         => true,
		'implode'      => true,
		'in_array'     => true,
		'ltrim'        => true,
		'preg_replace' => true,
		'sprintf'      => array( 2, 3, 4 ), // More args could be added
		'str_repeat'   => true,
		'str_replace'  => true,
		'uasort'       => true,
		'usort'        => true,
		'version_compare' => true,
	)
);

$parser->add_ignored_atts(
	array(
		'action',
		'autocomplete',
		'class',
		'enctype',
		'for',
		'height',
		'href',
		'http-equiv',
		'id',
		'method',
		'name',
		'onclick',
		'scope',
		'style',
		'type',
		'width',
	)
);

$parser->add_ignored_strings(
	array(
		'/',
		'[',
		']',
		'`',
	)
);
