<?php

/**
 * Wrapper for `DateTimeZone` to extend it with convenient methods
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Utility
 */

class Ai1ec_Date_Time_Zone_Utility extends DateTimeZone
{

    /**
     * Map of transitions details for given timestamp
     * {@see DateTimeZone::getTransitions()} for representation of details.
     * Return a map of prev(ious), curr(ent) and next transitions for
     * a given timestamp.
     *
     * @NOTICE: if we start accepting PHP 5.3 - update getTransitions
     * usage, to add offsets.
     *
     * @param int $timestamp UNIX timestamp (UTC0) for which to find transitions
     *
     * @return array Map of prev|curr|next transitions
     */
    public function getDetailedTransitions( $timestamp ) {
        $transition_list = $this->getTransitions();
        $output          = array(
            'prev' => NULL,
            'curr' => NULL,
            'next' => NULL,
        );
        $previous = $current = NULL;
        foreach ( $transition_list as $transition ) {
            if (
                NULL !== $previous &&
                $timestamp >= $current['ts'] &&
                $timestamp < $transition['ts']
            ) {
                $output['prev'] = $previous;
                $output['curr'] = $current;
                $output['next'] = $transition;
                break;
            }
            $previous = $current;
            $current        = $transition;
        }
        unset( $previous, $current, $transition_list, $transition );
        return $output;
    }

}
