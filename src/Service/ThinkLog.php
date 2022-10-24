<?php
/**
 * Created by PhpStorm.
 * User: 媳妇
 * Date: 2022-10-24
 * Time: 17:47
 */

namespace App\Service;

use think\facade\Log;

class ThinkLog implements BaseLog
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Log();
        $this->logger->init([
            'default'	=>	'file',
            'channels'	=>	[
                'file'	=>	[
                    'type'	=>	'file',
                    'path'	=>	'./logs/',
                ],
            ],
        ]);
    }

    public function info($message = '')
    {
        $this->logger->info(strtoupper($message));
    }

    public function debug($message = '')
    {
        $this->logger->debug(strtoupper($message));
    }

    public function error($message = '')
    {
        $this->logger->error(strtoupper($message));
    }
}