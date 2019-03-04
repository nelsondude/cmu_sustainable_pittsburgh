<?php
/**
 * @package Smart Countdown 3 AJAX server for Joomla! 3.0
 * @version 3.2.6
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined ( '_JEXEC' ) or die ();
class smartCountdown3ControllerEvent extends JControllerLegacy {
	public function getEvent() {
		$app = JFactory::getApplication ();
		$module_id = $app->input->getInt ( 'scd_module_id', 0 );
		
		// get requested module data
		$db = JFactory::getDbo ();
		$db->setQuery ( 'SELECT a.* FROM #__modules AS a WHERE a.id = ' . $module_id );
		try {
			$module = $db->loadObject ();
		} catch ( RuntimeException $e ) {
			self::sendResponse ( null, 500, $e->getMessage () );
			return;
		}
		// check that the module exists
		$params_raw = @$module->params;
		if (empty ( $params_raw )) {
			self::sendResponse ( null, 100, 'Module not found' );
			return;
		}
		
		$params = new JRegistry ();
		$params->loadString ( $params_raw );
		
		// look for events import plugins
		$dispatcher = JDispatcher::getInstance ();
		JPluginHelper::importPlugin ( 'system' );
		$result = $dispatcher->trigger ( 'onCountdownGetEventsQueue', array (
				'mod_smartcountdown3',
				$params 
		) );
		
		if (in_array ( false, $result, true )) {
			self::sendResponse ( null, 101, 'Error processing plugins' );
			return;
		}
		
		// filter out empty result elements (plugin returned true)
		$result = array_filter ( $result, function ($v) {
			return $v !== true;
		} );
		
		$now_micro_ts = microtime ( true );
		$now_ts_millis = round ( $now_micro_ts, 3 ) * 1000;
		$now_ts = round ( $now_micro_ts );
		
		// get counter display modes from options. Here we are interested in
		// "countup limit" only.
		$modes = explode ( ':', $params->get ( 'counter_modes', '-1:-1' ) );
		// we discard special "countdown to end" setting and replace it with auto.
		$countup_limit = $modes [1] < 0 ? - 1 : $modes [1];
		
		if (empty ( $result )) {
			// plugins not enabled for this module instance or system-wide
			// get internal counter
			$deadline = JDate::getInstance ( $params->get ( 'deadline', 'now' ) );
			
			if ($countup_limit >= 0 && $deadline->getTimestamp () + $countup_limit <= $now_ts) {
				$deadline = '';
			} else {
				$deadline = $deadline->format ( 'c' );
			}
			
			$options = array (
					'deadline' => $deadline,
					'countup_limit' => $countup_limit,
					'now' => $now_ts_millis 
			);
		} else {
			// process imported events
			$current_event = self::processImportedEvents ( $result, $countup_limit, $now_ts );
			if (empty ( $current_event )) {
				$options = array (
						'deadline' => '',
						'countup_limit' => '',
						'imported_title' => '',
						'now' => $now_ts_millis 
				);
			} else {
				$options = array (
						'deadline' => $current_event ['deadline'],
						'countup_limit' => $current_event ['countup_limit'],
						'imported_title_down' => $current_event ['imported_title_down'],
						'imported_title_up' => $current_event ['imported_title_up'],
						'is_countdown_to_end' => $current_event ['is_countdown_to_end'],
						'now' => $now_ts_millis 
				);
				// Only add redirect URLs to response if they are defined by event import plugin
				// (when not defined in response, those set in module options will be used)
				if (isset ( $current_event ['click_url'] )) {
					$options ['click_url'] = $current_event ['click_url'];
				}
				if (isset ( $current_event ['redirect_url'] )) {
					$options ['redirect_url'] = $current_event ['redirect_url'];
				}
			}
		}
		self::sendResponse ( $options );
	}
	private static function processImportedEvents($result, $countup_limit, $now_ts) {
		if (empty ( $result )) {
			return false;
		}
		
		// Plain events array
		$events = array ();
		
		// merge events from all providers. For now there is no difference which
		// import plugin events comes from
		foreach ( $result as $group ) {
			foreach ( $group as $event ) {
				$events [] = $event;
			}
		}
		
		// Structured events. Each deadline will be an array of events, keyed and sorted
		// by their end time
		$timeline = array ();
		
		foreach ( $events as &$event ) {
			$event_start_ts = $event ['deadline'];
			
			// calculate event end time
			if ($countup_limit > 0) {
				// explicit up limit, don't depend on duration
				$event_end_ts = $event_start_ts + $countup_limit;
			} elseif ($countup_limit == - 1) {
				// automatic up limit, use event duration as is
				$event_end_ts = $event_start_ts + $event ['duration'];
			} else {
				// for countup_limit == 0 we leave event end = event start,
				// i.e. duration 0
				$event_end_ts = $event_start_ts;
			}
			
			// discard finished events. If events are finished we break here and no
			// event_start_ts group will be create in the timeline
			if ($event_end_ts <= $now_ts) {
				continue;
			}
			
			// set effective event duration
			$event ['duration'] = $event_end_ts - $event_start_ts;
			
			// create group by start time (if not exists)
			if (! isset ( $timeline [$event_start_ts] )) {
				$timeline [$event_start_ts] = array ();
			}
			
			// make sure we have unique $event_end_ts key: otherwise if there are fully overlapping
			// events the last event data will overwrite the previous one(s) which will be lost
			while ( isset ( $timeline [$event_start_ts] [$event_end_ts] ) ) {
				$event_end_ts = '0' . $event_end_ts;
			}
			
			// add event to timeline
			$timeline [$event_start_ts] [$event_end_ts] = $event;
		}
		
		// we have our timeline array of arrays, no need for plain events array any more
		unset ( $events );
		
		// Sort events by end time
		foreach ( $timeline as &$group ) {
			// sort - shortest events should come first
			
			/*
			 * using the '0'.$key trick we solve the problem of multiple fully-overlapping events:
			 * the first full-overlapping is added by $key as is, the second - by '0'.$key, the third -
			 * by '00'.$key, etc. BUT after sorting those having more heading zeros come first, thus
			 * effectively reversing the order!
			 */
			// user-defined sort function: for numerically distinct values
			// we compare numerically, for zero-padded trick values we compare string length -
			// very easy but effective - the only difference is the number of zeros prepended
			// to the value, so this simple will do the trick - the shortest (i.e. added first)
			// will come first in the sorted array.
			uksort ( $group, function ($a, $b) {
				if (intval ( $a ) == intval ( $b )) {
					return strlen ( $a ) > strlen ( $b ) ? 1 : (strlen ( $a ) < strlen ( $b ) ? - 1 : 0);
				} else {
					return intval ( $a ) > intval ( $b ) ? 1 : (intval ( $a ) < intval ( $b ) ? - 1 : 0);
				}
			} );
		}
		
		// Sort events by start time
		ksort ( $timeline, SORT_NUMERIC );
		
		// normally event import plugins will fetch only valid events,
		// just in case the timeline is empty, we simulate "no events found"
		if (empty ( $timeline )) {
			return false;
		}
		
		// here we have all events grouped and sorted by start time,
		// each group is sorted by event duration (both sort orders - ASC)
		
		// get deadline timestamps
		$start_times = array_keys ( $timeline );
		
		// if more than 1 group starts in the past we have to discard all
		// except the last one
		foreach ( $start_times as $i => $timeline_key ) {
			if ($timeline_key < $now_ts && isset ( $start_times [$i + 1] ) && $start_times [$i + 1] < $now_ts) {
				unset ( $timeline [$timeline_key] );
			}
		}
		$start_times = array_keys ( $timeline );
		
		// at this point we need only fist two groups:
		// the first one is our target deadline, and the next will
		// provide countup limit if there are events in the first
		// group that last after the second group start time
		
		$deadline_ts = $start_times [0];
		if (isset ( $start_times [1] )) {
			$max_countup_limit = $start_times [1] - $deadline_ts;
		}
		
		// we need to prepare some data
		$counter_events = array_values ( reset ( $timeline ) );
		// events are sorted by start time / duration in timeline, so the first
		// event in "start" group will be the shortest one
		$shortest_event = $counter_events [0];
		// get effective countup_limit
		$counter_countup_limit = $countup_limit < 0 || $countup_limit > $shortest_event ['duration'] ? $shortest_event ['duration'] : $countup_limit;
		if (isset ( $max_countup_limit ) && $counter_countup_limit > $max_countup_limit) {
			// sanitize countup_limit
			$counter_countup_limit = $max_countup_limit;
		}
		
		// event import plugins can be set up to import event titles:
		// common titles - should be displayed both before event and when event has started
		// per-mode titles - one for countdown mode and the other for count up or countdown-to-end
		
		// we maintain 2 array for all simultaneos events. If an event imported has per-mode
		// titles set we add each title to the corresponding concatenation array,
		// otherwise (common titles) event title will be added to both concat arrays.
		$concat_title_down = array ();
		$concat_title_up = array ();
		
		// looping through events with same deadlines we must keep track of
		// "countdown-to-end" (CTE) events. We can never mix normal and CTE events in the
		// same counter because event description will be wrong for one of the events:
		// E.g. if another event starts at the same time as current CTE event ends,
		// showing both event titles as "... Will finish in" would be absolutely wrong!
		// So we prepare and empty array for CTE events. Later we will completely
		// reconstruct event description if at least one CTE event is found.
		$countdown_to_end_events = array ();
		
		$redirect_url = null;
		$click_url = null;
		
		// iterate through events - construct title proposal and detect
		// countdown_to_end events
		foreach ( $counter_events as &$event ) {
			// update concatenation arrays
			self::concatTitles ( $concat_title_down, $concat_title_up, $event );
			
			// if there are more than 1 current evetns, each one can define its own
			// redirection URL. We must resolve this conflict for both auto-redirect and click:
			// the first event in list must win. Links in event titles will work OK even if
			// multiple events are listed.
			if (empty ( $redirect_url ) && ! empty ( $event ['redirect_url'] )) {
				$redirect_url = $event ['redirect_url'];
			}
			if (empty ( $click_url ) && ! empty ( $event ['click_url'] )) {
				$click_url = $event ['click_url'];
			}
			
			// special case - countdown-to-end event
			if (! empty ( $event ['is_countdown_to_end'] )) {
				// we have met a countdown-to-end event
				$countdown_to_end_events [] = $event;
			}
		}
		
		if (! empty ( $countdown_to_end_events )) { // at least one element is a "countdown-to-end"
		                                            // in CTE mode we force event duration to zero
			$counter_countup_limit = 0;
			
			// reconstruct concatenated titles - even if another event starts at the same moment as
			// a certain event end, we only include CTE event titles only!
			$concat_title_down = array ();
			$concat_title_up = array ();
			
			foreach ( $countdown_to_end_events as $event ) {
				// update concatenation arrays
				self::concatTitles ( $concat_title_down, $concat_title_up, $event );
			}
			$is_countdown_to_end = 1;
		} else {
			$is_countdown_to_end = 0;
		}
		
		// start clean data structure
		$event = array ();
		
		// join titles to a string (may be empty string if no titles found)
		$concat_title_down = implode ( ', ', $concat_title_down );
		$event ['imported_title_down'] = $concat_title_down;
		$concat_title_up = implode ( ', ', $concat_title_up );
		$event ['imported_title_up'] = $concat_title_up;
		
		// $event['imported_title'] = ''; //deprecated
		
		$deadline = new DateTime ();
		$deadline->setTimestamp ( $deadline_ts );
		$event ['deadline'] = $deadline->format ( 'c' );
		$event ['is_countdown_to_end'] = $is_countdown_to_end;
		if (! empty ( $redirect_url )) {
			$event ['redirect_url'] = $redirect_url;
		}
		if (! empty ( $click_url )) {
			$event ['click_url'] = $click_url;
		}
		$event ['countup_limit'] = $counter_countup_limit;
		
		return $event;
	}
	private static function concatTitles(&$concat_title_down, &$concat_title_up, $event) {
		if (isset ( $event ['title_down'] ) && trim ( $event ['title_down'] ) != '') {
			$concat_title_down [] = $event ['title_down'];
		} elseif (isset ( $event ['title'] ) && trim ( $event ['title'] ) != '') {
			$concat_title_down [] = $event ['title'];
		}
		if (isset ( $event ['title_up'] ) && trim ( $event ['title_up'] ) != '') {
			$concat_title_up [] = $event ['title_up'];
		} elseif (isset ( $event ['title'] ) && trim ( $event ['title'] ) != '') {
			$concat_title_up [] = $event ['title'];
		}
	}
	private static function sendResponse($options = array('deadline' => ''), $err_code = 0, $err_msg = '') {
		$response = array (
				'err_code' => $err_code,
				'err_msg' => $err_msg,
				'options' => $options 
		);
		
		// clear output buffer to suppress warning and notices
		while ( ob_get_clean () )
			;
		
		echo json_encode ( $response );
		JFactory::getApplication ()->close ();
	}
}