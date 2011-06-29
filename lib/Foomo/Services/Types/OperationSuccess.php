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
 * documents operation success
 *
 */
class OperationSuccess {
	/**
	 * general success code
	 *
	 */
	const CODE_SUCCESS = 0;
	/**
	 * general success message key
	 */
	const MESSAGE_KEY_SUCCESS = 'success';
	/**
	 * general success message
	 */
	const MESSAGE_SUCCESS = 'operation succeeded';
	/**
	 * general failure code
	 *
	 */
	const CODE_FAILURE = 1;
	/**
	 * general failure message key
	 */
	const MESSAGE_KEY_FAILURE = 'failure';
	/**
	 * general failure message
	 */
	const MESSAGE_FAILURE = 'operation failed';
	/**
	 * unix style error code - 0 means success
	 * 
	 * @var integer
	 */
	public $code;
	/**
	 * a programmer firendly message
	 *
	 * @var string
	 */
	public $message;
	/**
	 * a property name for I18n
	 *
	 * @var string
	 */
	public $messageKey;
	/**
	 * cut the crap, thats all I want to know
	 *
	 * @var boolean
	 */
	public $success;
}