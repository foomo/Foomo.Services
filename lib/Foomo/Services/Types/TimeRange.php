<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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