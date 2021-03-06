<?php

namespace Yamete\Driver;

use Tuna\CloudflareMiddleware;

class HentaiRead extends \Yamete\DriverAbstract
{
    private $aMatches = [];
    const DOMAIN = 'hentairead.com';

    public function canHandle()
    {
        return (bool)preg_match(
            '~^https?://(' . strtr(self::DOMAIN, ['.' => '\.']) . ')/(?<album>[^/]+)/$~',
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
        $oRes = $this->getClient()->request('GET', $this->sUrl . '1/1/');
        $aReturn = [];
        $i = 0;
        $iNbPages = count($this->getDomParser()->load((string)$oRes->getBody())->find('select.cbo_wpm_pag option'));
        for ($iPage = 1; $iPage <= $iNbPages; $iPage++) {
            $oRes = $this->getClient()->request('GET', $this->sUrl . '1/' . $iPage . '/');
            /**
             * @var \PHPHtmlParser\Dom\AbstractNode $oImg
             */
            $oImg = $this->getDomParser()->load((string)$oRes->getBody())->find('img#manga-single-image');
            $sFilename = $oImg->getAttribute('src');
            $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($i++, 5, '0', STR_PAD_LEFT)
                . '-' . basename($sFilename);
            $aReturn[$sBasename] = $sFilename;
        }
        return $aReturn;
    }

    /**
     * @param array $aOptions
     * @return \GuzzleHttp\Client
     */
    public function getClient($aOptions = [])
    {
        $oClient = parent::getClient(
            [
                'cookies' => new \GuzzleHttp\Cookie\FileCookieJar(tempnam('/tmp', __CLASS__)),
                'headers' => ['User-Agent' => self::USER_AGENT, 'Referer' => $this->sUrl],
            ]
        );
        /**
         * @var \GuzzleHttp\HandlerStack $oHandler
         */
        $oHandler = $oClient->getConfig('handler');
        $oHandler->push(CloudflareMiddleware::create());
        return $oClient;
    }

    private function getFolder()
    {
        return implode(DIRECTORY_SEPARATOR, [self::DOMAIN, $this->aMatches['album']]);
    }
}
