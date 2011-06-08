<?php

namespace Foomo\Services\RPC\Protocol\Reply;

/**
 * method reply exception
 */
class Exception extends \Exception {
  /**
   * error code
   *
   * @var integer
   */
  public $code;
  /**
   * error message
   *
   * @var string
   */
  public $message;
  /**
   * key for a localized message
   *
   * @var string
   */
  public $messageKey;
  public function __construct($message, $code, $messageKey)
  {
    $this->message = $message;
    $this->messageKey = $messageKey;
    $this->code = $code;
  }
}