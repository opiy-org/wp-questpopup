<?php
/*
 * Plugin Name: QuestPopup
 * Version: 1.0
 * Plugin URI: http://opiy.org/donate/
 * Description: Делаем крутой попап на страницах сайта
 * Author: opiy
 * Author URI: http://opiy.org/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: questpopup
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Opiy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-questpopup.php' );
require_once( 'includes/class-questpopup-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-questpopup-admin-api.php' );

/**
 * Returns the main instance of questpopup to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object questpopup
 */
function questpopup () {
	$instance = questpopup::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = questpopup_Settings::instance( $instance );
	}

	return $instance;
}

questpopup();