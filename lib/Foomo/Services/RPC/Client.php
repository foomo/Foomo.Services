<?php

namespace Foomo\Services\RPC;

use Foomo\Services\RPC\Serializer\SerializerInterface;

/**
 * a little client to perform RPC calls
 *
 */
class Client {
	/**
	 * my serializer
	 *
	 * @var RPCSerializerInterface
	 */
	protected $serializer;
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	protected $targetClass;
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	protected $endPoint;
	/**
	 * that is where the cookies for curl get stored
	 *
	 * @var string
	 */
	private $cookies = array();
	/**
	 * the currently returned reply - see 
	 * 
	 * @var Foomo\Services\RPC\Protocol\Reply\MethodReply
	 */
	public $currentReply;
	/**
	 * construct a client
	 *
	 * @param RPCSerializerInterface $serializer
	 * @param string $targetClass name of the class to talk to
	 * @param string $endPoint uri of the service
	 */
	public function __construct(SerializerInterface $serializer, $targetClass, $endPoint)
	{
		$this->serializer = $serializer;
		$this->targetClass = $targetClass;
		$this->endPoint = $endPoint;
	}
	protected function callServer($clientVersion, $name, $arguments)
	{
		$serialized = $this->serializer->serialize($this->getRequestForSimpleCall($clientVersion, $name, $arguments));
		$reply = $this->serializer->unserialize($rawReply = $this->post($serialized));
		/* @var $reply RPCCallReply */
		$methodReply = $reply->methodReplies[0];
		$this->currentReply = $methodReply;
		/* @var $methodReply RPCCallMethodReply */
		if(isset($methodReply->exception)) {
			throw $methodReply->exception;
		}
		return $methodReply->value;
	}

	public function parallelCall($methodCalls)
	{
		$clientVersion = constant(get_called_class() . '::VERSION');
		$postDataArray = array();
		foreach($methodCalls as $methodCall) {
			$request = new Protocol\Call();
			$request->head = new Protocol\Call\Head();
			$request->head->callId = 0;
			$request->head->classVersion = $clientVersion;
			$request->head->className = $this->targetClass;
			$request->calls = array($methodCall);
			$postDataArray[] = $request;

		}
		$resultData = $this->multiPost($postDataArray);
		$ret = array();
		foreach($resultData as $serialized) {
			$reply = $this->serializer->unserialize($serialized);
			if(false !== $reply) {
				$ret[] = $reply->methodReplies[0];
			} else {
				trigger_error('server did not reply properly', E_USER_WARNING);
				$ret[] = false;
			}
		}
		return $ret;
	}

	private function getRequestForSimpleCall($clientVersion, $name, $arguments)
	{
		$request = new Protocol\Call();
		$request->head = new Protocol\Call\Head();
		$request->head->callId = 0;
		$request->head->classVersion = $clientVersion;
		$request->head->className = $this->targetClass;
		$methodCall = new Protocol\Call\MethodCall();
		$methodCall->arguments = $arguments;
		$methodCall->id = 0;
		$methodCall->method = $name;
		$request->calls[] = $methodCall;
		return $request;
	}
	/**
	 * it is all so magic ...
	 *
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, $arguments)
	{
		return $this->callServer(null, $name, $arguments);
	}
	/**
	 * drop the session
	 */
	public function deleteCookies()
	{
		$this->cookies = array();
	}
	/**
	 * explictly set a cookie file
	 *
	 * @param array $filename array('cookieName' => 'value')
	 */
	public function setCookies($cookies)
	{
		$this->cookies = $cookies;
	}
	/**
	 * get the current cookiefile
	 *
	 * @return string filename
	 */
	public function getCookies()
	{
		return $this->cookies;
	}
	/**
	 * another stupid post implementation
	 *
	 * @param string $data string to post
	 * @return string
	 */
	private function post($data)
	{
		$ch = $this->getHandleForPost($data);
		$result = curl_exec($ch);

		if($result === false) {
			curl_close($ch);
			trigger_error('a curl error occurred ' . curl_error($ch), E_USER_ERROR);
		} else {
			$replyData = $this->extractData($result, $ch);
			foreach($replyData['cookies'] as $cookieName => $cookieValue) {
				$this->cookies[$cookieName] = $cookieValue;
			}
			curl_close($ch);
			return $replyData['body'];
		}
	}

	private function extractData($result, $ch)
	{
		$headerLength = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($result, 0, $headerLength);
		$body = substr($result, $headerLength);
		return array('body' => $body, 'cookies' => $this->extractSetCookies($header), 'header' => $header);
	}

	private function getHandleForPost($data)
	{
		$ch = curl_init();

		$url = $this->endPoint;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		// $post = 'call=' . urlencode($data);
		// curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);//array('call' => $data));
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('call' => $data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(count($this->cookies)>0) {
			$cookies = array();
			foreach($this->cookies as $cookieName => $cookieValue) {
				$cookies[] = $cookieName . '=' . $cookieValue;
			}
			curl_setopt ($ch, CURLOPT_COOKIE, implode('; ', $cookies));
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		return $ch;
	}
	private function multiPost($dataArray)
	{

        $mh = curl_multi_init();

        // start the first batch of requests
		$chArray = array();
        foreach($dataArray as $data) {
			$currentCh = $this->getHandleForPost($this->serializer->serialize($data));
			$chArray[] = $currentCh;
            curl_multi_add_handle($mh, $currentCh);
        }

		$bodies = array();

		do {
			$status = curl_multi_exec($mh, $active);
			// $info = curl_multi_info_read($mh);
		} while ($status === CURLM_CALL_MULTI_PERFORM || $active);

		foreach($chArray as $currentCh) {
			$result = curl_multi_getcontent($currentCh);
			$replyData = $this->extractData($result, $currentCh);
			foreach($replyData['cookies'] as $cookieName => $cookieValue) {
				$this->cookies[$cookieName] = $cookieValue;
			}
			curl_close($currentCh);
			$bodies[] = $replyData['body'];

		}
        curl_multi_close($mh);
        return $bodies;
	}
	private function extractSetCookies($headers)
	{
		$cookies = array();
		$lines = explode(chr(10), $headers);
		foreach($lines as $line) {
			if(strpos($line, 'Set-Cookie:') === 0) {
				$cookieLine = trim(substr($line, strlen('Set-Cookie:')));
				$cookieParts = explode(';', $cookieLine);
				$cookieParts = explode('=', trim($cookieParts[0]));
				$cookies[$cookieParts[0]] = $cookieParts[1];

			}
		}
		return $cookies;
	}
}