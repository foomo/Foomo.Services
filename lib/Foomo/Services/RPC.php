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

namespace Foomo\Services;

use Foomo\Services\RPC\Serializer\SerializerInterface;
use Foomo\Services\RPC\Serializer\PHP;
use Foomo\Services\RPC\Serializer\AMF;

/**
 * Me so restless today ...
 *
 * serve shit good old ugly rpc style
 * currently the proxies are bound to serializers - that might change in the future
 */
class RPC
{
	//---------------------------------------------------------------------------------------------
	// ~ Static variables
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @var messages for the current call
	 */
	public static $messages = array();

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * serve a class as a service
	 *
	 * @param stdClass $serviceClassInstance object that provides the services functionality
	 * @param Foomo\Services\RPC\Serializer\SerializerInterface $serializer name of the class that handles the service wrapping, marshalling ...
	 * @param string $package package of a proxy
	 */
	public static function serveClass($serviceClassInstance, SerializerInterface $serializer, $package=null)
	{
		echo \Foomo\MVC::run(new RPC\Frontend($serviceClassInstance, $serializer, $package));
	}

	/**
	 * @param mixed $message
	 */
	public static function addMessage($message)
	{
		self::$messages[] = $message;
	}

	/**
	 * @todo what's that supposed to do?
	 *
	 * @param type $statusUpdate
	 */
	public static function addStatusUpdate($statusUpdate)
	{

	}
}