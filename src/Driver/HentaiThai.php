<?php

namespace Yamete\Driver;

class HentaiThai extends \Yamete\DriverAbstract
{
    private $aMatches = [];
    const DOMAIN = 'hentaithai.com';

    public function canHandle()
    {
        return (bool)preg_match(
            '~^https?://(' . strtr(self::DOMAIN, ['.' => '\.']) . ')/forum/index\.php\?topic=(?<album>[^/]+)$~',
            $this->sUrl,
            $this->aMatches
        );
    }

    /**
     * @return array|string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDownloadables()
    {
        $oRes = $this->getClient()->request('GET', $this->sUrl);
        $aReturn = [];
        $i = 0;
        foreach ($this->getDomParser()->load((string)$oRes->getBody())->find('.img-fluid img') as $oImg) {
            /**
             * @var \PHPHtmlParser\Dom\AbstractNode $oImg
             */
            $sFilename = 'https://' . self::DOMAIN . '/' . $oImg->getAttribute('src');
            $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($i++, 5, '0', STR_PAD_LEFT)
                . '-' . basename($sFilename);
            $aReturn[$sBasename] = $sFilename;
        }
        return $aReturn;
    }

    private function getFolder()
    {
        return implode(DIRECTORY_SEPARATOR, [self::DOMAIN, $this->aMatches['album']]);
    }
}
