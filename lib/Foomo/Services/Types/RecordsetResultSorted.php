<?php

namespace Foomo\Services\Types;

abstract class RecordsetResultSorted extends RecordsetResult {
	/**
	 * sorting order in which the startIndex and endIndex has to be seen
	 *
	 * @var string
	 */
	public $order;
}