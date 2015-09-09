<?php

/**
 * Wrap library calls to date subsystem.
 *
 * Meant to increase performance and work around known bugs in environment.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Date
 */

/*
 * Date parser for PHP <= 5.2
 *
 * Source: http://stackoverflow.com/questions/6668223/php-date-parse-from-format-alternative-in-php-5-2
 *
 * Modified to always populate hour, minute and second.
 *
 */
if ( ! function_exists( 'date_parse_from_format' ) ) {
	function date_parse_from_format( $format, $date ) {
		// reverse engineer date formats
		$keys                      = array(
			'Y' => array('year',   '\d{4}'),
			'y' => array('year',   '\d{2}'),
			'm' => array('month',  '\d{2}'),
			'n' => array('month',  '\d{1,2}'),
			'M' => array('month',  '[A-Z][a-z]{3}'),
			'F' => array('month',  '[A-Z][a-z]{2,8}'),
			'd' => array('day',    '\d{2}'),
			'j' => array('day',    '\d{1,2}'),
			'D' => array('day',    '[A-Z][a-z]{2}'),
			'l' => array('day',    '[A-Z][a-z]{6,9}'),
			'u' => array('hour',   '\d{1,6}'),
			'h' => array('hour',   '\d{2}'),
			'H' => array('hour',   '\d{2}'),
			'g' => array('hour',   '\d{1,2}'),
			'G' => array('hour',   '\d{1,2}'),
			'i' => array('minute', '\d{2}'),
			's' => array('second', '\d{2}')
		);

		// convert format string to regex
		$regex                     = '';
		$chars                     = str_split( $format );
		foreach ( $chars AS $n => $char ) {
			$lastChar = isset( $chars[$n - 1] ) ? $chars[$n - 1] : '';
			$skipCurrent           = '\\' == $lastChar;
			if ( !$skipCurrent && isset( $keys[$char] ) ) {
				$regex            .= '(?P<' . $keys[$char][0] . '>' . $keys[$char][1] . ')';
			} else if ( '\\' == $char ) {
				$regex            .= $char;
			} else {
				$regex            .= preg_quote( $char );
			}
		}

		$dt                        = array();
		// now try to match it
		if ( preg_match( '#^' . $regex . '$#', $date, $dt ) ) {
			foreach ( $dt AS $k => $v ) {
				if ( is_int( $k ) ) {
					unset( $dt[$k] );
				}
			}
			if ( ! checkdate( $dt['month'], $dt['day'], $dt['year'] ) ) {
				$dt['error_count'] = 1;
			} else {
				$dt['error_count'] = 0;
			}
			if ( ! isset( $dt['hour'] ) ) {
				$dt['hour']        = 0;
			}
			if ( ! isset( $dt['minute'] ) ) {
				$dt['minute']      = 0;
			}
			if ( ! isset( $dt['second'] ) ) {
				$dt['second']      = 0;
			}
		} else {
			$dt['error_count']     = 1;
		}
		$dt['errors']              = array();
		$dt['fraction']            = '';
		$dt['warning_count']       = 0;
		$dt['warnings']            = array();
		$dt['is_localtime']        = 0;
		$dt['zone_type']           = 0;
		$dt['zone']                = 0;
		$dt['is_dst']              = '';

		return $dt;
	}
}

class Ai1ec_Date_System extends Ai1ec_Base {

	/**
	 * @var array List of local time (key '0') and GMT time (key '1').
	 */
	protected $_current_time = array();

	/**
	 * @var Ai1ec_Cache_Memory
	 */
	protected $_gmtdates;

	/**
	 * Initiate current time list.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$gmt_time = ( version_compare( PHP_VERSION, '5.1.0' ) >= 0 )
			? time()
			: gmmktime();
		$this->_current_time     = array(
			(int)$_SERVER['REQUEST_TIME'],
			$gmt_time,
		);
		$this->_gmtdates = $registry->get( 'cache.memory' );
	}

	/**
	 * Get current time UNIX timestamp.
	 *
	 * Uses in-memory value, instead of re-calling `time()` / `gmmktime()`.
	 *
	 * @param bool $is_gmt Set to true to get GMT timestamp.
	 *
	 * @return int Current time UNIX timestamp
	 */
	public function current_time( $is_gmt = false ) {
		return $this->_current_time[(int)( (bool)$is_gmt )];
	}

	/**
	 * Returns the associative array of date patterns supported by the plugin.
	 *
	 * Currently the formats are:
	 *   array(
	 *     'def' => 'd/m/yyyy',
	 *     'us'  => 'm/d/yyyy',
	 *     'iso' => 'yyyy-m-d',
	 *     'dot' => 'm.d.yyyy',
	 *   );
	 *
	 * 'd' or 'dd' represent the day, 'm' or 'mm' represent the month, and 'yy'
	 * or 'yyyy' represent the year.
	 *
	 * @return array List of supported date patterns.
	 */
	public function get_date_patterns() {
		return array(
			'def' => 'd/m/yyyy',
			'us'  => 'm/d/yyyy',
			'iso' => 'yyyy-m-d',
			'dot' => 'm.d.yyyy',
		);
	}

	/**
	 * Get acceptable date format.
	 *
	 * Returns the date pattern (in the form 'd-m-yyyy', for example) associated
	 * with the provided key, used by plugin settings. Simply a static map as
	 * follows:
	 *
	 * @param string $key Key for the date format.
	 *
	 * @return string Associated date format pattern.
	 */
	public function get_date_pattern_by_key( $key = 'def' ) {
		$patterns = $this->get_date_patterns();
		if ( ! isset( $patterns[$key] ) ) {
			return (string)current( $patterns );
		}
		return $patterns[$key];
	}

	/**
	 * Format timestamp into URL safe, user selected representation.
	 *
	 * Returns a formatted date given a timestamp, based on the given date
	 * format, with any '/' characters replaced with URL-friendly '-'
	 * characters.
	 *
	 * @see Ai1ec_Date_System::get_date_patterns() for supported date formats.
	 *
	 * @param int    $timestamp UNIX timestamp representing a date.
	 * @param string $pattern   Key of date pattern (@see
	 *                          self::get_date_format_patter()) to
	 *                          format date with
	 *
	 * @return string Formatted date string.
	 */
	public function format_date_for_url( $timestamp, $pattern = 'def' ) {
		$date = $this->format_date( $timestamp, $pattern );
		$date = str_replace( '/', '-', $date );
		return $date;
	}

	/**
	 * Similar to {@see format_date_for_url} just using new DateTime interface.
	 *
	 * @param Ai1ec_Date_Time $datetime Instance of datetime to format.
	 * @param string          $pattern  Target format to use.
	 *
	 * @return string Formatted datetime string.
	 */
	public function format_datetime_for_url(
		Ai1ec_Date_Time $datetime,
		$pattern = 'def'
	) {
		$date = $datetime->format( $this->get_date_format_patter( $pattern ) );
		return str_replace( '/', '-', $date );
	}

	/**
	 * Returns the date formatted with new pattern from a given date and old pattern.
	 *
	 * @see  self::get_date_patterns() for supported date formats.
	 *
	 * @param  string $date          Formatted date string
	 * @param  string $old_pattern   Key of old date pattern (@see
	 *                               self::get_date_format_patter())
	 * @param  string $new_pattern   Key of new date pattern (@see
	 *                               self::get_date_format_patter())
	 * @return string                Formatted date string with new pattern
	 */
	public function convert_date_format( $date, $old_pattern, $new_pattern ) {
		// Convert old date to timestamp
		$timeArray = date_parse_from_format( $this->get_date_format_patter( $old_pattern ), $date );

		$timestamp = mktime(
			$timeArray['hour'], $timeArray['minute'], $timeArray['second'],
			$timeArray['month'], $timeArray['day'], $timeArray['year']
		);

		// Convert to new date pattern
		return $this->format_date( $timestamp, $new_pattern );
	}

	/**
	 * Returns a formatted date given a timestamp, based on the given date format.
	 *
	 * @see  self::get_date_patterns() for supported date formats.
	 *
	 * @param  int $timestamp    UNIX timestamp representing a date (in GMT)
	 * @param  string $pattern   Key of date pattern (@see
	 *                           self::get_date_format_patter()) to
	 *                           format date with
	 * @return string            Formatted date string
	 */
	public function format_date( $timestamp, $pattern = 'def' ) {
		return gmdate( $this->get_date_format_patter( $pattern ), $timestamp );
	}

	public function get_date_format_patter( $requested ) {
		$pattern = $this->get_date_pattern_by_key( $requested );
		$pattern = str_replace(
			array( 'dd', 'd', 'mm', 'm', 'yyyy', 'yy' ),
			array( 'd',  'j', 'm',  'n', 'Y',    'y' ),
			$pattern
		);
		return $pattern;
	}

	/**
	 * Returns human-readable version of the GMT offset.
	 *
	 * @param string $timezone_name Olsen Timezone name [optional=null]
	 *
	 * @return string GMT offset expression
	 */
	public function get_gmt_offset_expr( $timezone_name = null ) {
		$timezone = $this->get_gmt_offset( $timezone_name );
		$offset_h = (int)( $timezone / 60 );
		$offset_m = absint( $timezone - $offset_h * 60 );
		$timezone = sprintf(
			Ai1ec_I18n::__( 'GMT%+d:%02d' ),
			$offset_h,
			$offset_m
		);

		return $timezone;
	}

	/**
	 * Get current GMT offset in seconds.
	 *
	 * @param string $timezone_name Olsen Timezone name [optional=null]
	 *
	 * @return int Offset from GMT in seconds.
	 */
	public function get_gmt_offset( $timezone_name = null ) {
		if ( null === $timezone_name ) {
			$timezone_name = 'sys.default';
		}
		$current = $this->_registry->get(
			'date.time',
			'now',
			$timezone_name
		);
		return $current->get_gmt_offset();
	}

	/**
	 * gmgetdate method
	 *
	 * Get date/time information in GMT
	 *
	 * @param int $timestamp Timestamp at which information shall be evaluated
	 *
	 * @return array Associative array of information related to the timestamp
	 */
	public function gmgetdate( $timestamp = NULL ) {
		if ( NULL === $timestamp ) {
			$timestamp = (int)$_SERVER['REQUEST_TIME'];
		}
		if ( NULL === ( $date = $this->_gmtdates->get( $timestamp ) ) ) {
			$particles = explode(
				',',
				gmdate( 's,i,G,j,w,n,Y,z,l,F,U', $timestamp )
			);
			$date      = array_combine(
				array(
					'seconds',
					'minutes',
					'hours',
					'mday',
					'wday',
					'mon',
					'year',
					'yday',
					'weekday',
					'month',
					0
				),
				$particles
			);
			$this->_gmtdates->set( $timestamp, $date );
		}
		return $date;
	}

	/**
	 * Returns current rounded time as unix integer.
	 *
	 * @param int $shift Shift value.
	 *
	 * @return int Unix timestamp.
	 */
	public function get_current_rounded_time( $shift = 11 ) {
		return $this->current_time() >> $shift << $shift;
	}
}