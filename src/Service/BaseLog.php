<?php
/**
 * Created by PhpStorm.
 * User: 媳妇
 * Date: 2022-10-24
 * Time: 17:47
 */

namespace App\Service;


interface BaseLog
{
    public function info($message = '');
    public function error($message = '');
    public function debug($message = '');
}