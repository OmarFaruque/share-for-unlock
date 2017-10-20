<?php
/**
 * Plugin Name: share for unlock
 * Plugin URI: https://larasoftbd.com/form-status-update
 * Description: Share content to social media and unlock content.
 * Version: 1.0.0
 * Author: larasoft
 * Author URI: https://larasoftbd.com/
 * Text Domain: larasoft
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * @package     Contact-Form-7-Extension
 * @category 	Core
 * @author 		LaraSoft
 */

/**
 * Restrict direct access
 */



if ( ! defined( 'ABSPATH' ) ) { exit; }
define('SRDIR', plugin_dir_path( __FILE__ ));
define('SRURL', plugin_dir_url( __FILE__ ));

require_once(SRDIR . 'inc/aps-class.php');
new socialShareAndUnlock;


