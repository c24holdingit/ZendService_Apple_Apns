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

use ZendService\Apple\Apns\Certificate;
use ZendService\Apple\Exception\InvalidArgumentException;

/**
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage UnitTests
 * @group      ZendService
 * @group      ZendService_Apple
 * @group      ZendService_Apple_Apns
 */
class CertificateTest extends \PHPUnit_Framework_TestCase
{
    public function testValidInitializationWithoutPassword()
    {
        $certificate = new Certificate(__DIR__ . '/TestAsset/certificate.pem');
    }
    
    public function testValidInitializationWithPassword()
    {
        $certificate = new Certificate(__DIR__ . '/TestAsset/certificate.pem', 'test_password');
    }
    
    public function testArrayTypeForCertificate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $certificate = new Certificate(array('not/a/valid/path'));
    }
    
    public function testNumberTypeForCertificate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $certificate = new Certificate(4);
    }
    
    public function testMissingFile()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $certificate = new Certificate('not/a/valid/path');
    }
    
    public function testInvalidTypeForPassword()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $certificate = new Certificate(array('password_array'));
    }
}
