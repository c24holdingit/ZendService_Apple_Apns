<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\Apple\Exception;

/**
 * Curl Transport Exception
 */
class CurlTransportException extends \RuntimeException
{
    public function __construct($code, $description)
    {
        parent::__construct(sprintf('During execution the curl error %s occured. (%s)', $code, $description));
    }
}
