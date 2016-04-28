<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendServiceTest\Apple\Apns\TestAsset;

use ZendService\Apple\Apns\Exception;
use ZendService\Apple\Apns\Client as ZfMessageClient;

/**
 * Message Client Proxy
 * This class is utilized for unit testing purposes
 *
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage Apns
 */
class Client extends ZfMessageClient
{
    /**
     * Read Response
     *
     * @var string
     */
    protected $readResponse;

    /**
     * Write Response
     *
     * @var mixed
     */
    protected $writeResponse;

    /**
     * Set the Response
     *
     * @param  string        $str
     * @return Client
     */
    public function setReadResponse($str)
    {
        $this->readResponse = $str;

        return $this;
    }

    /**
     * Set the write response
     *
     * @param  mixed         $resp
     * @return Client
     */
    public function setWriteResponse($resp)
    {
        $this->writeResponse = $resp;

        return $this;
    }

    public function send(\ZendService\Apple\Apns\Message $message)
    {
        return null;
    }
}
