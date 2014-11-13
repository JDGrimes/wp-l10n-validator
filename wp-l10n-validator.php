<?php

/**
 * Command line app to parse non-gettexted strings from files.
 *
 * Though written primarily as a CLI app, it may also be used directly. It can
 * validate a single file, or an entire directory (including any and all
 * levels of subdirectories). Only the later option is available from the default
 * CLI usage, although it is possible to extend this as needed. See the README.md
 * file for more information.
 *
 * @link http://plugins.svn.wordpress.org/codestyling-localization/trunk/codestyling-localization.php Revision 778516 (~1.99.30)
 *
 * @package WP_L10n_Validator
 * @version 0.1.2-beta
 * @since   0.1.0
 * @author  J.D. Grimes <jdg@codesymphony.co>
 * @license GPLv2
 */

/**
 * Extensible gettext localization validator for WordPress.
 *
 * This class is where the magic happens. It is a parser that can be used to validate
 * the gettext localization of a PHP project, either via the commandline or from
 * another script. It is also highly customizable, either by setting different
 * options and/or by extending the class.
 *
 * @since 0.1.0
 */
class WP_L10n_Validator {

	//
	// Protected Vars.
	//

	/**
	 * The expected textdomains.
	 *
	 * These domains are indexes, the values are currently ignored.
	 *
	 * @since 0.1.0
	 *
	 * @type array $textdomains
	 */
	protected $textdomains;

	/**
	 * The base dir.
	 *
	 * @since 0.1.0
	 *
	 * @type string $basedir
	 */
	protected $basedir;

	/**
	 * The name of the current file.
	 *
	 * @since 0.1.0
	 *
	 * @type sting $filename
	 */
	protected $filename;

	/**
	 * The current line number.
	 *
	 * @since 0.1.0
	 *
	 * @type int $line_number
	 */
	protected $line_number;

	/**
	 * Info for the current function.
	 *
	 * @since 0.1.0
	 *
	 * @type array {
	 *       @type string $name The name of the function.
	 *       @type string $type The type of function: l10n, ignored, unknown.
	 *       @type bool   $args_started Whether the opening parentheses has been encountered.
	 *       @type int    $arg_count The number of comma's encounted since the args started.
	 *       @type int    $parentheses The number of open parentheses. When this becomes 0, the function call has ended.
	 * }
	 */
	protected $cur_func;

	/**
	 * Whether we are in a function call and the arguments have started.
	 *
	 * @since 0.1.0
	 *
	 * @type bool $args_started
	 */
	protected $args_started;

	/**
	 * The name of the current HTML attribute.
	 *
	 * @since 0.1.2
	 *
	 * @type string $cur_attr
	 */
	protected $cur_attr;

	/**
	 * Whether we are in an include/require(_once) statement.
	 *
	 * @since 0.1.0
	 *
	 * @type bool $in_include
	 */
	protected $in_include;

	/**
	 * Whether we are in a case evaluated expression in a switch statement.
	 *
	 * @since 0.1.0
	 *
	 * @type bool $in_switch_case
	 */
	protected $in_switch_case;

	/**
	 * Whether we have just encountered a new Instance() (T_NEW).
	 *
	 * @since 0.1.0
	 *
	 * @type bool $in_new_class
	 */
	protected $in_new_class;

	/**
	 * The name of the current class (and possibly parent).
	 *
	 * @since 0.1.0
	 *
	 * @type array {
	 *       @type string $self   The name of the class.
	 *       @type string $parent The name the parent of the class (if one).
	 * }
	 */
	protected $in_class;

	/**
	 * Whether we have just encountered a function declaration.
	 *
	 * This has the value 'func_name' if we have just hit the 'function' token, and
	 * should be on the lookout for the function name. Then when the opening curly
	 * bracket of the function body is encountered it is set to 'braces', and will be
	 * set back to false when the closing bracket is found.
	 *
	 * @since 0.1.2
	 *
	 * @type bool|string $in_func_declaration
	 */
	protected $in_func_declaration;

	/**
	 * The function call stack.
	 *
	 * This keeps track of the stack when a function is called within a call to
	 * another function: func_1( func_2() );
	 *
	 * When the inner function is entered, the data for the outer function is pushed
	 * into the stack, and when the inner function is exited, it is pulled back out.
	 *
	 * @see L10n_Validator::$cur_func
	 *
	 * @type array $func_stack
	 */
	protected $func_stack;

	/**
	 * Localization functions.
	 *
	 * @since 0.1.0
	 *
	 * @type array $l10n_functions
	 */
	protected $l10n_functions = array();

	/**
	 * These l10n function arguments are allowed to be non-strings.
	 *
	 * @since 0.1.0
	 *
	 * @type array $non_string_l10n_args
	 */
	protected $non_string_l10n_args = array();

	//
	// Protected Static Vars.
	//

	/**
	 * Whether any errors have been found.
	 *
	 * @since 0.1.2
	 *
	 * @type bool $errors
	 */
	protected static $errors;

	/**
	 * The config callbacks.
	 *
	 * @since 0.1.0
	 *
	 * @type array $config_callbacks
	 */
	protected static $config_callbacks;

	/**
	 * Basic configuration from the JSON config file.
	 *
	 * @since 0.1.0
	 *
	 * @type array $config
	 */
	protected static $config = array(
		'ignores-cache' => 'wp-l10n-validator-ignores.cache',
		'cache'         => 'wp-l10n-validator.cache',
	);

	//
	// Public Vars.
	//

	/**
	 * Functions whose arguments should be ignored.
	 *
	 * The (include|require)(_once)? constructs are always ignored, and that cannot
	 * be overridden here.
	 *
	 * @since 0.1.0
	 *
	 * @type array $ignored_functions
	 */
	public $ignored_functions = array();

	/**
	 * HTML attributes whose values to ignore.
	 *
	 * @since 0.1.0
	 *
	 * @type array $ignored_atts
	 */
	public $ignored_atts = array();

	/**
	 * Strings to ignore universally.
	 *
	 * @since 0.1.0
	 *
	 * @type array $ignored_strings
	 */
	public $ignored_strings = array();

	/**
	 * Specific string occurances to ignore.
	 *
	 * @since 0.1.0
	 *
	 * @type array $ignored_string_occurences
	 */
	public $ignored_string_occurences = array();

	/**
	 * Specific string occurance ignores line number tolerance.
	 *
	 * @since 0.1.0
	 *
	 * @type int $ignores_tolerance
	 */
	public $ignores_tolerance = 5;

	/**
	 * The debug marker.
	 *
	 * When this token is encountered, the debug_callback() method is called.
	 *
	 * @since 0.1.0
	 *
	 * @type string $debug_marker
	 */
	public $debug_marker = '_debug_';

	/**
	 * The full path to the cache file.
	 *
	 * @since 0.1.0
	 *
	 * @type string $cache_file
	 */
	public $cache_file = '';

	/**
	 * The cached results.
	 *
	 * @since 0.1.0
	 *
	 * @type array $cache
	 */
	public $cache = array();

	/**
	 * Whether to save the cache after parsing.
	 *
	 * @since 0.1.0
	 *
	 * @type bool $save_cache
	 */
	public $save_cache = true;

	/**
	 * Whether to run the files one by one.
	 *
	 * This is useful for large projects, especially for the first run. When there is
	 * a cache, it will run until it reaches the first changed file, then stop.
	 *
	 * @since 0.1.0
	 *
	 * @type bool $one_by_one
	 */
	public $one_by_one = false;

	//
	// Public Methods.
	//

	/**
	 * Construct the parser.
	 *
	 * @since 0.1.0
	 *
	 * @param string       $basedir    The base dir.
	 * @param string|array $textdomain A text domain or an array of textdomains. If
	 *                                 an array, the domains should be the keys.
	 */
	public function __construct( $basedir, $textdomain ) {

		$this->basedir = realpath( $basedir );

		if ( is_array( $textdomain ) ) {

			$this->textdomains = $textdomain;

		} else {

			// Array values currently ignored.
			$this->textdomains = array(
				$textdomain => true,
			);
		}

		$this->cache_file = self::resolve_path( self::$config['cache'] );

		$ignores = self::load_json_file( self::resolve_path( self::$config['ignores-cache'] ) );

		if ( $ignores )
			$this->ignored_string_occurences += $ignores;

		foreach ( (array) self::$config_callbacks as $callback ) {

			call_user_func( $callback, $this );
		}

		$this->load_cache();
	}

	/**
	 * Load the configuration for the parser.
	 *
	 * @since 0.1.0
	 *
	 * @param string $type  The configuration type, e.g., 'php'.
	 * @param array  $parts The part(s) of the configuration to load. Possible values
	 *        are 'ignores' and 'l10n'. Both parts are loaded by default.
	 */
	public function load_config( $type, $parts = array( 'l10n', 'ignores' ) ) {

		$parser = $this;

		foreach ( $parts as $part ) {

			include __DIR__ . "/config/{$type}/{$part}.php";
		}
	}

	/**
	 * Load any cache from the cache file.
	 *
	 * @since 0.1.0
	 */
	public function load_cache() {

		if ( empty( $this->cache_file ) )
			$this->cache_file = $this->basedir . '/wp-l10n-validator.cache';

		$cache = self::load_json_file( $this->cache_file );

		if ( $cache )
			$this->cache = $cache;
	}

	/**
	 * Save the cache.
	 *
	 * @since 0.1.0
	 */
	public function save_cache() {

		self::save_json_file( $this->cache_file, $this->cache );

		if ( ! empty( $this->ignored_string_occurences ) )
			self::save_json_file( $this->resolve_path( self::$config['ignores-cache'] ), $this->ignored_string_occurences );
	}

	/**
	 * Add to the list of l10n functions.
	 *
	 * The function names should be keys, and the values arrays of function data, as
	 * follows:
	 *
	 * @struct array {
	 *         @type string $status     'supported, or 'deprecated' if the function
	 *               should no longer be used.
	 *         @type int    $textdomain The argument number of the textdomain, minus one.
	 *         @type int    $required   The number of required arguments, minus one.
	 * }
	 *
	 * @since 0.1.0
	 *
	 * @param array $functions The functions.
	 */
	public function add_l10n_functions( $functions ) {

		$this->l10n_functions += $functions;
	}

	/**
	 * Add to the list of l10n function arguments where non-string vars are allowed.
	 *
	 * @since 0.1.0
	 *
	 * @param
	 */
	public function add_non_string_l10n_args( $args ) {

		$this->non_string_l10n_args += $args;
	}

	/**
	 * Add to the list of functions to ignore.
	 *
	 * @since 0.1.0
	 *
	 * @param array $functions The names of the functions to add.
	 */
	public function add_ignored_functions( array $functions ) {

		$this->ignored_functions += $functions;
	}

	/**
	 * Update the list of functions to ignore.
	 *
	 * @since 0.2.0
	 *
	 * @param array $functions The names of the functions to add/update.
	 */
	public function update_ignored_functions( array $functions ) {

		$this->ignored_functions = array_merge(
			$this->ignored_functions
			, $functions
		);
	}

	/**
	 * Remove functions from the ignored list.
	 *
	 * @since 0.1.0
	 *
	 * @param array $functions The names of the functions to remove.
	 */
	public function remove_ignored_functions( array $functions ) {

		$this->ignored_functions = array_diff( $this->ignored_functions, array_flip( $functions ) );
	}

	/**
	 * Add to the list of ignored HTML attributes.
	 *
	 * @since 0.1.0
	 *
	 * @param array $atts An array of attribute names.
	 */
	public function add_ignored_atts( array $atts ) {

		$this->ignored_atts += $atts;
	}

	/**
	 * Update the list of ignored HTML attributes.
	 *
	 * @since 0.2.0
	 *
	 * @param array $atts An array of attribute names.
	 */
	public function update_ignored_atts( array $atts ) {

		$this->ignored_atts = array_merge( $this->ignored_atts, $atts );
	}

	/**
	 * Add to the list of ignored strings.
	 *
	 * @since 0.1.0
	 *
	 * @param array|string $strings The strings to ignore.
	 */
	public function add_ignored_strings( $strings ) {

		$this->ignored_strings += array_flip( (array) $strings );
	}

	/**
	 * Update the list of ignored strings.
	 *
	 * @since 0.2.0
	 *
	 * @param array|string $strings The strings to ignore.
	 */
	public function update_ignored_strings( $strings ) {

		$this->ignored_strings = array_merge(
			$this->ignored_strings
			, array_flip( (array) $strings )
		);
	}

	/**
	 * Parse the project.
	 *
	 * Parses all .php files in the project's base directory and any subdirectories.
	 *
	 * @since 0.1.0
	 */
	public function parse() {

		$base_length = strlen( $this->basedir );

		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->basedir ) ) as $filename ) {

			if ( $filename->getExtension() != 'php' )
				continue;

			$this->filename = substr( $filename, $base_length );

			if ( $this->is_ignored_file( $this->filename ) ) {
				continue;
			}

			 if ( $this->_parse_file() && $this->one_by_one )
			 	break;
		}

		if ( $this->save_cache )
			$this->save_cache();
	}

	/**
	 * Check if a file is supposed to be ignored.
	 *
	 * @since 0.2.0
	 *
	 * @param string $file The path to the file relative to the project root.
	 *
	 * @return bool True if the file is ignored, otherwise false.
	 */
	public function is_ignored_file( $file ) {

		if (
			! isset( self::$config['ignored-paths'] )
			|| ! is_array( self::$config['ignored-paths'] )
		) {
			return false;
		}

		foreach ( self::$config['ignored-paths'] as $path ) {

			if ( substr( $file, 0, strlen( $path ) ) === $path ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Parse a single file within a project.
	 *
	 * @since 0.1.0
	 *
	 * @param string $filename The name of the file to parse, relative to the basedir.
	 *
	 * @return bool|array False if the file doesn't exist, or an array.
	 */
	public function parse_file( $filename ) {

		$full_path = $this->basedir . $filename;

		if ( ! file_exists( $full_path ) ) {

			static::error( "The file {$full_path} does not exist." );
			return false;
		}

		$this->filename = $filename;

		return $this->_parse_file();
	}

	/**
	 * Get the current state of the parser.
	 *
	 * Useful in debugging.
	 *
	 * @since 0.1.0
	 *
	 * @return array {
	 *         The current state of the parser.
	 *
	 *        @type string $filename The name of the file currently being parsed.
	 *        @type int    $line     The line of the file the parser is on.
	 *        @type array|bool $cur_func {
	 *              Info about the current function, or false if not currently in a function.
	 *
	 *              @type string $name         The name of the current function
	 *              @type string $type         The type of the function.
	 *              @type bool   $args_started Whether we have encountered the opening parenthesis.
	 *              @type int    $args_count   The number of arguments we have encountered.
	 *              @type int    $parentheses  The number of open parenthesis.
	 *        }
	 *        @type array $func_stack If we are inside of multiple functions, the data for the outer ones as above.
	 * }
	 */
	public function get_parser_state() {

		return array(
			'filename'            => $this->filename,
			'line'                => $this->line_number,
			'cur_func'            => $this->cur_func,
			'cur_attr'            => $this->cur_attr,
			'func_stack'          => $this->func_stack,
			'in_include'          => $this->in_include,
			'in_switch_case'      => $this->in_switch_case,
			'in_new_class'        => $this->in_new_class,
			'in_class'            => $this->in_class,
			'in_func_declaration' => $this->in_func_declaration,
		);
	}

	/**
	 * Check if there were any errors.
	 *
	 * @since 0.2.0
	 *
	 * @return bool True if there were errors, false otherwise.
	 */
	public function had_errors() {

		return self::$errors;
	}

	//
	// Public Static Methods.
	//

	/**
	 * Register a config function.
	 *
	 * @since 0.1.0
	 *
	 * @param string $function The function name.
	 */
	public static function register_config_callback( $function ) {

		self::$config_callbacks[] = $function;
	}

	//
	// Private Methods.
	//

	/**
	 * Parse the current a file.
	 *
	 * @since 0.1.0
	 */
	private function _parse_file() {

		self::$errors = false;
		$check_hash = true;
		$file = $this->basedir . $this->filename;

		$file_size = @filesize( $file );

		if ( $file_size ) {
			if ( isset( $this->cache[ $this->filename ]['size'] ) && $file_size != $this->cache[ $this->filename ]['size'] )
				$check_hash = false;

			$this->cache[ $this->filename ]['size'] = $file_size;
		}

		$content = file_get_contents( $file );

		if ( $content === false ) {
			static::error( "The contents of the file {$this->filename} could not be retrieved." );
			return false;
		}

		$checksum = hash( 'md5', $content );

		/*
		 * If we need to check the hash, and we find that the old hash is cached and
		 * is the same as the current hash, and the file didn't have any errors
		 * before, then we skip it.
		 */
		if (
			$check_hash
			&& isset( $this->cache[ $this->filename ]['hash'] )
			&& $checksum == $this->cache[ $this->filename ]['hash']
			&& isset( $this->cache[ $this->filename ]['errors'] )
			&& ! $this->cache[ $this->filename ]['errors']
		) {
			return false;
		}

		$this->cache[ $this->filename ]['hash'] = $checksum;

		$this->_parse_string( $content );

		$this->cache[ $this->filename ]['errors'] = self::$errors;

		return true;
	}

	/**
	 * Parse a string.
	 *
	 * It attempts to do the following:
	 * * Find untranslated strings in HTML.
	 * * Find encapsed stringes that aren't being translated.
	 * * Make sure all l10n function arguments are valid -
	 * * * No variables where there should be a string.
	 * * * No variables within strings.
	 * * * etc.
	 * * etc.
	 *
	 * @since 0.1.0
	 *
	 * @param string $content The string to parse.
	 */
	private function _parse_string( $content ) {

		if ( ! $content )
			return;

		// Reset the parser.
		$this->args_started        = false;
		$this->cur_func            = false;
		$this->cur_attr            = false;
		$this->in_include          = false;
		$this->in_switch_case      = false;
		$this->in_new_class        = false;
		$this->in_class            = false;
		$this->in_func_declaration = false;
		$this->line_number         = 1;
		$this->func_stack          = array();

		// The number of open brackets ([) and braces ({).
		$brackets = 0;
		$braces   = 0;

		$func_dec_braces = false;

		$in_extends = false;

		$tokens = token_get_all( $content );

		foreach ( $tokens as $index => $token ) {

			if ( is_array( $token ) ) {

				list( $id, $text ) = $token;

				switch ( $id ) {

					// The token is a string - it may be a function name.
					case T_STRING:
						// Find out what this string would look like if it were a function.
						$full_function = $text;

						if ( $this->debug_marker == $full_function ) {

							$this->debug_callback();
							break;
						}

						while ( isset( $tokens[ $index - 1 ][1] ) ) {

							/*
							 * Attempt to get a full method name in OO code.
							 */
							switch ( $tokens[ $index - 1 ][1] ) {

								case '::':
								case '->':
									if ( isset( $tokens[ $index - 2 ][1] ) ) {
										switch ( $tokens[ $index - 2 ][1] ) {

											case 'self':
											case '$this':
											case 'static':
												if ( isset( $this->in_class['self'] ) ) {

													$full_function = $this->in_class['self'] . '::' . $full_function;
													break;
												}
											// fallthru

											case 'parent':
												if ( isset( $this->in_class['parent'] ) ) {

													$full_function = $this->in_class['parent'] . '::' . $full_function;
													break;
												}
											// fallthru

											default:
												$full_function = $tokens[ $index - 2 ][1] . $tokens[ $index - 1 ][1] . $full_function;
										}

									} else {

										$full_function = '(unknown)' . $tokens[ $index - 1 ][1] . $full_function;
									}

									$index -= 2;
								break;

								default: break 2;
							}
						}

						if ( $this->in_new_class ) {

							$full_function .= '::__construct';
							$this->in_new_class = false;

						} elseif ( true === $this->in_class ) {

							$this->in_class = array( 'self' => $full_function );
							break;

						} elseif ( $this->in_class && 'func_name' == $this->in_func_declaration ) {

							$full_function = $this->in_class['self'] . '::' . $full_function;

						} elseif ( $in_extends ) {

							$this->in_class['parent'] = $full_function;
							$in_extends = false;
							break;
						}

						if ( isset( $this->l10n_functions[ $full_function ] ) ) {

							// This is a l10n function.
							$this->_enter_function( $full_function, 'l10n' );

							// Give an error for deprecated l10n functions.
							if ( 'deprecated' == $this->l10n_functions[ $full_function ]['status'] )
								$this->report_deprecated_l10n_function( $full_function );

						} elseif ( isset( $this->ignored_functions[ $full_function ] ) && true === $this->ignored_functions[ $full_function ] ) {

							// We are entering a function that we want to ignore.
							$this->_enter_function( $full_function, 'ignored' );

						} else {

							// We don't know what this is. It *might* be a function.
							$this->_enter_function( $full_function, 'unknown' );
						}
					break; // T_STRING

					// We're encoutering a language construct that we'll likely treat as an ignored function.
					case T_ARRAY:
					case T_ELSEIF:
					case T_EMPTY:
					case T_FOR:
					case T_FOREACH:
					case T_IF:
					case T_ISSET:
					case T_SWITCH:
					case T_UNSET:
					case T_WHILE:
						$type = 'unknown';

						if ( isset( $this->ignored_functions[ $text ] ) )
							$type = 'ignored';

						$this->_enter_function( $text, $type );
					break;

					// We're encountering an include or require, which we will ignore.
					case T_INCLUDE:
					case T_INCLUDE_ONCE:
					case T_REQUIRE:
					case T_REQUIRE_ONCE:
						$this->_enter_function( $text, 'include' );
					break;

					// We're encountering a switch case.
					case T_CASE:
						$this->_enter_function( $text, 'case' );
					break;

					// This token is an encapsed string. We'll want to make sure it's translatable.
					case T_ENCAPSED_AND_WHITESPACE:
						/*
						 * Add one character of whitespace to each side so we won't
						 * mutilate the string when we trim it below.
						 */
						$text = " {$text} ";
					// fallthru

					case T_CONSTANT_ENCAPSED_STRING:
						if ( $this->cur_func && $this->cur_func['type'] == 'l10n' ) {

							// Check if this is a textdomain.
							if ( $this->l10n_functions[ $this->cur_func['name'] ]['textdomain'] == $this->cur_func['arg_count'] ) {

								$textdomain = substr( $text, 1, strlen( $text ) - 2 );

								// Validate the textdomain.
								if ( ! isset( $this->textdomains[ $textdomain ] ) )
									$this->report_unexpected_textdomain( $textdomain );
							}

						} elseif (
							(
									! $this->cur_func
								||
									$this->cur_func['type'] != 'ignored'
								&& (
										! isset( $this->ignored_functions[ $this->cur_func['name'] ][0] )
									||
										! in_array( $this->cur_func['arg_count'] + 1, $this->ignored_functions[ $this->cur_func['name'] ] )
								)
							) && ! (
									 $brackets
								|| (
										isset( $tokens[ $index + 2 ][0] )
									&&
										T_DOUBLE_ARROW == $tokens[ $index + 2 ][0]
								)
							)
						) {
							/*
							 * We aren't inside a function, or at least not in one
							 * that we're supposed to be ignoring, and we aren't
							 * supposed to ignore this particular function argument,
							 * AND, this isn't an array key. I.e., listen up!
							 */

							// Remove surrounding quotes, prepare for logging.
							$non_gettext = $this->prepare_non_gettext( substr( $text, 1, strlen( $text ) - 2 ) );

							if ( $non_gettext )
								$this->report_non_gettext( $non_gettext );
						}
					break; // T_CONSTANT_ENCAPSED_STRING

					// Check for non-gettexted strings in HTML.
					case T_INLINE_HTML:
						$non_gettext = $this->prepare_non_gettext( $text );

						if ( $non_gettext )
							$this->report_non_gettext( $non_gettext );
					break;

					case T_NEW:
						$this->in_new_class = true;
					break;

					case T_CLASS:
						$this->in_class = true;
					break;

					case T_FUNCTION:
						if ( $this->in_func_declaration != 'braces' ) {
							$this->in_func_declaration = 'func_name';
						}
					break;

					case T_EXTENDS:
						$in_extends = true;
					break;

					/*
					 * These tokens will be accompanied by closing curly braces, but
					 * we won't catch the opening brace below (becuase it is part of
					 * these tokens) so we'll catch it here. Otherwise the parser
					 * will think that the class declaration has ended before it has.
					 */
					case T_CURLY_OPEN:
					case T_DOLLAR_OPEN_CURLY_BRACES:
						if ( $this->in_class ) {
							$braces++;
						}
					break;

					case T_WHITESPACE:
					case T_COMMENT:
					case T_DOC_COMMENT: break;

					default:
						if ( $this->cur_func && $this->cur_func['type'] == 'l10n' && ! isset( $this->non_string_l10n_args[ $this->cur_func['arg_count'] ][ $this->cur_func['name'] ] ) ) {

							// This is a l10n function argument, and it should be just a string.
							$this->report_invalid_l10n_arg();
						}

				} // switch ( $id )

				$token = $text;

			} else {

				// The token wasn't an array.

				switch ( $token ) {

					case '(':
						if ( $this->cur_func ) {
							$this->cur_func['parentheses']++;
							$this->cur_func['args_started'] = true;

							$this->args_started = true; // We didn't start them, though.
						}

						$this->in_new_class = false; // In case of a $variable class instantiation.
					break;

					case ',':
						// Exit the current function if its arguments haven't started.
						$this->_exit_function( true );

						if ( $this->args_started )
							$this->cur_func['arg_count']++;
					break;

					case ')':
						// Exit the current function if its arguments haven't started.
						$this->_exit_function( true );

						if ( ! $this->cur_func )
							break;

						$this->cur_func['parentheses']--;

						// If the parentheses have cancled out, the function has ended.
						if ( $this->cur_func['parentheses'] == 0 ) {

							if ( $this->cur_func && $this->cur_func['type'] == 'l10n' ) {

								$required_args = $this->l10n_functions[ $this->cur_func['name'] ]['required'];

								if ( $this->cur_func['arg_count'] < $required_args ) {

									$this->report_required_args( $required_args + 1 );
								}

								if (
									$this->cur_func['arg_count'] < $this->l10n_functions[ $this->cur_func['name'] ]['textdomain']
									&& ! isset( $this->textdomains[''] )
								) {
									$this->report_unexpected_textdomain( '' );
								}
							}

							$this->_exit_function();
						}
					break;

					// Keep track of brackets so we can ignore sting array keys.
					case '[':
						$brackets++;
					break;

					case ']':
						$brackets--;
					break;

					// Keep track of curly braces when we are in a class declaration.
					case '{':
						if ( $this->in_class ) {

							if ( 'func_name' == $this->in_func_declaration ) {
								$func_dec_braces = $braces;
								$this->in_func_declaration = 'braces';
							}

							$braces++;

						} else {

							if ( 'func_name' === $this->in_func_declaration ) {
								$this->in_func_declaration = false;
							}
						}
					break;

					case '}':
						if ( $this->in_class ) {

							if ( 0 == --$braces ) {
								$this->in_class = false;
							}

							if ( 'braces' == $this->in_func_declaration && $braces === $func_dec_braces ) {
								$this->in_func_declaration = $func_dec_braces = false;
							}
						}
					break;

					case ';':
						$this->_exit_function();
						$this->in_include = false;
					// fallthru

					case ':':
						if ( $this->in_switch_case ) {

							$this->_exit_function();
							$this->in_switch_case = false;
						}
					break;

					default:
						if ( $this->cur_func && $this->cur_func['type'] == 'l10n' && ! isset( $this->non_string_l10n_args[ $this->cur_func['arg_count'] ][ $this->cur_func['name'] ] ) ) {

							// This is a l10n function argument, and it should be just a string.
							$this->report_invalid_l10n_arg();
						}

				} // switch ( $token )

			} // if ( is_array( $token ) ) } else {

			$this->line_number += substr_count( $token, "\n" );

		} // foreach ( $tokens as $token )

	} // function _parse_string

	/**
	 * Enter a function.
	 *
	 * @since 0.1.0
	 *
	 * @param string $function The name of the function.
	 * @param string $type     The type of function.
	 */
	private function _enter_function( $function, $type ) {

		$this->args_started = false;

		switch ( $type ) {

			case 'include':
				$type = 'ignored';
				$this->args_started = true;
				$this->in_include = true;
			break;

			case 'case':
				$type = 'ignored';
				$this->args_started = true;
				$this->in_switch_case = true;
			break;
		}

		// Ignore child class methods based on parent class.
		if (
				$type != 'ignored'
			&&
				! empty( $this->in_class['parent'] )
			&&
			 	strpos( $function, "{$this->in_class['self']}::" ) == 0
			&&
				isset(
					$this->ignored_functions[
						str_replace(
							"{$this->in_class['self']}::"
							, "{$this->in_class['parent']}::"
							, $function
						)
					]
				)
		) {
			$type = 'ignored';
		}

		if ( $this->cur_func ) {

			if ( $this->cur_func['type'] == 'l10n' && ! isset( $this->non_string_l10n_args[ $this->cur_func['arg_count'] ][ $this->cur_func['name'] ] ) ) {

				/*
				 * We are in an l10n function, and this argument is not allowed to be
				 * a non-string. Ignore the inner function call and just report the
				 * argument as invalid.
				 */
				$this->report_invalid_l10n_arg();
				return;
			}

			if ( $this->cur_func['args_started'] ) {

				/*
				 * If we are currently in a function, add it to the stack. If the
				 * arguments hadn't started yet, then it wasn't a real funtion
				 * call, so we don't need to add it to the stack.
				 */
				$this->func_stack[] = $this->cur_func;

				// Check if we are supposed to ignore immediate children function calls.
				if (
						$type != 'ignored'
					&&
						'ignored' == $this->cur_func['type']
					&&
						! empty( $this->ignored_functions[ $this->cur_func['name'] ] )
					&&
						true === $this->ignored_functions[ $this->cur_func['name'] ]
				) {
					$type = 'ignored';
				}
			}
		}

		// Set up for the new function.
		$this->cur_func = array(
			'name'         => $function,
			'type'         => $type,
			'args_started' => $this->args_started,
			'arg_count'    => 0,
			'parentheses'  => 0,
		);

	} // function _enter_function()

	/**
	 * Exit the current function.
	 *
	 * This function can either exit only if the current function's arguments haven't
	 * started, or also the first function whose arguments have started.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $only_unstarted Whether to only exit the function if its arguments
	 *        haven't started.
	 *
	 * @return void
	 */
	private function _exit_function( $only_unstarted = false ) {

		if ( ! $this->args_started && 'recursive' !== $only_unstarted ) {

			// Move up the stack.
			$this->_exit_function( 'recursive' );

			if ( $only_unstarted || ! $this->cur_func )
				return;

		} elseif ( $only_unstarted === true ) {

			return;
		}

		// Move up the stack one level.
		$this->cur_func = array_pop( $this->func_stack );

		if ( ! $this->cur_func ) {

			$this->cur_func     = false;
			$this->args_started = false;

		} else {

			$this->args_started = true;
		}
	}

	/**
	 * Prepares a non-gettexted string to pass to the logging function.
	 *
	 * @since 0.1.0
	 *
	 * @param string $text The text of the string.
	 *
	 * @return string|bool The text to log, or false if the string should be ignored.
	 */
	private function prepare_non_gettext( $text ) {

		$exited = false;

		if ( $this->cur_func && ! $this->args_started ) {

			// If the arguments hadn't started, this wasn't a real function.
			$this->_exit_function( true );

			$exited = true;
		}

		// If we've exited the original "function", make sure this one isn't ignored or l10n.
		if ( $exited && $this->cur_func && ( $this->cur_func['type'] == 'ignored' || $this->cur_func['type'] == 'l10n' ) ) {

			return false;
		}

		if ( strpos( $text, '<' ) !== false ) {

			/*
			 * There may be HTML in this.
			 *
			 * First we will search for any attrributes whose values should be
			 * gettexted, then we rip all of the attributes out.
			 */

			// Remove style/script tags and contents first.
			$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );

			/*
			 * Really ugly REGEX here. Yeah, I know, we probably shouldn't be trying
			 * to parse HTML with REGEXs. An alternative, would be to parse the file
			 * as HTML in addition to parsing the PHP tokens. However, for some odd
			 * reason, it seems like lots of XML/HTML parsers don't like invalid XML,
			 * and we really almost expect lots of stuff that might not be valid,
			 * simply because of the way it is output by/mixed with the code. Also,
			 * we would still have to do something here, because we will encounter
			 * HTML fragments in encapsed strings. In short, "patches welcome" ;-).
			 *
			 * OK, so what does this actually (try to) do? It parses out all HTML
			 * element attribute-like strings, and then we loop through all of the
			 * ones that we aren't just supposed to ignore and report the values as
			 * non-gettexed strings, if they aren't empty.
			 */
			preg_match_all( '~([a-zA-z-]+)=(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')~', $text, $html_atts );

			$attr_names = array_diff( $html_atts[1], $this->ignored_atts );

			if ( reset( $attr_names ) ) {

				$keep_looping = true;

				while ( $keep_looping ) {

					$key = key( $attr_names );

					$attr_value = $html_atts[2][ $key ] . $html_atts[3][ $key ];

					if ( ! empty( $attr_value ) && ( $attr_value = $this->prepare_non_gettext( $attr_value ) ) ) {

						$this->cur_attr = $attr_names[ $key ];
						$this->report_non_gettext( $attr_value );
					}

					$keep_looping = next( $attr_names );
				}

				$this->cur_attr = false;
			}

			/*
			 * Now lets try to rip all of the HTML elements out and see if there is
			 * anything meaningfull left.
			 */
			$text = preg_replace( '~</?[a-zA-z]+[^>]*/?>?~', '', $text );
		}

		$text = trim( $text );

		/*
		 * Clean out escaped whitespace, loose self-closing tag ends, and quotes
		 * (they may belong to some HTML). If that leaves us an empty string, then we
		 * can safely ignore this.
		 */
		if ( trim( str_replace( array( '\n', '\r', '/>', '"', '\'', '>' ), '', $text ) ) === '' )
			return false;

		// Filter out bits of interspersed HTML like " id=", <a href=", " class="">, or " alt="" />.
		switch ( $text[0] ) {

			case '<':
			case '"':
			case "'":
				switch ( substr( $text, -2, 2 ) ) {

					case '="':
					case "='":
					case '">':
					case "'>":
					case '/>':
						return false;
				}
			break;
		}

		if ( ! strpos( $text, ' ' ) ) {

			// Filter out all-lowercase strings with an underscore in them: ignore_this
			if ( strpos( $text, '_' ) !== false && strtolower( $text ) === $text )
				return false;

			// Filter out URLs.
			if ( strpos( $text, 'http' ) === 0 && strpos( $text, '/' ) )
				return false;

			// Filter out file paths.
			if ( '/' === $text{0} || '.php' === substr( $text, -4 ) || '/' === substr( $text, -1 ) ) {
				return false;
			}
		}

		if ( isset( $this->ignored_strings[ $text ] ) )
			return false;

		if ( isset( $this->ignored_string_occurences[ $this->filename ][ $text ] ) ) {

			foreach ( $this->ignored_string_occurences[ $this->filename ][ $text ] as $line => $cur_func ) {

				if (
					$line + $this->ignores_tolerance > $this->line_number
					&& $line - $this->ignores_tolerance < $this->line_number
					&& $this->cur_func == $cur_func
				) {

					if ( $line != $this->line_number ) {

						$this->ignored_string_occurences[ $text ][ $this->line_number ] = $cur_func;
						unset( $this->ignored_string_occurences[ $text ][ $line ] );
					}

					return false;
				}
			}
		}

		return $text;

	} // private function prepare_non_gettext()

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

		$extra = '';

		if ( $this->cur_func ) {

			$extra = " {$this->cur_func['name']}( " . ( $this->cur_func['arg_count'] + 1 ) . " )";

			foreach ( $this->func_stack as $func ) {

				$extra = " {$func['name']}( " . ( $func['arg_count'] + 1 ) . "{$extra} )";
			}

		} elseif ( $this->cur_attr ) {

			$extra = " {$this->cur_attr}=''";
		}

		$this->error( "{$this->filename}#{$this->line_number}{$extra}: Non gettexted string '{$text}'" );
	}

	/**
	 * Report an invalid l10n function argument.
	 *
	 * @since 0.1.0
	 */
	protected function report_invalid_l10n_arg() {

		$this->error( "{$this->filename}#{$this->line_number} {$this->cur_func['name']}( " . ( $this->cur_func['arg_count'] + 1 ) . " ): Invalid l10n function argument." );
	}

	/**
	 * Report an unexpected textdomain.
	 *
	 * @since 0.1.0
	 *
	 * @param string $text The unexpected textdomain.
	 */
	protected function report_unexpected_textdomain( $text ) {

		$this->error( "{$this->filename}#{$this->line_number} {$this->cur_func['name']}( " . ( $this->cur_func['arg_count'] + 1 ) . " ): Unexpected textdomain: '{$text}'." );
	}

	/**
	 * Report a function that has less than the required arguments.
	 *
	 * @since 0.1.0
	 *
	 * @param int $required_args The number of arguments required by the function.
	 */
	protected function report_required_args( $required_args ) {

		$this->error( "{$this->filename}#{$this->line_number}: {$this->cur_func['name']}() requires {$required_args} arguments, only " . ( $this->cur_func['arg_count'] + 1 ) . " given." );
	}

	/**
	 * Report the use of a deprecated l10n function.
	 *
	 * @since 0.1.0
	 *
	 * @param string $function The name of the deprecated function.
	 */
	protected function report_deprecated_l10n_function( $function ) {

		$this->error( "{$this->filename}#{$this->line_number}: {$this->cur_func['name']}() is deprecated! Please use its replacement instead." );
	}

	/**
	 * Callback for debugging.
	 *
	 * This callback is triggered by the debug token.
	 *
	 * @since 0.1.0
	 */
	protected function debug_callback() {

		$func_text = '';
		$func_stack = '';

		if ( $this->cur_func ) {

			$func_text = " {$this->cur_func['name']}( " . ( $this->cur_func['arg_count'] + 1 ) . " )";

			if ( count( $this->func_stack ) ) {

				$func_stack = "\n\t Function call stack:";
				$i = 0;

				foreach ( $this->func_stack as $func ) {

					$i++;
					$func_stack .= "\n\t\t {$i}. {$func['name']}( " . ( $func['arg_count'] + 1 ) . " )";
				}
			}
		}

		$in_class = '';

		if ( $this->in_class ) {

			$in_class .= "\n\t In class: ";

			if ( isset( $this->in_class['self'] ) ) {

				$in_class .= $this->in_class['self'];
				$in_class .= ( isset( $this->in_class['parent'] ) ) ? '(parent: ' . $this->in_class['parent'] . ')' : '';

			} else {

				$in_class .= '-entering-';
			}
		}

		$this->error(
			"{$this->filename}#{$this->line_number}{$func_text}: Debug token found."
			. "\n\t In include: " . ( $this->in_include ? 'yes' : 'no' )
			. "\n\t In switch case: " . ( $this->in_switch_case ? 'yes' : 'no' )
			. "\n\t In new class: " . ( $this->in_new_class ? 'yes' : 'no' )
			. "\n\t In function declaration: " . ( $this->in_func_declaration ? $this->in_func_declaration : 'no' )
			. $in_class
			. $func_stack
		);
	}

	//
	// Public Static Methods.
	//

	/**
	 * Make sure a path is absolute.
	 *
	 * @since 0.1.0
	 *
	 * @param string $path     The path to make absolute.
	 * @param string $relative The path that $path is relative to if it is relative.
	 *                         If empty, the current working directory is used.
	 *
	 * @return string An absolute path.
	 */
	protected static function resolve_path( $path, $relative = false ) {

		if ( strpos( $path, './' ) === 0 ) {

			if ( ! $relative && ! ( $relative = getcwd() ) ) {

				static::error( 'Unable to get current working directory.' );
				exit;
			}

			$path = $relative . ltrim( $path, '.' );
		}

		return $path;
	}

	/**
	 * Load a JSON file.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file The full path to the file.
	 *
	 * @return array|bool An array from the decoded JSON or false on failure.
	 */
	protected static function load_json_file( $file ) {

		if ( ! file_exists( $file ) )
			return false;

		$json = file_get_contents( $file );

		if ( ! $json )
			return false;

		$data = json_decode( $json, true );

		if ( ! is_array( $data ) )
			return false;

		return $data;
	}

	/**
	 * Save an array to a file as a JSON string.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file The file to save the data to.
	 * @param array  $data The data to save to the file.
	 *
	 * @return bool Whether the data was saved to the file successfully.
	 */
	protected static function save_json_file( $file, $data ) {

		$json = json_encode( $data );

		if ( ! $json )
			return false;

		return (bool) file_put_contents( $file, $json );
	}

	/**
	 * Give an error.
	 *
	 * @since 0.1.0
	 *
	 * @param string $message The error message.
	 */
	public static function error( $message ) {

		self::$errors = true;

		fwrite( STDERR, $message . "\n" );
	}

	/**
	 * Run the cli app.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file The file that should have been called from the CLI.
	 *
	 * @return WP_L10n_Validator The parser instance.
	 */
	public static function cli() {

		error_reporting( E_ALL );

		// Attempt to load a JSON config file for this project.
		self::load_json_config();

		// Attempt to locate a config file for this project.
		if ( ! empty( self::$config['bootstrap'] ) )
			include self::resolve_path( self::$config['bootstrap'] );

		// If the config file hasn't overridden the parser, use the default.
		if ( ! isset( $parser ) || ! $parser instanceof WP_L10n_Validator ) {

			global $argv;

			$args = static::parse_cli_args( $argv );

			$class = get_called_class();

			$parser = new $class( $args['basedir'], $args['textdomain'] );

			switch ( $args['config'] ) {

				case 'wordpress':
					$parser->load_config( 'wordpress' );
				// fallthru

				default:
					$parser->load_config( 'default', array( 'ignores' ) );
			}

			$parser->one_by_one = $args['one-by-one'];
			$parser->update_ignored_functions( $args['ignored-functions'] );
			$parser->update_ignored_strings( $args['ignored-strings'] );
			$parser->update_ignored_atts( $args['ignored-atts'] );
		}

		// Parse the project.
		$parser->parse();

		return $parser;

	} // function cli()

	/**
	 * Load the JSON configuration, if present.
	 *
	 * @since 0.1.0
	 *
	 * @return bool Whether the file was loaded successfully.
	 */
	public static function load_json_config() {

		$working_dir = getcwd();

		if ( ! $working_dir )
			return false;

		$config = self::load_json_file( $working_dir . '/wp-l10n-validator.json' );

		if ( ! $config )
			return false;

		self::$config = array_merge( self::$config, $config );

		return true;
	}

	/**
	 * Parse the CLI args.
	 *
	 * If the CLI call does not have all of the expected arguments, the usage will
	 * be displayed by calling static::cli_usage().
	 *
	 * @since 0.1.0
	 *
	 * @param array $args The CLI args ($argv).
	 *
	 * @return array {
	 *         The parsed args.
	 *
	 *         @type string $basedir    The base directory of the project to parse.
	 *         @type string $textdomain The expected textdomain.
	 *         @type string $config     The configuration to load. Default is 'wordpress'.
	 */
	public static function parse_cli_args( $args ) {

		$parsed_args = array_merge(
			array(
				'basedir'           => '',
				'textdomain'        => '',
				'config'            => 'wordpress',
				'one-by-one'        => false,
				'ignored-functions' => array(),
				'ignored-strings'   => array(),
				'ignored-atts'      => array(),
			)
			, self::$config
		);

		if ( empty( $parsed_args['basedir'] ) ) {

			$parsed_args['basedir'] = getcwd();

			if ( ! $parsed_args['basedir'] ) {
				self::error( 'Failed: unable to get current working directory.' );
				exit( 1 );
			}

		} else {

			$parsed_args['basedir'] = self::resolve_path( $parsed_args['basedir'] );
		}

		if ( ( $key = array_search( '-1', $args ) ) ) {

			unset( $args[ $key ] );
			$args = array_values( $args );
			$parsed_args['one-by-one'] = true;
		}

		if ( ! isset( $args[1] ) ) {

			if ( empty( $parsed_args['textdomain'] ) )
				static::cli_usage();

		} else {

			$parsed_args['textdomain'] = $args[1];
		}

		if ( isset( $args[2] ) )
			$parsed_args['config'] = $args[2];

		return $parsed_args;
	}

	/**
	 * Display CLI usage message.
	 *
	 * @since 0.1.0
	 */
	public static function cli_usage() {

		fwrite(
			STDERR,
			"\nUsage: wp-l10n-validator -[1c] TEXTDOMAIN [CONFIG]\n\n"
			. "Validate all .php files in the current directory for proper gettexting.\n"
			. "\nArguments:\n"
			. "\tTEXTDOMAIN - The textdomain used in the project.\n"
			. "\tCONFIG - Configuration to use. Corressponds to one of the directories\n"
			. "\t\t in /config (wordpress by default).\n"
			. "\nFlags:\n"
			. "\t1 - Parse only one file at a time.\n"
			. "\tc - Generate a specific ignores cache.\n"
		);
		exit( 1 );
	}

} // class WP_L10n_Validator
