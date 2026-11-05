<?php
/**
 * Plugin Name: Simple mPDF Print Button
 * Plugin URI:  https://codepug.nl/
 * Description: Deze plugin toont een knop op je pagina, bericht of andere content, in een lay-out die je zelf hebt ontworpen. De lay-out wordt opgebouwd met standaard HTML en CSS en verwerkt door de <a href='https://mpdf.github.io/' target='-blank'>mPDF Bibliotheek.</a>
 * Version: 4.4.0
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
