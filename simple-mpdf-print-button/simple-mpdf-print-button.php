<?php
/**
 * Plugin Name: Simple mPDF Print Button
 * Plugin URI:  https://codepug.nl/
 * Description: Add PDF buttons to posts, pages and other content using your own HTML/CSS templates and the mPDF library.
 * Version: 4.4.1
 * Requires PHP: 8.0
 * Requires at least: 6.3
 * Author: Evert Beijes
 * Author URI: 
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin constants
define( 'SMPPB_PATH', plugin_dir_path( __FILE__ ) );
define( 'SMPPB_URL', plugin_dir_url( __FILE__ ) );

// Includes
require_once SMPPB_PATH . 'includes/functions.php';
require_once SMPPB_PATH . 'includes/post-types.php';
require_once SMPPB_PATH . 'includes/shortcodes.php';


require_once SMPPB_PATH . 'includes/placement.php';
require_once SMPPB_PATH . 'includes/pdf-handler.php';
require_once SMPPB_PATH . 'includes/template-manager.php';
require_once SMPPB_PATH . 'includes/presentation.php';

require_once SMPPB_PATH . 'includes/info-page.php';
