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
 * standard paging record set for value objects in services
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
abstract class RecordsetResult
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

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