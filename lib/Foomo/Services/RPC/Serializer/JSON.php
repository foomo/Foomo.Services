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

namespace Foomo\Services\RPC\Serializer;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class JSON implements SerializerInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const TYPE = 'serviceTypeRpcJson';

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function getType()
	{
		return self::TYPE;
	}

	/**
	 *
	 * @param string $call
	 * @return string
	 */
	public function serialize($call)
	{
		return json_encode($call);
	}

	/**
	 * @param string $serialized
	 * @return string
	 */
	public function unserialize($serialized)
	{
		return json_decode($serialized);
	}

	/**
	 * @return string
	 */
	public function getContentMime()
	{
		return 'application/x-json';
	}

	/**
	 * @return boolean
	 */
	public function supportsTypes()
	{
		return false;
	}
}
