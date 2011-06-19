<?php

namespace Foomo\Services\Reflection;

/**
 * gives meta information, when generating service proxy clients, so that a
 * local class / value object can be mapped to a remote one
 *
 */
class RemoteClass extends \Annotation {
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