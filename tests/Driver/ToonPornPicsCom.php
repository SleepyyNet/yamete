<?php

namespace YameteTests\Driver;


class ToonPornPicsCom extends \PHPUnit\Framework\TestCase
{
    public function testDownload()
    {
        $url = 'http://www.toonpornpics.com/galleries/mix-of-famous-cartoon-anime-hentai-girls';
        $driver = new \Yamete\Driver\ToonPornPicsCom();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(3, count($driver->getDownloadables()));
    }
}
