<?php
/**
 * Created by PhpStorm.
 * User: 媳妇
 * Date: 2022-10-24
 * Time: 17:47
 */

namespace App\Service;


class Log4Logger implements BaseLog
{
    private $logger;

    function __construct()
    {
        $this->logger = \Logger::getLogger("Log");
    }

    public function info($message = '')
    {
        $this->logger->info($message);
    }

    public function debug($message = '')
    {
        $this->logger->debug($message);
    }

    public function error($message = '')
    {
        $this->logger->error($message);
    }
}