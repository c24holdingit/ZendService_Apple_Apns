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

use ZendService\Apple\Apns\Application;
use ZendService\Apple\Exception\InvalidArgumentException;

/**
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage UnitTests
 * @group      ZendService
 * @group      ZendService_Apple
 * @group      ZendService_Apple_Apns
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidInitializationWithoutPassword()
    {
        $application = new Application('bundle-id', __DIR__ . '/TestAsset/certificate.pem');
    }
    
    public function testValidInitializationWithPassword()
    {
        $application = new Application('bundle-id', __DIR__ . '/TestAsset/certificate.pem', 'test_password');
    }
    
    public function testArrayTypeForCertificate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $application = new Application('bundle-id', array('not/a/valid/path'));
    }
    
    public function testNumberTypeForCertificate()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $application = new Application('bundle-id', 4);
    }
    
    public function testMissingFile()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $application = new Application('bundle-id', 'not/a/valid/path');
    }
    
    public function testInvalidTypeForPassword()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $application = new Application('bundle-id', array('password_array'));
    }
}
