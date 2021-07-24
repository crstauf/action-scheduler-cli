<?php declare( strict_types=1 );

namespace AS_CLI\Commands\Action;
use AS_CLI\Commands\Command_Abstract;
use function \WP_CLI\Utils\get_flag_value;

class Generate extends Command_Abstract {

	const COMMAND = 'ascli action generate';

	/**
	 * Execute command.
	 *
	 * @return void
	 */
	public function execute() : void {
		$hook           = $this->args[0];
		$schedule_start = $this->args[1];
		$hook_args      = get_flag_value( $this->assoc_args, 'args', array() );
		$group          = get_flag_value( $this->assoc_args, 'group', '' );
		$interval       = absint( get_flag_value( $this->assoc_args, 'interval', 0 ) );
		$count          = absint( get_flag_value( $this->assoc_args, 'count', 0 ) );
		$cron           = get_flag_value( $this->assoc_args, 'cron', '' );

		$schedule_start = as_get_datetime_object( $schedule_start );

		if ( !empty( $args ) )
			$hook_args = json_decode( $hook_args, true );

		$function_args = array(
			'start'     => ( int ) $schedule_start->format( 'U' ),
			'cron'      => $cron,
			'interval'  => $interval,
			'count'     => $count,
			'hook'      => $hook,
			'hook_args' => $hook_args,
			'group'     => $group,
		);

		// Default to creating single action.
		$action_type = 'single';
		$function    = 'as_schedule_single_action';

		// Creating cron action.
		if ( !empty( $cron ) ) {
			$action_type = 'cron';
			$function = 'as_schedule_cron_action';

		// Creating multiple single actions.
		} else if (
			   !empty( $count )
			&& !empty( $interval )
		) {
			$function = array( $this, 'add_multiple_single_actions' );

		// Creating recurring action.
		} else if ( !empty( $interval ) ) {
			$action_type = 'recurring';
			$function = 'as_schedule_recurring_action';

		// Creating single action.
		} else if ( !empty( $count ) ) {
			\WP_CLI::warning( 'Flag `count` must be accompanied with `interval` flag.' );
			unset( $function_args['count'] );
		}

		$function_args = array_values( array_filter( $function_args ) );

		try {
			$actions_added = call_user_func_array( $function, $function_args );
			$num_actions_added = count( ( array ) $actions_added );
		} catch ( \Exception $e ) {
			$this->print_error( $e );
		}

		$this->print_success( $num_actions_added, $action_type );
	}

	/**
	 * Schedule multiple single actions.
	 *
	 * @param int $schedule_start Starting timestamp of first action.
	 * @param int $interval How long to wait between runs.
	 * @param int $count Limit number of actions to schedule.
	 * @param string $hook The hook to trigger.
	 * @param array $args Arguments to pass when the hook triggers.
	 * @param string $group The group to assign this job to.
	 *
	 * @return int[] IDs of actions added.
	 */
	protected function add_multiple_single_actions( int $schedule_start, int $interval, int $count, string $hook, array $args = array(), string $group = '' ) : array {
		$actions_added = array();

		$progress_bar = \WP_CLI\Utils\make_progress_bar(
			sprintf( _n( 'Creating %d action', 'Creating %d actions', $count, 'action-scheduler' ), number_format_i18n( $count ) ),
			$count
		);

		for ( $i = 0; $i < $count; $i++ ) {
			$actions_added[] = as_schedule_single_action( $schedule_start + ( $i * $interval ), $hook, $args, $group );
			$progress_bar->tick();
		}

		$progress_bar->finish();

		return $actions_added;
	}

	/**
	 * Print a success message with the action ID.
	 *
	 * @param int $action_added
	 * @param string $action_type
	 *
	 * @return void
	 */
	protected function print_success( $actions_added, $action_type ) : void {
		\WP_CLI::success(
			sprintf(
				/* translators: %d refers to the total number of taskes added */
				_n( '%d %s action scheduled.', '%d %s actions scheduled.', $actions_added, 'action-scheduler' ),
				number_format_i18n( $actions_added ),
				$action_type
			)
		);
	}

	/**
	 * Convert an exception into a WP CLI error.
	 *
	 * @param \Exception $e The error object.
	 *
	 * @throws \WP_CLI\ExitException
	 *
	 * @return void
	 */
	protected function print_error( \Exception $e ) : void {
		\WP_CLI::error(
			sprintf(
				/* translators: %s refers to the exception error message. */
				__( 'There was an error creating the scheduled action: %s', 'action-scheduler' ),
				$e->getMessage()
			)
		);
	}

}
