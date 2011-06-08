<?php
namespace Foomo\Services\Types;

/**
 * standard paging record set for value objects in services
 *
 */
abstract class RecordsetResult {
	/**
	 * first record index in the complete result scope
	 * the very first possible index is 0
	 *
	 * @var integer
	 */
	public $startIndex;
	/**
	 * index of the last element in items
	 * 
	 * @var integer
	 */
	public $endIndex;
	/**
	 * how many items are in the total recordset
	 *
	 * @var integer
	 */
	public $totalRecords;
	/**
	 * page size - max records per page
	 * 
	 * @var integer
	 */
	public $pageSize;
	/**
	 * abstract contents type them for (SOAP) webservices
	 *
	 * @var array
	 */
	public $items;
}