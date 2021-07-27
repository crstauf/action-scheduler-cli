<?php declare( strict_types=1 );

namespace AS_CLI\Commands\Action;
use function \WP_CLI\Utils\get_flag_value;

class Action {

	const COMMAND = 'ascli action';

	/**
	 * Cancel the next occurrence or all occurrences of a scheduled action.
	 *
	 * ## OPTIONS
	 *
	 * <hook>
	 * : Name of the action hook.
	 *
	 * [<group>]
	 * : The group the job is assigned to.
	 *
	 * [--args=<args>]
	 * : JSON object of arguments assigned to the job.
	 * ---
	 * default: []
	 * ---
	 *
	 * [--all]
	 * : Cancel all occurrences of a scheduled action.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @uses \AS_CLI\Commands\Action\Cancel::execute()
	 * @return void
	 */
	function cancel( array $args, array $assoc_args ) : void {
		require_once 'Action_Cancel.php';
		$command = new Cancel( $args, $assoc_args );
		$command->execute();
	}

	/**
	 * Creates a new scheduled action.
	 *
	 * ## OPTIONS
	 *
	 * <hook>
	 * : Name of the action hook.
	 *
	 * <start>
	 * : A unix timestamp representing the date you want the action to start. Also 'async' or 'now' to enqueue an async action.
	 *
	 * [--args=<args>]
	 * : JSON object of arguments to pass to callbacks when the hook triggers.
	 * ---
	 * default: []
	 * ---
	 *
	 * [--cron=<cron>]
	 * : A cron-like schedule string (https://crontab.guru/).
	 * ---
	 * default: ''
	 * ---
	 *
	 * [--group=<group>]
	 * : The group to assign this job to.
	 * ---
	 * default: ''
	 * ---
	 *
	 * [--interval=<interval>]
	 * : Number of seconds to wait between runs.
	 * ---
	 * default: 0
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp ascli action create hook_async async
	 *     wp ascli action create hook_single 1627147598
	 *     wp ascli action create hook_recurring 1627148188 --interval=5
	 *     wp ascli action create hook_cron 1627147655 --cron='5 4 * * *'
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @uses \AS_CLI\Commands\Action\Create::execute()
	 *
	 * @return void
	 */
	function create( array $args, array $assoc_args ) : void {
		require_once 'Action_Create.php';
		$command = new Create( $args, $assoc_args );
		$command->execute();
	}

	/**
	 * Generates some scheduled actions.
	 *
	 * ## OPTIONS
	 *
	 * <hook>
	 * : Name of the action hook.
	 *
	 * <start>
	 * : The Unix timestamp representing the date you want the action to start.
	 *
	 * --count=<count>
	 * : Number of actions to create.
	 * ---
	 * default: 0
	 * ---
	 *
	 * --interval=<interval>
	 * : Number of seconds to wait between runs.
	 * ---
	 * default: 0
	 * ---
	 *
	 * [--args=<args>]
	 * : JSON object of arguments to pass to callbacks when the hook triggers.
	 * ---
	 * default: []
	 * ---
	 *
	 * [--group=<group>]
	 * : The group to assign this job to.
	 * ---
	 * default: ''
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp ascli action create test_multiple 1627147598 --count=5 --interval=5
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @uses \AS_CLI\Commands\Action\Generate::execute()
	 * @return void
	 */
	function generate( array $args, array $assoc_args ) : void {
		require_once 'Action_Generate.php';
		$command = new Generate( $args, $assoc_args );
		$command->execute();
	}

	/**
	 * Get details about a scheduled action.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the action to get.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole action, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @uses \AS_CLI\Commands\Action\Get::execute()
	 * @return void
	 */
	function get( array $args, array $assoc_args ) : void {
		require_once 'Action_Get.php';
		$command = new Get( $args, $assoc_args );
		$command->execute();
	}

	/**
	 * Get a list of scheduled actions.
	 *
	 * Display actions based on all arguments supported by
	 * [as_get_scheduled_actions()](https://actionscheduler.org/api/#function-reference--as_get_scheduled_actions).
	 *
	 * [--<field>=<value>]
	 * : One or more arguments to pass to as_get_scheduled_actions().
	 *
	 * [--field=<field>]
	 * : Prints the value of a single property for each action.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific object properties.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - ids
	 *   - json
	 *   - count
	 *   - yaml
	 * ---
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each action:
	 *
	 * * id
	 * * hook
	 * * status
	 * * group
	 * * recurring
	 * * scheduled_date
	 *
	 * These fields are optionally available:
	 *
	 * * args
	 * * log_entries
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @uses \AS_CLI\Commands\Action\Action_List::execute()
	 * @return void
	 */
	function list( array $args, array $assoc_args ) : void {
		require_once 'Action_List.php';
		$command = new Action_List( $args, $assoc_args );
		$command->execute();
	}

}
