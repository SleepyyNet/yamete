<?php

namespace YameteTests\Driver;


class SexCartoonPicsCom extends \PHPUnit\Framework\TestCase
{
    public function testDownload()
    {
        $url = 'http://www.sexcartoonpics.com/fr/galleries/-kiyokawa-zaidan-kiyokawa-nijiko-sonogo-no-haha-2214';
        $driver = new \Yamete\Driver\SexCartoonPicsCom();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(8, count($driver->getDownloadables()));
    }
}
