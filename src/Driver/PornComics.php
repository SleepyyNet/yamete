<?php

namespace Yamete\Driver;

class PornComics extends ThreeDPornPics
{
    const DOMAIN = 'porncomics.me';

    protected function getDomain()
    {
        return self::DOMAIN;
    }
}
