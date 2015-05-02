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
		'_deprecated_argument'  => array( 1, 2 ),
		'_deprecated_file'      => array( 1, 2 ),
		'_deprecated_function'  => true,
		'add_action'            => true,
		'add_filter'            => true,
		'add_menu_page'         => array( 3, 4, 5 ),
		'add_option'            => true,
		'add_query_arg'         => true,
		'add_screen_option'     => array( 1 ),
		'add_shortcode'         => true,
		'add_submenu_page'      => array( 1, 4, 5, 6 ),
		'admin_url'             => true,
		'apply_filters'         => array( 1 ),
		'check_admin_referer'   => true,
		'check_ajax_referer'    => true,
		'current_time'          => true,
		'current_user_can'      => true,
		'dbDelta'               => true,
		'delete_metadata'       => true,
		'delete_option'         => true,
		'delete_site_transient' => true,
		'delete_transient'      => true,
		'do_action'             => array( 1 ),
		'get_bloginfo'          => true,
		'get_file_data'         => true,
		'get_option'            => true,
		'get_plugins'           => true,
		'get_post'              => true,
		'get_post_type_archive_link' => true,
		'get_site_transient'    => true,
		'get_transient'         => true,
		'get_user_by'           => true,
		'get_user_meta'         => true,
		'get_user_option'       => true,
		'get_user_setting'      => array( 1 ),
		'get_users'             => true,
		'load_plugin_textdomain' => true,
		'network_admin_url'     => true,
		'plugin_basename'       => true,
		'plugins_url'           => true,
		'post_type_supports'    => true,
		'register_activation_hook' => true,
		'register_post_type'    => array( 1 ),
		'register_setting'      => true,
		'register_widget'       => true,
		'remove_action'         => true,
		'remove_filter'         => true,
		'remove_query_arg'      => true,
		'sanitize_html_class'   => true,
		'sanitize_user_field'   => true,
		'selected'              => true,
		'self_admin_url'        => true,
		'set_site_transient'    => true,
		'set_transient'         => true,
		'set_url_scheme'        => true,
		'set_user_setting'      => array( 1 ),
		'shortcode_atts'        => array( 3 ),
		'submit_button'         => array( 2, 3, 4 ),
		'update_comment_meta'   => true,
		'update_option'         => true,
		'update_site_option'    => true,
		'update_user_meta'      => array( 1, 2 ),
		'wp_cache_delete'       => true,
		'wp_create_nonce'       => true,
		'wp_enqueue_script'     => true,
		'wp_enqueue_style'      => true,
		'wp_http_supports'      => true,
		'wp_kses'               => array( 2 ),
		'wp_list_pluck'         => array( 2, 3 ),
		'wp_localize_script'    => array( 1, 2 ),
		'wp_nonce_field'        => true,
		'wp_nonce_url'          => true,
		'wp_redirect'           => true,
		'wp_register_script'    => true,
		'wp_register_style'     => true,
		'wp_reset_vars'         => true,
		'wp_safe_redirect'      => true,
		'wp_schedule_event'     => array( 1, 2, 3 ),
		'wp_verify_nonce'       => true,
		// Instance calls.
		'$screen->in_admin'          => true,
		'$wp_list_table->search_box' => array( 2 ),
		'$wpdb->insert'              => true,
		'$wpdb->prepare'             => true,
		'$wpdb->query'               => true,
		'$wpdb->update'              => true,
		'$wpdb->get_results'         => true,
		'$wpdb->get_var'             => true,
		'$wpdb->get_col'             => true,
		// Static/parent method calls.
		'File_Upload_Upgrader::__construct' => true,
		'WP_Date_Query::__construct'        => true,
		'WP_Error::__construct'             => array( 1 ),
		'WP_List_Table::__construct'        => true,
		'WP_List_Table::screen->in_admin'   => true,
		'WP_Widget::__construct'            => array( 1 ),
	)
);

$parser->add_ignored_strings(
	array(
		'%d',
		'%s',
		'%f',
	)
);
