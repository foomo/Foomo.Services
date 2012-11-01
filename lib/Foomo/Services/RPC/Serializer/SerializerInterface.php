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
 * streamline RPC calls
 *
 */
interface SerializerInterface {
    /**
     * your type for service descriptions
     *
     * @return string
     */
    public function getType();
	/**
	 * serialize
	 *
	 * @param mixed $var
	 * 
	 * @return string serialized data
	 */
	public function serialize($call);
	/**
	 * unserialize
	 *
	 * @param string $serialized
	 * 
	 * @return mixed unserialized call
	 */
	public function unserialize($serialized);
	/**
	 * what is your mime type
	 */
	public function getContentMime();
	/**
	 * tells, if this serializer supports types or not
	 * 
	 * @return boolean
	 */
	public function supportsTypes();
}
