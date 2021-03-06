<?php

namespace Yamete\Driver;

if (!class_exists(FreeFamousCartoonPornCom::class)) {
    class FreeFamousCartoonPornCom extends \Yamete\DriverAbstract
    {
        private $aMatches = [];
        const DOMAIN = 'freefamouscartoonporn.com';

        protected function getDomain()
        {
            return self::DOMAIN;
        }

        public function canHandle()
        {
            return (bool)preg_match(
                '~^https?://(' . strtr($this->getDomain(), ['.' => '\.']) . ')/content/(?<album>[^/]+)/index\.html$~',
                $this->sUrl,
                $this->aMatches
            );
        }

        protected function getSelectors()
        {
            return [
                '#grid-content a',
                '#aniimated-thumbnials a',
            ];
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
            $oIterator = new \ArrayIterator;
            foreach ($this->getSelectors() as $sSelector) {
                $oIterator = $this->getDomParser()->load((string)$oRes->getBody(), ['cleanupInput' => false])
                    ->find($sSelector);
                if (count($oIterator) !== 0) {
                    break;
                }
            }
            foreach ($oIterator as $oLink) {
                /**
                 * @var \PHPHtmlParser\Dom\AbstractNode $oLink
                 * @var \PHPHtmlParser\Dom\AbstractNode $oImage
                 */
                $sUrl = 'http://' . $this->getDomain() . $oLink->getAttribute('href');
                $sSelector = '.container-gal-item img';
                $oImage = $this->getDomParser()->load(
                        (string)$this->getClient()->request('GET', $sUrl)->getBody(),
                        ['cleanupInput' => false]
                    )
                    ->find($sSelector)[0];
                $sFilename = $oImage->getAttribute('src');
                $sBasename = $this->getFolder() . DIRECTORY_SEPARATOR . str_pad($i++, 5, '0', STR_PAD_LEFT)
                    . '-' . basename($sFilename);
                $aReturn[$sBasename] = $sFilename;
            }
            return $aReturn;
        }

        private function getFolder()
        {
            return implode(DIRECTORY_SEPARATOR, [$this->getDomain(), $this->aMatches['album']]);
        }
    }
}
