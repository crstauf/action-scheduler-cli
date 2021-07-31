<?php declare( strict_types=1 );
/**
 * Plugin Name: Action Scheduler CLI
 * Description: Registers WP-CLI commands for Action Scheduler plugin.
 * Version: 0.1.0
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 */

namespace AS_CLI;

/**
 * Action: action_scheduler_pre_init
 *
 * Only load if Action Scheduler is active.
 *
 * @uses Plugin::init()
 * @return void
 */
add_action( 'action_scheduler_pre_init', static function() : void {

	require_once 'classes/Plugin.php';
	Plugin::init( __FILE__ );

} );
