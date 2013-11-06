<?php

/**
 * Ignores configuration for WordPress.
 *
 * @package WP_L10n_Validator
 * @since 0.1.0
 */

$parser->add_ignored_functions(
	array(
		// Functions.
		'_deprecated_function'  => true,
		'add_action'            => true,
		'add_filter'            => true,
		'add_option'            => true,
		'add_query_arg'         => true,
		'add_shortcode'         => true,
		'admin_url'             => true,
		'check_admin_referer'   => true,
		'check_ajax_referer'    => true,
		'current_time'          => true,
		'current_user_can'      => true,
		'dbDelta'               => true,
		'delete_metadata'       => true,
		'delete_option'         => true,
		'delete_site_transient' => true,
		'delete_transient'      => true,
		'get_bloginfo'          => true,
		'get_file_data'         => true,
		'get_option'            => true,
		'get_plugins'           => true,
		'get_post'              => true,
		'get_site_transient'    => true,
		'get_transient'         => true,
		'get_user_meta'         => true,
		'get_user_option'       => true,
		'network_admin_url'     => true,
		'plugins_url'           => true,
		'register_activation_hook' => true,
		'register_widget'       => true,
		'remove_filter'         => true,
		'sanitize_user_field'   => true,
		'selected'              => true,
		'self_admin_url'        => true,
		'set_site_transient'    => true,
		'set_transient'         => true,
		'set_url_scheme'        => true,
		'update_comment_meta'   => true,
		'update_option'         => true,
		'update_site_option'    => true,
		'wp_cache_delete'       => true,
		'wp_create_nonce'       => true,
		'wp_enqueue_script'     => true,
		'wp_enqueue_style'      => true,
		'wp_nonce_field'        => true,
		'wp_nonce_url'          => true,
		'wp_register_script'    => true,
		'wp_register_style'     => true,
		'wp_verify_nonce'       => true,
		// Instance calls.
		'$wpdb->insert'  => true,
		'$wpdb->prepare' => true,
		'$wpdb->query'   => true,
		'$wpdb->update'  => true,
		// New instance calls.
		'new File_Upload_Upgrader' => true,
		'new WP_Date_Query'        => true,
	)
);

$parser->add_ignored_args(
	array(
		// Functions.
		'add_menu_page'      => array(       3, 4, 5 ),
		'add_screen_option'  => array( 1 ),
		'add_submenu_page'   => array( 1,       4, 5, 6 ),
		'apply_filters'      => array( 1 ),
		'do_action'          => array( 1 ),
		'get_user_setting'   => array( 1 ),
		'set_user_setting'   => array( 1 ),
		'shortcode_atts'     => array(       3 ),
		'submit_button'      => array(    2, 3, 4 ),
		'update_user_meta'   => array( 1, 2 ),
		'wp_localize_script' => array( 1, 2 ),
		// New instances.
		'new WP_Error'       => array( 1 ),
	)
);

$parser->add_ignored_strings(
	array(
		'%d',
		'%s',
		'%f',
	)
);
