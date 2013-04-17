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

namespace Foomo\Services\RPC;

use ReflectionClass;
use Foomo\Utils;
use Foomo\Timer;
use Foomo\Config;
use Foomo\Cache\Proxy;
use Foomo\Log\Logger;
use Foomo\Reflection\PhpDocEntry;
use Foomo\Services\RPC\Serializer\SerializerInterface;
use Foomo\Services\RPC\Protocol\Reply;
use Foomo\Services\RPC\Protocol\Reply\Head as ReplyHead;
use Foomo\Services\RPC\Protocol\Call;
use Foomo\Services\RPC\Protocol\Reply\Exception as ReplyException;
use Foomo\Services\RPC\Protocol\Reply\MethodReply;
use Foomo\Services\RPC;

/**
 * handle a service call
 *
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class Server
{
	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * serve a RPC service call
	 *
	 * @param stdClass $serviceClassInstance instance of the class which is supposed to handle the call
	 * @param \Foomo\Services\RPC\Serializer\SerializerInterface $serializer
	 * @param string $input
	 *
	 * @return string serialized Foomo\Services\RPC\Protocol\Reply
	 */
	public static function run($serviceClassInstance, SerializerInterface $serializer, $input) {
		// is the serviceClassInstance an Object
		if(!is_object($serviceClassInstance)) {
			throw new \Exception('$serviceClassInstance must be an object');
		}

		// unserialize the incoming call with the given serializer and check if it is a RPCCallMethodCall
		$call = $serializer->unserialize($input);
		if(!($call instanceof Call)) {
			trigger_error(__METHOD__ . ' there was an error, when unserializing with ' . get_class($serializer) . ' :', E_USER_WARNING);
			Utils::appendToPhpErrorLog(
				'---------------------- $input ----------------------' . PHP_EOL .
				$input .
				'---------------------- unserialized $call ----------------------' . PHP_EOL .
				serialize($call) . PHP_EOL
			);
			throw new \Exception('the given $input turned out not to be a RPCCall after it was deserialized with ' . get_class($serializer));
		}

		// checking if the exposed class is being called
		if(!($serviceClassInstance instanceof $call->head->className)) {
			throw new \Exception('$serviceClassInstance must be an instance of ' . $call->head->className);
		}

		//compare client and service versions
		$serviceClassVersion = constant(get_class($serviceClassInstance).'::VERSION');
		if($call->head->classVersion != $serviceClassVersion) {
			throw new \Exception('wrong client version ' . $call->head->classVersion . ' expected ' . $serviceClassVersion . ' and got called with ' . $call->head->classVersion);
		}

		// set up the reply
		$serviceReply = new Reply;
		$serviceReply->head = new ReplyHead;
		$serviceReply->head->callId = $call->head->callId;
		$serviceReply->head->sessionId = session_id();
		// get to work through all the method calls
		foreach($call->calls as $methodCall) {
			/* @var $methodCall Call\MethodCall */
			$serviceReply->methodReplies[] = self::callMethod($serviceClassInstance, $methodCall, $serializer);
		}
		// serialize it and give it back
		$ret = $serializer->serialize($serviceReply);
		// trigger_error(urlencode($ret));
		return $ret;
	}

	/**
	 *
	 * @param stdClass $serviceClassInstance
	 * @param \Foomo\Services\RPC\Protocol\Call\MethodCall $methodCall
	 * @param SerializerInterface $serializer
	 *
	 * @return MethodReply
	 */
	public static function callMethod($serviceClassInstance, Call\MethodCall $methodCall,  SerializerInterface $serializer)
	{
        $callNotice = get_class($serviceClassInstance) . '->' . $methodCall->method . '() with ' . count($methodCall->arguments) . ' args';
        Timer::start($callNotice);

		RPC::$messages = array();
		$reply = new MethodReply;
		$reply->id = $methodCall->id;

		try {
			// try to call the given method with the given parameters
			if(!method_exists($serviceClassInstance, $methodCall->method)) {
				throw new ReplyException('method ' . $methodCall->method .' does not exist on ' . get_class($serviceClassInstance), 1, 'methodDoesNotExist');
			}
			//$reply->value = call_user_func_array(array($serviceClassInstance, $methodCall->method), $methodCall->arguments);
			Logger::transactionBegin($transactionName = 'RPC service call ' . get_class($serviceClassInstance) . '->' . $methodCall->method);

			if(!$serializer->supportsTypes()) {
				// @todo depending upon the serializer data must be casted
				// i.e. json does not support types => cast from hash to object
				// based on reflection
			}
            $useArgs = array();
            $sortArgs = false;
            // this is a security measure for regression to older clients, that do not conform the protocol correctly
            foreach($methodCall->arguments as $arg) {
                if($arg instanceof \Foomo\Services\RPC\Protocol\Call\MethodArgument) {
                    /* @var $arg \Foomo\Services\RPC\Protocol\Call\MethodArgument */
                    $sortArgs = true;
                    $useArgs[$arg->name] = $arg->value;
                } else {
                    $useArgs[] = $arg;
                }
            }
            if($sortArgs) {
                $sortedArgs = array();
                $methodReflection = new \ReflectionMethod($serviceClassInstance, $methodCall->method);
                foreach($methodReflection->getParameters() as $reflParameter) {
                    $sortedArgs[] = $useArgs[$reflParameter->getName()];
                }
                $useArgs = $sortedArgs;
            }
			$reply->value = Proxy::call($serviceClassInstance, $methodCall->method, $useArgs);
			Logger::transactionComplete($transactionName);
		} catch(\Exception $e) {
			// is it an exception, that was expected i.e. phpDocumented
			// look up the method documentation
			$ref = new ReflectionClass($serviceClassInstance);
			$methods = $ref->getMethods();
			$throwTypes = array();
			foreach ($methods as $method) {
				/* @var $method \ReflectionMethod */
				$methodName = $method->getName();
				if($methodName == $methodCall->method) {
					$phpDoc = new PhpDocEntry($method->getDocComment(), $method->getDeclaringClass()->getNamespaceName());
					foreach($phpDoc->throws as $throwType) {
						$throwTypes[] = $throwType->type;
					}
				}
			}
			Logger::transactionAbort($transactionName, get_class($e) . ' ' . $e->getMessage());
			if(in_array(get_class($e), $throwTypes)) {
				// expected exception
				$reply->exception = $e;
				Timer::addMarker(__METHOD__ . ' expected exception :' . get_class($e));
			} else {
				// unexpected exception
				$reply->exception = new ReplyException('an error occured on the server', '1', 'serverError');
				if(in_array(Config::getMode(), array(Config::MODE_DEVELOPMENT, Config::MODE_TEST))) {
					// be verbose in dev and debug
					$reply->exception->code = $e->getCode();
					$reply->exception->message = $e->getMessage();
				} else {
					// ensure that nothing leaks in production
					$reply->exception->code = '1';
				}
				trigger_error(
					__METHOD__ . ' an unexpected exception was thrown, when I tried to call ' . $callNotice . ' :: type : ' . get_class($e) . ' message : ' . $e->getMessage() . PHP_EOL .
					' stack trace: ' . PHP_EOL . $e->getTraceAsString() . PHP_EOL
					, E_USER_WARNING
				);
				Utils::appendToPhpErrorLog(serialize($methodCall));
				Timer::addMarker(__METHOD__ . ' exception (unexpected) :' . get_class($e));
			}
		}
		$reply->messages = RPC::$messages;
        Timer::stop($callNotice);
		return $reply;
	}
}
