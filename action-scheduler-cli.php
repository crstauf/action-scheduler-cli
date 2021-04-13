<?php

namespace ActionSchedulerCLI;

if ( !defined( 'WP_CLI' ) || !WP_CLI )
	return;
	
add_action( 'plugins_loaded', function() {
	
	if ( !class_exists( 'ActionScheduler' ) )
		return;
	
	require_once 'commands/action.php';
	require_once 'commands/generate.php';

	\WP_CLI::add_command( 'action-scheduler action',   Commands\Action::class );
	\WP_CLI::add_command( 'action-scheduler generate', Commands\Generate::class );
	
} );

?>