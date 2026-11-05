<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'smppb_admin_menu' );
add_action( 'admin_enqueue_scripts', 'smppb_admin_enqueue_assets' );
add_action( 'admin_init', 'smppb_handle_form_submissions' );
