<?php

namespace Test\App;

use App\Service\AppLogger;
use App\Util\HttpRequest;
use PHPUnit\Framework\TestCase;
use App\App\Demo;


class DemoTest extends TestCase
{

    public function test_foo()
    {
    }

    public function test_get_user_info()
    {
        $demo = new Demo(
            new AppLogger(),
            new HttpRequest()
        );
        $ret = $demo->get_user_info();
        $this->assertNotNull(ret);
        $this->assertArrayHasKey($ret, 'id');
        $this->assertArrayHasKey($ret, 'username');
    }
}
