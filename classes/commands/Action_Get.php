<?php declare( strict_types=1 );

namespace AS_CLI\Commands\Action;
use AS_CLI\Commands\Command_Abstract;
use function \WP_CLI\Utils\get_flag_value;

class Get extends Command_Abstract {

	const COMMAND = 'ascli action get';
	const DATE_FORMAT = 'Y-m-d H:i:s O';

	/**
	 * Execute command.
	 *
	 * @return void
	 */
	public function execute() : void {
		$action_id = $this->args[0];
		$store     = \ActionScheduler::store();
		$logger    = \ActionScheduler::logger();
		$action    = $store->fetch_action( $action_id );


		$action_arr = array(
			'id'             => $this->args[0],
			'hook'           => $action->get_hook(),
			'status'         => $store->get_status( $action_id ),
			'args'           => $action->get_args(),
			'group'          => $action->get_group(),
			'recurring'      => $action->get_schedule()->is_recurring() ? 'yes' : 'no',
			'scheduled_date' => $this->get_schedule_display_string( $action->get_schedule() ),
			'log_entries'    => array(),
		);

		foreach ( $logger->get_logs( $action_id ) as $log_entry ) {
			$action_arr['log_entries'][] = array(
				'date'    => $log_entry->get_date()->format( static::DATE_FORMAT ),
				'message' => $log_entry->get_message(),
			);
		}

		$fields = array_keys( $action_arr );

		if ( !empty( $this->assoc_args['fields'] ) )
			$fields = explode( ',', $this->assoc_args['fields'] );

		$formatter = new \WP_CLI\Formatter( $this->assoc_args, $fields );
		$formatter->display_item( $action_arr );
	}

	/**
	 * Get the scheduled date in a human friendly format.
	 *
	 * @see \ActionScheduler_ListTable::get_schedule_display_string()
	 * @param ActionScheduler_Schedule $schedule
	 * @return string
	 */
	protected function get_schedule_display_string( \ActionScheduler_Schedule $schedule ) {

		$schedule_display_string = '';

		if ( ! $schedule->get_date() ) {
			return '0000-00-00 00:00:00';
		}

		$next_timestamp = $schedule->get_date()->getTimestamp();

		$schedule_display_string .= $schedule->get_date()->format( static::DATE_FORMAT );

		return $schedule_display_string;
	}

	/**
	 * Returns the recurrence of an action or 'Non-repeating'. The output is human readable.
	 *
	 * @see \ActionScheduler_ListTable::get_recurrence()
	 * @param ActionScheduler_Action $action
	 *
	 * @return string
	 */
	protected function get_recurrence( $action ) {
		$schedule = $action->get_schedule();
		if ( $schedule->is_recurring() ) {
			$recurrence = $schedule->get_recurrence();

			if ( is_numeric( $recurrence ) ) {
				/* translators: %s: time interval */
				return sprintf( __( 'Every %s', 'action-scheduler' ), self::human_interval( $recurrence ) );
			} else {
				return $recurrence;
			}
		}

		return __( 'Non-repeating', 'action-scheduler' );
	}

}
