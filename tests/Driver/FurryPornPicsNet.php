<?php

namespace YameteTests\Driver;


class FurryPornPicsNet extends \PHPUnit\Framework\TestCase
{
    public function testDownload()
    {
        $url = 'http://www.furrypornpics.net/galleries/jay-naylor-sarah';
        $driver = new \Yamete\Driver\FurryPornPicsNet();
        $driver->setUrl($url);
        $this->assertTrue($driver->canHandle());
        $this->assertEquals(2, count($driver->getDownloadables()));
    }
}
