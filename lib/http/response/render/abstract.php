<?php
/**
 * Abstract strategy class to render the Request.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Response.Render
 */
abstract class Ai1ec_Http_Response_Render_Strategy {

	/**
	 * Dump output buffers before starting output
	 *
	 * @return bool True unless an error occurs
	 */
	protected function _dump_buffers() {
		$result = true;
		while ( ob_get_level() ) {
			$result &= ob_end_clean();
		}
		return $result;
	}

	/**
	 * Render the output.
	 * 
	 * @param array $params
	 */
	abstract public function render( array $params );
}