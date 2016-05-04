<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage Apns
 */

namespace ZendService\Apple\Apns;

use ZendService\Apple\Exception;

/**
 * Client
 *
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage Apns
 */
class Client
{
    /**
     * APNS URI Constants
     * @var int
     */
    const SANDBOX_URI = 'sandbox';
    const PRODUCTION_URI = 'production';

    /**
     * @var array
     */
    protected $uris = array(
        'sandbox' => 'https://api.development.push.apple.com/3/device/',
        'production' => 'https://api.push.apple.com/3/device/',
    );
    
    /**
     * @var string
     */
    protected $environment = self::PRODUCTION_URI;
    
    /**
     * @var certificate
     */
    protected $certificate;

    /**
     * @var resource
     */
    protected $curlHandle;

    /**
     * Sets the apns certificate
     * 
     * @param Certificate $certificate
     */
    public function setCertificate(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }
    
    /**
     * Sets the environment
     * 
     * @param string $environment
     * @throws Exception\InvalidArgumentException
     */
    public function setEnvironment($environment)
    {
        if (!array_key_exists($environment, $this->uris)) {
            throw new Exception\InvalidArgumentException('Environment must be one of PRODUCTION_URI or SANDBOX_URI');
        }
        
        $this->environment = $environment;
    }
    
    /**
     * Creates the curl  handle
     * 
     * Note: The following prerequisites have to be met for doing HTTP/2 calls
     *       * >= PHP 5.5.24
     *       * >= Curl 7.46
     *       * >= openssl 1.0.2e
     * 
     * @return AbstractClient
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    protected function getCurlHandle()
    {
        if(is_resource($this->curlHandle)) {
            return $this->curlHandle;
        }
        
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            throw new Exception\RuntimeException('Curl constant CURL_HTTP_VERSION_2_0 (>=PHP 5.5.24) is not defined.');
        }
   
        $this->curlHandle = curl_init();
        
        curl_setopt($this->curlHandle, CURLOPT_POST, true);
        curl_setopt($this->curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($this->curlHandle, CURLOPT_SSLCERT, $this->certificate->getPath());
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_HEADER, true);
        
        if($this->certificate->getPassphrase() != null) {
            curl_setopt($this->curlHandle, CURLOPT_SSLCERTPASSWD, $this->certificate->getPassphrase());
        }
        
        return $this->curlHandle;
    }

    /**
     * Send Message
     *
     * @param ApnsMessage $message
     * @return MessageResponse
     * @throws Exception\CurlTransportException
     */
    public function send(Message $message)
    {
        $ch = $this->getCurlHandle();
        
        $headers = array();
        $headers[] = 'apns-priority: ' . $message->getPriority();
        
        if($message->getId() !== null) {
            $headers[] = 'apns-id: ' . $message->getId();
        }
        
        if($message->getTopic() !== null) {
            $headers[] = 'apns-topic: ' . $message->getTopic();
        }
        
        if($message->getExpire()  !== null) {
            $headers[] = 'apns-expiration: ' . $message->getExpire();
        }

        curl_setopt($ch, CURLOPT_URL, $this->uris[$this->environment] . $message->getToken());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message->getPayloadJson());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch) > 0) {
            throw new Exception\CurlTransportException(curl_errno($ch), curl_error($ch));
        }
        
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
		$body = substr($response, $headerSize);

        return new Response($headers, $body);
    }
    
    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        if(is_resource($this->curlHandle)) {
            curl_close($this->curlHandle);
        }
    }
}
