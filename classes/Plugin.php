<?php declare( strict_types=1 );

namespace AS_CLI;
use AS_CLI\Commands\Action\Action;

class Plugin {

	static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	static function init( string $file ) : void {
		static $once = false;

		if ( true === $once )
			return;

		$once = true;
		$instance = static::instance();

		$instance->file = $file;
		$instance->directory = dirname( $file );
	}

	protected function __construct() {

		require_once 'commands/Action.php';
		require_once 'commands/Command_Abstract.php';

		\WP_CLI::add_command( Action::COMMAND, Action::class );

	}

}
