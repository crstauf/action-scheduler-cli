<?php declare( strict_types=1 );

namespace AS_CLI\Commands;

abstract class Command_Abstract extends \WP_CLI_Command {

	protected $args;
	protected $assoc_args;

	public function __construct( array $args, array $assoc_args ) {
		$this->args = $args;
		$this->assoc_args = $assoc_args;
	}

	abstract public function execute() : void;

}
