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
 * Application Object
 *
 * @category   ZendService
 * @package    ZendService_Apple
 * @subpackage Apns
 */
class Application
{
    /**
     * @var string
     */
    protected $bundleId;

    /**
     * @var string
     */
    protected $certificate;
    
    /**
     * @var string
     */
    protected $passphrase;
    
    /**
     * Constructor
     *
     * @param string $bundleId
     * @param string $certificate
     * @param string $passPhrase
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($bundleId, $certificate, $passPhrase = null)
    {
        if (!is_string($certificate) || !file_exists($certificate)) {
           throw new Exception\InvalidArgumentException('Certificate must be a valid path to a APNS certificate');
        }
        
        if ($passPhrase !== null) {
            if (!is_scalar($passPhrase)) {
                throw new Exception\InvalidArgumentException('SSL passphrase must be a scalar');
            }
        }

        $this->bundleId = $bundleId;
        $this->certificate = $certificate;
        $this->passphrase = $passPhrase;
    }

    /**
     * Returns the bundle id
     *
     * @return string
     */
    public function getBundleId()
    {
        return $this->bundleId;
    }

    /**
     * Returns the certificates path
     * 
     * @return string
     */
    public function getCertificatePath()
    {
        return $this->certificate;
    }

    /**
     * Returns the passphrase
     * 
     * @return string
     */
    public function getCertificatePassword()
    {
        return $this->passphrase;
    }
}
