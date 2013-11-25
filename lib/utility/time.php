<?php

/**
 * Time and date manipulations library.
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Utility
 */

class Ai1ec_Time_Utility extends Ai1ec_Base{


	/**
	 * @var Ai1ec_Memory_Utility Instance, where DateTimeZone objects are held
	 */
	protected $_timezones         = NULL;

	/**
	 * @var Ai1ec_Memory_Utility Instance, where timezone GMT offsets (in hours)
	 *                           are held
	 */
	protected $_gmt_offsets       = NULL;

	/**
	 * @var Ai1ec_Memory_Utility Instance, where parsed GMT timestamp
	 *                           information is stored
	 */
	protected $_gmtdates          = NULL;

	/**
	 * @var Ai1ec_Memory_Utility Instance, where offsets between two timezones
	 *                           are stored
	 */
	protected $_timezone_offsets  = NULL;

	/**
	 * @var Ai1ec_Memory_Utility Instance storing difference inflected by DST
	 */
	protected $_dst_differences   = NULL;

	/**
	 * @var Ai1ec_Time_I18n_Utility Instance of I18n time management utility
	 */
	protected $_time_i18n         = NULL;

	/**
	 * @var array Information about default timezone to speedup access
	 */
	protected $_default_timezone  = NULL;

	/**
	 * @var array Current time UNIX timestamp at 0 and GMT timestamp at 1
	 */
	protected $_current_time      = NULL;


	/**
	 * to_mysql_date method
	 *
	 * Convert UNIX timestamp to date, that may be used within
	 * MySQL `DATE` field.
	 *
	 * @param int $timestamp Timestamp to convert to MySQL date
	 *
	 * @return string MySQL date to use in queries
	 */
	public function to_mysql_date( $timestamp ) {
		return date( 'Y-m-d H:i:s', $timestamp );
	}

	/**
	 * from_mysql_date method
	 *
	 * Convert date, stored in MySQL `DATE` type field to UNIX timestamp.
	 *
	 * @param string $date Date retrieved from MySQL
	 *
	 * @return int UNIX timestamp decoded from date given
	 */
	 public function from_mysql_date( $date ) {
		return strtotime( $date );
	}

	/**
	 * Returns the associative array of date patterns supported by the plugin,
	 * currently:
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
	 * @return array Supported date patterns
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
	 * Returns the date pattern (in the form 'd-m-yyyy', for example) associated
	 * with the provided key, used by plugin settings. Simply a  map as
	 * follows:
	 *
	 * @param  string $key Key for the date format
	 * @return string      Associated date format pattern
	 */
	 public function get_date_pattern_by_key( $key = 'def' ) {
		$patterns = self::get_date_patterns();
		return $patterns[$key];
	}

	/**
	 * Returns a formatted date given a timestamp, based on the given date format,
	 * with any '/' characters replaced with URL-friendly '-' characters.
	 * @see  Ai1ec_Time_Utility::get_date_patterns() for supported date formats.
	 *
	 * @param  int $timestamp    UNIX timestamp representing a date (in GMT)
	 * @param  string $pattern   Key of date pattern (@see
	 *                           Ai1ec_Time_Utility::get_date_patterns()) to
	 *                           format date with
	 * @return string            Formatted date string
	 */
	 public function format_date_for_url( $timestamp, $pattern = 'def' ) {
		$date = self::format_date( $timestamp, $pattern );
		$date = str_replace( '/', '-', $date );
		return $date;
	}

	/**
	 * Returns a formatted date given a timestamp, based on the given date format.
	 * @see  Ai1ec_Time_Utility::get_date_patterns() for supported date formats.
	 *
	 * @param  int $timestamp    UNIX timestamp representing a date (in GMT)
	 * @param  string $pattern   Key of date pattern (@see
	 *                           Ai1ec_Time_Utility::get_date_patterns()) to
	 *                           format date with
	 * @return string            Formatted date string
	 */
	 public function format_date( $timestamp, $pattern = 'def' ) {
		$pattern = self::get_date_pattern_by_key( $pattern );
		$pattern = str_replace(
			array( 'dd', 'd', 'mm', 'm', 'yyyy', 'yy' ),
			array( 'd', 'j', 'm', 'n', 'Y', 'y' ),
			$pattern
		);
		return gmdate( $pattern, $timestamp );
	}

	/**
	 * get_default_timezone method
	 *
	 * Singleton backed interface method, to get name of system-default
	 * timezone name.
	 *
	 * @return string Name of default timezone
	 *
	 * @throws Ai1ec_Date_Exception If default timezone is invalid/undefined
	 */
	 public function get_default_timezone() {
		if ( NULL === $this->_default_timezone ) {
			$name       = date_default_timezone_get();
			$name_entry = array(
				'name'  => $name,
				'valid' => false,
			);
			if ( is_string( $name ) ) {
				$name_entry['valid'] = true;
			}
			$this->_default_timezone = $name_entry;
			unset( $name, $name_entry );
		}
		if ( ! $this->_default_timezone['valid'] ) {
			throw new Ai1ec_Date_Exception( 'Default timezone undefined' );
		}
		return $this->_default_timezone['name'];
	}

	/**
	 * get_local_timezone method
	 *
	 * Get timezone used by current user/installation, as local.
	 *
	 * @param string $default Timezone to use, if none is detected
	 *
	 * @return string Timezone identified as local
	 */
	public function get_local_timezone( $default = 'America/Los_Angeles' ) {
		 $_cached = NULL;
		if ( NULL === $_cached ) {
			$user = wp_get_current_user();
			$zone = '';
			if ( $user->ID > 0 ) {

                $ai1ec_app_helper = $this->_registry->get( 'Ai1ec_App_Helper' );
				$zone = $ai1ec_app_helper->user_selected_tz( $user->ID );
			}
			unset( $user );
			if ( empty( $zone ) ) {
				$zone = get_option( 'timezone_string', $default );
				if ( empty( $zone ) ) {
					$zone = false;
				}
			}
			$_cached = $zone;
			unset( $zone );
		}
		if ( false === $_cached ) {
			return $default;
		}
		return $_cached;
	}

	/**
	 * get_gmt_offset method
	 *
	 * Get timezone offset from GMT, in hours, given time, at which this shall
	 * be evaluated, and, optionally, zone name, which defaults to the name of
	 * local time zone.
	 *
	 * @param bool|int $timestamp Timestamp to use for evaluation
	 * @param string $zone Timezone name, from which to calculate offset
	 *
	 * @return float Timezone offset from GMT in hours
	 */
	 public function get_gmt_offset( $timestamp = false, $zone = NULL ) {
		
		if ( NULL === $zone ) {
			$zone = $this->get_local_timezone();
		}
		if ( NULL === ( $offset = $this->_gmt_offsets->get( $zone ) ) ) {
			$timezone  = $this->_get_timezone( $zone );
			$reference = $this->_date_time_from_timestamp( $timestamp );
			if ( false === $timezone || false === $reference ) {
				$offset = get_option( 'gmt_offset' );
			} else {
				$offset = round( $timezone->getOffset( $reference ) / 3600, 2);
			}
			unset( $timezone, $reference );
			$this->_gmt_offsets->set( $zone, $offset );
		}
		return $offset;
	}

	/**
	 * Returns human-readable version of the GMT offset.
	 *
	 * @param bool|int $timestamp Timestamp to use for evaluation
	 * @param string $zone Timezone name, from which to calculate offset
	 *
	 * @return string           GMT offset expression
	 */
	 public function get_gmt_offset_expr(
		$timestamp = false, $zone = NULL
	) {
		$timezone = self::get_gmt_offset( $timestamp, $zone );
		$timezone = sprintf(
			__( 'GMT%+d:%02d', AI1EC_PLUGIN_NAME ),
			intval( $timezone ),
			( abs( $timezone ) * 60 ) % 60
		);

		return $timezone;
	}


	/**
	 * get_local_offset_from_gmt method
	 *
	 * Get local timezone offset from GMT in seconds.
	 * NOTICE: this uses assumption, that within the same day the timezone will
	 * remain the same (the DST happens after midnight, so any timezone should
	 * round over to it).
	 *
	 * @param int $timestamp Timestamp at which difference must be evaluated
	 *
	 * @return int Local timezone offset from GMT
	 *
	 * @var Ai1ec_Memory_Utility $offsets Instance of UTC offsets storage
	 *
	 * @throws Ai1ec_Date_Exception If local timezone is invalid
	 */
	 public function get_local_offset_from_gmt( $timestamp ) {
		$zone   = $this->get_local_timezone();
		$offset = false;
		try {
			$offset = $this->get_timezone_offset( 'UTC', $zone, $timestamp );
		} catch ( Exception $tz_excpt ) {
			try {
				$offset = $this->get_gmt_offset( $timestamp, $zone ) * 3600;
			} catch ( Exception $gmt_excpt ) {
				throw new Ai1ec_Date_Exception(
					'Invalid local timezone ' . var_export( $zone, true )
				);
			}
		}
		return $offset;
	}

	/**
	 * gmt_to_local method
	 *
	 * Convert timestamp given in GMT to timestamp in local timezone.
	 *
	 * @param int $timestamp Timestamp to convert
	 *
	 * @return int UNIX timestamp in local timezone
	 */
	 public function gmt_to_local( $timestamp ) {
		return $timestamp + self::get_local_offset_from_gmt( $timestamp );
	}

	/**
	 * Get time difference occuring during DST change time
	 *
	 * Return the offset required to add to local time required to
	 * counteract offset introduced by DST during change time.
	 * Only case, when this must return non-zero result is at the
	 * time DST is being changed.
	 *
	 * @param int $timestamp Time for which DST counteraction must be calculated
	 *
	 * @return int Number of seconds to add to local time to counteract DST
	 *             effect when converting to UTC
	 */
	 public function dst_difference( $timestamp ) {
		if (
			NULL === ( $actual = $this->_dst_differences->get( $timestamp ) )
		) {
			$local_tz       = $this->get_local_timezone();
			$tz_object      = $this->_get_timezone( $local_tz );
			$transitions    = $tz_object->getDetailedTransitions( $timestamp );
			$dst_offset     = absint( $transitions['curr']['offset'] );
			$dst_length     = $transitions['curr']['offset'] -
				$transitions['prev']['offset'];
			if ( $transitions['curr']['offset'] > 0 ) {
				$dst_next_diff = $transitions['next']['ts'] - $timestamp;
				$abs_length    = absint( $dst_length );
				$dst_offset   += $dst_length;
				if (
					$dst_next_diff >  $dst_offset &&
					$dst_next_diff <= $abs_length
				) {
					$actual = $dst_length;
				}
			} else {
				$dst_start_diff = $timestamp - $transitions['curr']['ts'];
				$dst_offset    += $dst_length;
				if (
					$dst_start_diff >= $dst_length &&
					$dst_start_diff <  $dst_offset
				) {
					$actual = $dst_length;
				}
			}
			if ( NULL === $actual ) {
				$actual = 0;
			}
			$this->_dst_differences->set( $timestamp, $actual );
		}
		return $actual;
	}

	/**
	 * local_to_gmt method
	 *
	 * Convert timestamp given in local timezone to GMT timestamp.
	 *
	 * @param int $timestamp Timestamp to convert
	 *
	 * @return int UNIX timestamp in GMT
	 */
	 public function local_to_gmt( $timestamp ) {
		$gmtized    = $timestamp - self::get_local_offset_from_gmt(
				$timestamp
			);
		// {$gmtized} used intentionally as TZs are set on GMT basis
		$dst_offset = self::dst_difference( $gmtized );
		$result     = $gmtized - $dst_offset;
		return $result;
	}

	/**
	 * get_timezone_offset method
	 *
	 * Get difference, in seconds, between two given timezones at specified time
	 * given as UNIX timestamp (when not provided - current timestamp is used).
	 *
	 * @param string $remote_tz Remote timezone, to calculate offset from
	 * @param string $origin_tz Origin timezone, to calculate offset to
	 * @param bool|int $timestamp Reference timestamp, at which offset shall be
	 *                          evaluated [optional=false]
	 *
	 * @return int Difference, in seconds, between timezones
	 *
	 */
	 public function get_timezone_offset(
		$remote_tz,
		$origin_tz = NULL,
		$timestamp = false
	) {
		$use_key         = ( (string)$remote_tz ) . '@*@' .
			( (string)$origin_tz ) . '@*@' . ( (string)$timestamp );
		if ( NULL === ( $offset = $this->_timezone_offsets->get( $use_key ) ) ) {
			if ( NULL === $origin_tz ) {
				$origin_tz = $this->get_default_timezone();
			}
			if ( $remote_tz === $origin_tz ) {
				$offset = 0;
			} else {
				$remote_zone_obj = $this->_get_timezone( $remote_tz );
				$origin_zone_obj = $this->_get_timezone( $origin_tz );
				$reference_time  = $this->_date_time_from_timestamp( $timestamp );
				$offset          = $origin_zone_obj->getOffset(
						$reference_time
					) - $remote_zone_obj->getOffset( $reference_time );
			}
			$this->_timezone_offsets->set( $use_key, $offset );
		}
		return $offset;
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
	 * date_i18n method
	 *
	 * Method to be used in place of `date_i18n()` to improve performance
	 * of date-related operations. Useful when replacing several calls on
	 * same timestamp with different formatting options.
	 *
	 * @param string $format Format string to output timestamp in
	 * @param bool|int $timestamp UNIX timestamp to output in given format
	 * @param bool $is_gmt Set to true, to treat {$timestamp} as GMT
	 *
	 * @return string Formatted date-time entry
	 */
	 public function date_i18n(
		$format,
		$timestamp = false,
		$is_gmt    = true
	) {
		return $this->_time_i18n
			->format( $format, $timestamp, $is_gmt );
	}

	/**
	 * current_time method
	 *
	 * Get current time UNIX timestamp.
	 *
	 * @param bool $is_gmt Set to true to return GMT timestamp
	 *
	 * @return int Current time UNIX timestamp
	 */
	 public function current_time( $is_gmt = false ) {
		return $this->_current_time( $is_gmt );
	}

	/**
	 * _get_timezone method
	 *
	 * Parse given timezone name to DateTimeZone object and cache result in
	 * an in-memory location, to allow faster retrievals on repetitive call
	 * with the same name.
	 *
	 * @param string $name Name of timezone to get TZ object for
	 *
	 * @return DateTimeZone Instance of corresponding TZ object
	 *
	 * @throws Ai1ec_Date_Exception When timezone name is invalid
	 */
	protected function _get_timezone( $name ) {
		if (
			NULL === $name &&
			! ( $name = $this->get_default_tz() )
		) {
			return false;
		}
		if ( NULL === ( $zone = $this->_timezones->get( $name ) ) ) {
			try {
				$zone = new Ai1ec_Date_Time_Zone_Utility( $name );
			} catch ( Exception $excpt ) {
				$zone = false;
			}
			$this->_timezones->set( $name, $zone );
		}
		if ( false === $zone ) {
			throw new Ai1ec_Date_Exception(
				'Invalid timezone ' . var_export( $name, true )
			);
		}
		return $zone;
	}

	/**
	 * normalize_timestamp method
	 *
	 * Interface method to return normalized timestamp value.
	 *
	 * @param bool|int $timestamp Timestamp to normalize
	 * @param bool $is_gmt Define, whereas timestamp is expected to be in GMT
	 *
	 * @return int Normalized timestamp
	 */
	 public function normalize_timestamp(
		$timestamp = false,
		$is_gmt    = false
	) {
		return $this->_normalize_timestamp( $timestamp, $is_gmt );
	}

	/**
	 * Make location-sensitive timestamp modification
	 *
	 * @param string $modifier  Arbitrary modifier instruction, i.e. '+2 days'
	 * @param int    $timestamp Timestamp to modify
	 *
	 * @return int|bool Modified timestamp or false if modifier is invalid
	 *
	 * @uses strtotime To perform actual modification
	 */
	public function modify_timestamp( $modifier, $timestamp ) {
		return strtotime( $modifier, $timestamp );
	}

	/**
	 * _current_time method
	 *
	 * Get current time UNIX timestamp.
	 * Uses in-memory value, instead of re-calling `time()` / `gmmktime()`.
	 *
	 * @param bool $is_gmt Set to true to return GMT timestamp
	 *
	 * @return int Current time UNIX timestamp
	 */
	protected function _current_time( $is_gmt = false ) {
		return $this->_current_time[(int)( (bool)$is_gmt )];
	}

	/**
	 * normalize_timestamp method
	 *
	 * Interface method to return normalized timestamp value.
	 *
	 * @param bool|int $timestamp Timestamp to normalize
	 * @param bool $is_gmt Define, whereas timestamp is expected to be in GMT
	 *
	 * @return int Normalized timestamp
	 */
	protected function _normalize_timestamp(
		$timestamp = false,
		$is_gmt    = false
	) {
		$timestamp = (int)$timestamp;
		if ( 0 === $timestamp ) {
			$timestamp = $this->_current_time( $is_gmt );
		}
		return $timestamp;
	}

	/**
	 * _date_time_from_timestamp method
	 *
	 * Convert timestamp (UNIX timestamp, string value 'now' or boolean false)
	 * to DateTime object.
	 *
	 * @param bool|int $timestamp Timestamp to convert [optional=false]
	 *
	 * @throws Ai1ec_Date_Exception
	 * @return DateTime Instance of corresponding DateTime object
	 *
	 */
	protected function _date_time_from_timestamp( $timestamp = false ) {
		$timestamp = $this->_normalize_timestamp( $timestamp, true );
		$datetime  = NULL;
		try {
			$datetime = new DateTime( '@' . $timestamp );
		} catch ( Exception $excpt ) {
			throw new Ai1ec_Date_Exception(
				'Invalid timestamp ' . var_export( $timestamp, true )
			);
		}
		return $datetime;
	}

	/**
	 * Constructor
	 *
	 * Initialize properties to default values, that may result in better
	 * performance, than delaying this until actual usage.
	 *
	 * @return \Ai1ec_Time_Utility Constructor does not return
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
        $this->_registry = $registry;

		$this->_timezones        = Ai1ec_Memory_Utility::instance(
			__CLASS__ . '/timezones'
		);
		$this->_gmt_offsets      = Ai1ec_Memory_Utility::instance(
			__CLASS__ . '/gmt_offsets'
		);
		$this->_gmtdates         = Ai1ec_Memory_Utility::instance(
			__CLASS__ . '/gmt_dates'
		);
		$this->_timezone_offsets = Ai1ec_Memory_Utility::instance(
			__CLASS__ . '/timezone_offset'
		);
		$this->_dst_differences  = Ai1ec_Memory_Utility::instance(
			__CLASS__ . '/timezone_offset'
		);
		// we require PHP v.5.2.0 minimum, but let's leave this
		// check in place, for the sake of general truthfulness
		$gmt_time = ( version_compare( PHP_VERSION, '5.1.0' ) >= 0 )
			? time()
			: gmmktime();
		$this->_current_time     = array(
			(int)$_SERVER['REQUEST_TIME'],
			$gmt_time,
		);
		$this->_time_i18n        = new Ai1ec_Time_I18n_Utility();
	}
}
