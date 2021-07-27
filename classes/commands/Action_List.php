<?php declare( strict_types=1 );

namespace AS_CLI\Commands\Action;
use AS_CLI\Commands\Command_Abstract;
use function \WP_CLI\Utils\get_flag_value;

class Action_List extends Command_Abstract {

	const COMMAND = 'ascli action list';
	const DATE_FORMAT = 'Y-m-d H:i:s O';
	const PARAMETERS = array(
		'hook',
		'args',
		'date',
		'date_compare',
		'modified',
		'modified_compare',
		'group',
		'status',
		'claimed',
		'per_page',
		'offset',
		'orderby',
		'order',
	);

	/**
	 * Execute command.
	 *
	 * @return void
	 */
	public function execute() : void {
		$store  = \ActionScheduler::store();
		$logger = \ActionScheduler::logger();

		$fields = array(
			'id',
			'hook',
			'status',
			'group',
			'recurring',
			'scheduled_date',
		);

		$this->process_csv_arguments_to_arrays();

		if ( !empty( $this->assoc_args['fields'] ) )
			$fields = $this->assoc_args['fields'];

		$formatter = new \WP_CLI\Formatter( $this->assoc_args, $fields );

		$query_args = array_filter( $this->assoc_args, static function ( string $key ) : bool {
			return in_array( $key, static::PARAMETERS );
		}, ARRAY_FILTER_USE_KEY );

		if ( !empty( $query_args['args'] ) )
			$query_args['args'] = json_decode( $query_args['args'], true );

		switch ( $formatter->format ) {

			case 'ids':
				$actions = as_get_scheduled_actions( $query_args, 'ids' );
				echo implode( ' ', $actions );
				break;

			case 'count':
				$actions = as_get_scheduled_actions( $query_args, 'ids' );
				$formatter->display_items( $actions );
				break;

			default:
				$actions = as_get_scheduled_actions( $query_args );

				$actions_arr = array();

				foreach ( $actions as $action_id => $action ) {
					$action_arr = array(
						'id'             => $action_id,
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

					$actions_arr[] = $action_arr;
				}

				$formatter->display_items( $actions_arr );
				break;

		}
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
	 * Transforms arguments with '__' from CSV into expected arrays.
	 *
	 * @see \WP_CLI\CommandWithDBObject::process_csv_arguments_to_arrays()
	 * @link https://github.com/wp-cli/entity-command/blob/6e0e77a297eefa3329b94bec16c15cf7528d343f/src/WP_CLI/CommandWithDBObject.php
	 * @return void
	 */
	protected function process_csv_arguments_to_arrays() : void {
		foreach ( $this->assoc_args as $k => $v ) {
			if ( false !== strpos( $k, '__' ) ) {
				$this->assoc_args[ $k ] = explode( ',', $v );
			}
		}
	}

}
