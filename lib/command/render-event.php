<?php

/**
 * The concrete command that renders the event.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Render_Event extends Ai1ec_Command_Render_Calendar {

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::is_this_to_execute()
	 */
	public function is_this_to_execute() {
		$aco = $this->_registry->get( 'acl.aco' );
		return $aco->is_our_post_type();
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::do_execute()
	 */
	public function do_execute() {
		throw new Ai1ec_Exception( 'Single event view is not implemented.' );
		// get the event html
	}
}