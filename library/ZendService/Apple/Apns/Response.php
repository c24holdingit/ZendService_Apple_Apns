<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage Apns
 */

namespace ZendService\Apple\Apns;

use ZendService\Apple\Exception;
use Zend\Json\Decoder as JsonDecoder;
use Zend\Json\Json;

/**
 * Message Response
 *
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage Apns
 */
class Response
{
    /**
     * @var int Success
     */
    const RESULT_OK = 200;
    
    /**
     * @var int Bad request
     */
    const RESULT_BAD_REQUEST = 400;
    
    /**
     * @var int There was an error with the certificate
     */
    const RESULT_CERTIFICATE_ERROR = 403;
    
    /**
     * @var int A wrong HTTP method is used
     */
    const RESULT_BAD_METHOD = 405;
    
    /**
     * @var int The device token is no longer active for the topic.
     */
    const RESULT_TOKEN_NO_LONGER_ACTIVE = 410;
    
    /**
     * @var int The notification payload was too large
     */
    const RESULT_PAYLOAD_TOO_LARGE = 413;
    
    /**
     * @var int The server received too many requests for the same device token
     */
    const RESULT_TOO_MANY_REQUESTS = 429;
    
    /**
     * @var int Internal server error
     */
    const RESULT_INTERNAL_SERVER_ERROR = 500;
    
    /**
     * @var int The server is shutting down and unavailable
     */
    const RESULT_SHUTTING_DOWN = 503;

    /**
     * @var string The message payload was empty
     */
    const ERROR_PAYLOADEMPTY = 'PayloadEmpty';
    
    /**
     * @var string The message payload was too large
     */
    const ERROR_PAYLOADTOOLARGE = 'PayloadTooLarge';
    
    /**
     * @var string The apns-topic was invalid
     */
    const ERROR_BADTOPIC = 'BadTopic';
    
    /**
     * @var string Pushing to this topic is not allowed
     */
    const ERROR_TOPICDISALLOWED = 'TopicDisallowed';
    
    /**
     * @var string The apns-id value is bad
     */
    const ERROR_BADMESSAGEID = 'BadMessageId';
    
    /**
     * @var string The apns-expiration value is bad
     */
    const ERROR_BADEXPIRATIONDATE = 'BadExpirationDate';
    
    /**
     * @var string The apns-priority value is bad
     */
    const ERROR_BADPRIORITY = 'BadPriority';
    
    /**
     * @var string The device token is not specified in the request :path
     */
    const ERROR_MISSINGDEVICETOKEN = 'MissingDeviceToken';
    
    /**
     * @var string The specified device token was bad
     */
    const ERROR_BADDEVICETOKEN = 'BadDeviceToken';
    
    /**
     * @var string The device token does not match the specified topic
     */
    const ERROR_DEVICETOKENNOTFORTOPIC = 'DeviceTokenNotForTopic';
    
    /**
     * @var string The device token is inactive for the specified topic
     */
    const ERROR_UNREGISTERED = 'Unregistered';
    
    /**
     * @var string One or more headers were repeated
     */
    const ERROR_DUPLICATEHEADERS = 'DuplicateHeaders';
    
    /**
     * @var string The client certificate was for the wrong environment
     */
    const ERROR_BADCERTIFICATEENVIRONMENT = 'BadCertificateEnvironment';
    
    /**
     * @var string The certificate was bad
     */
    const ERROR_BADCERTIFICATE = 'BadCertificate';
    
    /**
     * @var string The specified action is not allowed
     */
    const ERROR_FORBIDDEN = 'Forbidden';
    
    /**
     * @var string The request contained a bad :path value
     */
    const ERROR_BADPATH = 'BadPath';
    
    /**
     * @var string The specified :method was not POST
     */
    const ERROR_METHODNOTALLOWED = 'MethodNotAllowed';
    
    /**
     * @var string Too many requests were made consecutively to the same device token
     */
    const ERROR_TOOMANYREQUESTS = 'TooManyRequests';
    
    /**
     * @var string Idle time out
     */
    const ERROR_IDLETIMEOUT = 'IdleTimeout';
    
    /**
     * @var string The server is shutting down
     */
    const ERROR_SHUTDOWN = 'Shutdown';
    
    /**
     * @var string An internal server error occurred
     */
    const ERROR_INTERNALSERVERERROR = 'InternalServerError';
    
    /**
     * @var string The service is unavailable
     */
    const ERROR_SERVICEUNAVAILABLE = 'ServiceUnavailable';
    
    /**
     * @var string The apns-topic header of the request was not specified and was required
     */
    const ERROR_MISSINGTOPIC = 'MissingTopic';

    
    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $responseCode;
    
    /**
     * @var string
     */
    protected $errorReason;
    
    /**
     * @var \DateTime
     */
    protected $time;

    /**
     * Constructor
     *
     * @param  string $rawHeaders
     * @param  string $body
     * @return Message
     */
    public function __construct($rawHeaders, $body)
    {
        $headerArray = $this->parseHeaders($rawHeaders);
        
        $this->responseCode = (int) $headerArray['code'];

        if(isset($headerArray['apns-id'])) {
            $this->id = $headerArray['apns-id'];
        }
            
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $responseJson = json_decode(trim($body), true);
        } else {
            $payload = JsonDecoder::decode(trim($body), Json::TYPE_ARRAY);
        }
        
        if(isset($responseJson['reason'])) {
            $this->errorReason = $responseJson['reason'];
        }
        
        if($this->responseCode == 410 && isset($responseJson['timestamp'])) {
            $this->time = \DateTime::createFromFormat('u', $responseJson['timestamp']);
        }
    }
    
    /**
     * Parses the header string into an array
     * 
     * @param string $rawHeaders
     * @return array
     */
    protected function parseHeaders($rawHeaders)
    {
        $rawHeaders = explode("\r\n", trim($rawHeaders));
        
        $headers = array();
        
        // status code
        $matches = array();
        preg_match('/^HTTP\/.*([0-9]{3})$/', trim(array_shift($rawHeaders)), $matches);
        $headers['code'] = (int)$matches[1];
        
        foreach($rawHeaders as $rawHeader) {
            
            if(!$rawHeader) {
                continue;
            }
            
            $delimiter = strpos($rawHeader, ':');
			if (!$delimiter) {
				continue;
			}
            
            $key = trim(strtolower(substr($rawHeader, 0, $delimiter)));
			$val = ltrim(substr($rawHeader, $delimiter + 1));
            
            $headers[$key] = $val;
        }
        
        return $headers;
    }

    /**
     * Returns the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the response code
     * 
     * @return int
     */
    function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Returns the error reason
     * 
     * @return string
     */
    function getErrorReason()
    {
        return $this->errorReason;
    }

    /**
     * Returns the last time apns confirmed that the token is no longer valid
     * 
     * @return \DateTime
     */
    function getTime()
    {
        return $this->time;
    }
}
