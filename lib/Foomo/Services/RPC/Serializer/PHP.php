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
class PHP implements SerializerInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const TYPE = 'serviceTypePhp';

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
	 * @param mixed $call
	 * @return string serialized data
	 */
	public function serialize($call)
	{
		return \serialize($call);
	}

	/**
	 * @param string $serialized
	 * @return mixed unserialized call
	 */
	public function unserialize($serialized)
	{
		$ret = @\unserialize($serialized);
		if ($ret === false) {
			trigger_error(__METHOD__ . ' could not unserialize data >>>' . $serialized . '<<<', \E_USER_WARNING);
		}
		return $ret;
	}

	/**
	 * @todo check, if this is right
	 * @return string
	 */
	public function getContentMime()
	{
		return 'text/plain';
	}

	/**
	 * @return boolean
	 */
	public function supportsTypes()
	{
		return true;
	}
}
