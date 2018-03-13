<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendServiceTest\Apple\Apns;

use ZendService\Apple\Apns\Response;
use PHPUnit\Framework\TestCase;

/**
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage UnitTests
 * @group      ZendService
 * @group      ZendService_Apple
 * @group      ZendService_Apple_Apns
 */
class ResponseTest extends TestCase
{
    public function testValidInstantiation()
    {
        $rawHeaders = "HTTP/2.0 200 OK\r\napns-id: 01234567-0123-0123-012345678901";
        $rawBody = '';

        $response = new Response($rawHeaders, $rawBody);

        $this->assertEquals(Response::RESULT_OK, $response->getResponseCode());
        $this->assertEquals('01234567-0123-0123-012345678901', $response->getId());
        $this->assertNull($response->getTime());
        $this->assertNull($response->getErrorReason());
    }

    public function testNonOkResponses()
    {
        $headers = array(
            '400' => "HTTP/2.0 400 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '403' => "HTTP/2.0 403 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '405' => "HTTP/2.0 405 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '410' => "HTTP/2.0 410 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '413' => "HTTP/2.0 413 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '429' => "HTTP/2.0 429 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '500' => "HTTP/2.0 500 OK\r\napns-id: 01234567-0123-0123-012345678901",
            '503' => "HTTP/2.0 503 OK\r\napns-id: 01234567-0123-0123-012345678901",
        );

        foreach($headers as $key => $header) {
            $response = new Response($header, '');
            $this->assertEquals($key, $response->getResponseCode());
        }
    }

    public function testReasons()
    {
        $bodies = array(
            'PayloadEmpty' => '{"reason": "PayloadEmpty"}',
            'PayloadTooLarge' => '{"reason": "PayloadTooLarge"}',
            'BadTopic' => '{"reason": "BadTopic"}',
            'TopicDisallowed' => '{"reason": "TopicDisallowed"}',
            'BadMessageId' => '{"reason": "BadMessageId"}',
            'BadExpirationDate' => '{"reason": "BadExpirationDate"}',
            'BadPriority' => '{"reason": "BadPriority"}',
            'BadDeviceToken' => '{"reason": "BadDeviceToken"}',
            'DeviceTokenNotForTopic' => '{"reason": "DeviceTokenNotForTopic"}',
            'Unregistered' => '{"reason": "Unregistered"}',
            'DuplicateHeaders' => '{"reason": "DuplicateHeaders"}',
            'BadCertificateEnvironment' => '{"reason": "BadCertificateEnvironment"}',
            'BadCertificate' => '{"reason": "BadCertificate"}',
            'Forbidden' => '{"reason": "Forbidden"}',
            'BadPath' => '{"reason": "BadPath"}',
            'MethodNotAllowed' => '{"reason": "MethodNotAllowed"}',
            'TooManyRequests' => '{"reason": "TooManyRequests"}',
            'IdleTimeout' => '{"reason": "IdleTimeout"}',
            'Shutdown' => '{"reason": "Shutdown"}',
            'InternalServerError' => '{"reason": "InternalServerError"}',
            'ServiceUnavailable' => '{"reason": "ServiceUnavailable"}',
            'MissingTopic' => '{"reason": "MissingTopic"}',
        );

        foreach($bodies as $key => $body) {
            $response = new Response("HTTP/2.0 200OK apns-id: 01234567-0123-0123-012345678901", $body);
            $this->assertEquals($key, $response->getErrorReason());
        }
    }

    public function testTokenNoLongerActive()
    {
        $rawHeaders = 'HTTP/2.0 410 OK apns-id: 01234567-0123-0123-012345678901';
        $rawBody = '{"reason": "Unregistered", "timestamp": "1460468014"}';

        $response = new Response($rawHeaders, $rawBody);

        $this->assertEquals(Response::RESULT_TOKEN_NO_LONGER_ACTIVE, $response->getResponseCode());
        $this->assertEquals(\DateTime::createFromFormat(1460468014, 'U'), $response->getTime());
        $this->assertEquals(Response::ERROR_UNREGISTERED, $response->getErrorReason());
    }


}
