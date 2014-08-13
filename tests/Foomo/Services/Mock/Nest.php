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

namespace Foomo\Services\Mock;

/**
 * a mock object to test nested reflection of doc comments
 *
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class Nest
{
	const TREE_TYPE_SPRUCE = 'spruce';
	const TEST_FLOAT = 1.2;
	const TEST_BOOL = true;
	const TEST_INT = 123;
	const TEST_ESCAPE_STRING = "\"FOO\nOK";
	const TREE_TYPE_OAK = 'oak';
	/**
	 * name of my tree
	 *
	 * @var string
	 */
	public $treeType = self::TREE_TYPE_SPRUCE;
	/**
	 * a pretty sleepy one
	 *
	 * @var Nest\Bird
	 */
	public $bird;
}