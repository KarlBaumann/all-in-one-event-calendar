<?php

/**
 * Renderer of settings page Calendar page selection snippet.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Html
 */
class Ai1ec_Html_Element_Calendar_Page_Selector
    extends Ai1ec_Html_Element_Settings {

	/**
	 * @var string HTML id attribute for selector.
	 */
	const ELEMENT_ID     = 'calendar_page_id';

	/**
	 * @var array Map of pages defined in system, use `get_pages()` WP call.
	 */
	protected $_pages    = array();

	/**
	 * @var int ID of page currently selected, NULL if none.
	 */
	protected $_selected = NULL;

	/**
	 * Set attributes for element.
	 *
	 * Currently recognized attributes:
	 *     - 'pages'    - {@see self::$_pages} for details;
	 *     - 'selected' - {@see self::$_selected} for details.
	 *
	 * @param string $attribute Name of attribute to set.
	 * @param mixed  $value     Value to set for attribute.
	 *
	 * @return Ai1ec_Html_Element_Calendar_Page_Selector Instance of self.
	 */
	public function set( $attribute, $value ) {
		// any validation may be provided here
		return parent::set( $attribute, $value );
	}

	/**
	 * Generate HTML snippet for inclusion in settings page.
	 *
	 * @param string $snippet Particle to append to result.
	 *
	 * @return string HTML snippet for page selection.
	 */
	public function render( $snippet = '' ) {
		$output = $this->_get_pages_selector() . $this->_get_page_view_link();
		$output = '<div class="pull-left">' . $output . '</div>';
		$output = '<label class="textinput" for="' . self::ELEMENT_ID . '">' .
			Ai1ec_I18n::__( 'Calendar page:' ) .
			'</label>' . $output;
		return parent::render( $output );
	}

	/**
	 * Generate link to open selected page in new window.
	 *
	 * @return string HTML snippet.
	 */
	protected function _get_page_view_link() {
		if ( empty( $this->_selected ) ) {
			return '';
		}
		$post = get_post( $this->_selected );
		if ( empty( $post ) ) {
			return '';
		}
		return '<p><a target="_blank" href="' . get_permalink( $post->ID ) . '">
			View "' . $post->title . '"
			<i class="icon-arrow-right"></i>
		</a></p>';
	}

	/**
	 * Generate dropdown selector to choose page.
	 *
	 * @return string HTML snippet.
	 */
	protected function _get_pages_selector() {
		$html = '<select id="' . self::ELEMENT_ID .
			'" class="inputwidth" name="' . self::ELEMENT_ID . '">';
		$list = $this->_get_pages();
		foreach ( $list as $key => $value ) {
			$html .= '<option value="' . $this->_html->esc_attr( $key ) . '"';
			if ( $this->_selected === $key ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . $this->_html->escape( $value ) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	/**
	 * Make a map of page IDs and titles for selection snippet.
	 *
	 * @return array Map of page keys and titles.
	 */
	protected function _get_pages() {
		$pages = $this->_pages;
		if ( ! is_array( $pages ) ) {
			$pages = array();
		}
		$output = array(
			'__auto_page:Calendar' => Ai1ec_I18n::__( '- Auto-Create New Page -' ),
		);
		foreach ( $pages as $key => $value ) {
			$output[$key] = $value;
		}
		return $output;
	}

}