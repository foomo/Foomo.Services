<?php
namespace Foomo\Services\Types;

/**
 * Class to model Time Ranges with one Start- and one End-time
 */
class TimeRange {
	/**
	 * The start time as UNIX time stamp
	 * If 0, then filter for time range from lowest values to end
	 *
	 * @var integer
	 */
	public $start;
	
	/**
	 * The end time as UNIX time stamp
	 * If 0, then filter for time range from start to highest value
	 *
	 * @var integer
	 */
	public $end;
	
	/**
	 * Returns the length of the Time Span in seconds
	 * if both values are set
	 * otherwise returns 0
	 *
	 * @return integer
	 */
	public function getTimeSpan() {
		if ($this->end != null and $this->start != null) {
			return ($this->end - $this->start);
		}
		return 0;
	}
	
}