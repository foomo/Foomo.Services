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

namespace Foomo\Services\Reflection;

/**
 * gives meta information, when generating service proxy clients, so that a
 * local class / value object can be mapped to a remote one
 *
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class RemoteClass extends \Annotation
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * if there is an existing class on the actionscript client class add the name of it here - the client is on the remote side
	 * @example org.foomo.bla.Blubb
	 *
	 * @var string
	 */
	public $name;
	/**
	 * if you want the ValueObject to go into a specific package -  sth. like
	 *
	 * @example org.foomo.serverObjects
	 *
	 * @var string
	 */
	public $package;
}