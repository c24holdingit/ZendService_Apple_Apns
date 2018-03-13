<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage UnitTests
 */

namespace ZendServiceTest\Apple\Apns;

//require_once __DIR__ . '/TestAsset/Client.php';

use ZendServiceTest\Apple\Apns\TestAsset\Client;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Apns\Response;
use ZendService\Apple\Apns\Certificate;
use PHPUnit\Framework\TestCase;

/**
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage UnitTests
 * @group      ZendService
 * @group      ZendService_Apple
 * @group      ZendService_Apple_Apns
 */
class ClientTest extends TestCase
{
    public function setUp()
    {
        $this->apns = new Client();
        $this->message = new Message();
    }

    protected function setupValidBase()
    {
        $this->apns->setEnvironment(Client::SANDBOX_URI);
        $this->apns->setCertificate(new Certificate(__DIR__ . '/TestAsset/certificate.pem'));

        $this->message->setToken('662cfe5a69ddc65cdd39a1b8f8690647778204b064df7b264e8c4c254f94fdd8');
        $this->message->setId(time());
        $this->message->setAlert('bar');
    }

    public function testConnectThrowsExceptionOnInvalidEnvironment()
    {
        $this->expectException('InvalidArgumentException');
        $this->apns->setEnvironment(5);
    }
}
