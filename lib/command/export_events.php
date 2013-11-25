<?php
class Ai1ec_Command_Export_Events extends Ai1ec_Command {

	protected $_params;

	public function is_this_to_execute() {
		$params = $this->get_parameters();
		if ( false === $params ) {
			return false;
		}
		if ( $params['action'] === Ai1ec_Command::EXPORT_METHOD &&
 			$params['controller'] === Ai1ec_Command::EXPORT_CONTROLLER ) {
			$params['tag_ids'] = Ai1ec_Request_Parser::get_param(
				'ai1ec_cat_ids',
				false
			);
			$params['cat_ids'] = Ai1ec_Request_Parser::get_param(
				'ai1ec_cat_ids',
				false
			);
			$params['post_ids'] = Ai1ec_Request_Parser::get_param(
				'ai1ec_post_ids',
				false
			);
			$params['lang'] = Ai1ec_Request_Parser::get_param(
				'lang',
				false
			);
			$this->_params = $params;
			return true;
		}
		return false;
	}

	public function set_render_strategy( Ai1ec_Request_Parser $request ) {
		$this->_render_strategy = $this->_registry->get(
			'http.response.render.strategy.ical'
		);
	}


	/* (non-PHPdoc)
	 * @see Ai1ec_Command::do_execute()
	 */
	public function do_execute() {


		$ai1ec_cat_ids  = $this->_params['cat_ids'];
		$ai1ec_tag_ids  = $this->_params['tag_ids'];
		$ai1ec_post_ids = $this->_params['cat_ids'];
		if ( ! empty( $this->_params['lang'] ) ) {
			$loc_helper = $this->_registry->get( 'p28n.wpml' );
			$loc_helper->set_language( $this->_params['lang'] );
		}
        $args = array( 'do_not_export_as_calendar' => false );
		$filter = array();
		if ( $ai1ec_cat_ids ) {
			$filter['cat_ids']  = Ai1ec_Number_Utility::convert_to_int_list(
				',',
				$ai1ec_cat_ids
			);
		}
		if ( $ai1ec_tag_ids ) {
			$filter['tag_ids']  = Ai1ec_Number_Utility::convert_to_int_list(
				',',
				$ai1ec_tag_ids
			);
		}
		if ( $ai1ec_post_ids ) {
            $args['do_not_export_as_calendar'] = true;
			$filter['post_ids'] = Ai1ec_Number_Utility::convert_to_int_list(
				',',
				$ai1ec_post_ids
			);
		}

		// when exporting events by post_id, do not look up the event's start/end date/time
		$start  = ( $ai1ec_post_ids !== false )
			? false
			: time() - 24 * 60 * 60; // Include any events ending today
		$end    = false;
		$search = $this->_registry->get( 'model.search' );
		$export_controller = $this->_registry->get( 'controller.import-export' );
		$events = $search->get_matching_events( $start, $end, $filter );
        $args['events'] = $events;
		$ics = $export_controller->export_events( 'ics', $args );
		return array( 'data' => $ics );

	}

}