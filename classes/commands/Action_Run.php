<?php declare( strict_types=1 );

namespace AS_CLI\Commands\Action;
use AS_CLI\Commands\Command_Abstract;
use function \WP_CLI\Utils\get_flag_value;

class Run extends Command_Abstract {

	protected $action_id;

	function __construct( array $args, array $assoc_args ) {
		parent::__construct( $args, $assoc_args );

		$this->action_id = absint( $args[0] );

		add_action( 'action_scheduler_execution_ignored', array( $this, 'action__ignored'  ) );
		add_action( 'action_scheduler_after_execute',     array( $this, 'action__executed' ) );
		add_action( 'action_scheduler_failed_execution',  array( $this, 'action__failed'   ), 10, 2 );
		add_action( 'action_scheduler_failed_validation', array( $this, 'action__invalid'  ), 10, 2 );
	}

	/**
	 * Execute.
	 *
	 * @uses \ActionScheduler_Abstract_QueueRunner::process_action()
	 * @uses \ActionScheduler_Action::execute()
	 * @return void
	 */
	function execute() : void {
		$runner = \ActionScheduler::runner();
		$runner->process_action( $this->action_id, 'Action Scheduler CLI' );
	}

	/**
	 * Action: action_scheduler_execution_ignored
	 *
	 * @param int $action_id
	 * @uses \WP_CLI::warning()
	 * @return void
	 */
	function action__ignored( int $action_id ) : void {
		if ( $this->action_id !== $action_id )
			return;

		\WP_CLI::warning( sprintf( 'Action %d was ignored.', $this->action_id ) );
	}

	/**
	 * Action: action_scheduler_after_execute
	 *
	 * @param int $action_id
	 * @uses \WP_CLI::success()
	 * @return void
	 */
	function action__executed( int $action_id ) : void {
		if ( $this->action_id !== $action_id )
			return;

		\WP_CLI::success( sprintf( 'Action %d was executed.', $this->action_id ) );
	}

	/**
	 * Action: action_scheduler_failed_execution
	 *
	 * @param int $action_id
	 * @param \Exception $e
	 * @uses \WP_CLI::error()
	 * @return void
	 */
	function action__failed( int $action_id, \Exception $e ) : void {
		if ( $this->action_id !== $action_id )
			return;

		\WP_CLI::error( sprintf( 'Action %d failed execution: %s', $this->action_id, $e->getMessage() ) );
	}

	/**
	 * Action: action_scheduler_failed_validation
	 *
	 * @param int $action_id
	 * @param \Exception $e
	 * @uses \WP_CLI::error()
	 * @return void
	 */
	function action__invalid( int $action_id, \Exception $e ) : void {
		if ( $this->action_id !== $action_id )
			return;

		\WP_CLI::error( sprintf( 'Action %d failed validation: %s', $this->action_id, $e->getMessage() ) );
	}

}
