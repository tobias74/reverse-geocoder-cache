<?php

namespace ReverseGeocoderCache;

class CacheClient
{
    protected static $countCacheMisses = 0;
    protected $cacheFrontEnd = false;
    protected $dataProvider = false;

    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function setCacheFrontEnd($frontEnd)
    {
        $this->cacheFrontEnd = $frontEnd;
    }

    public function get($latitude, $longitude)
    {
        if (!$this->cacheFrontEnd->exists($latitude, $longitude)) {
            ++self::$countCacheMisses;
            error_log('We had '.self::$countCacheMisses.' Cache-Misses so far...');
            try {
                $data = $this->retrieveData($latitude, $longitude);
            } catch (\ErrorException $e) {
                error_log('Google-Places-Error: ');
                error_log($e->getMessage());
                $data = 'place-error';
            }

            $this->cacheFrontEnd->set($latitude, $longitude, $data);

            if ('place-error' === $data) {
                throw new \Exception('we did get a place-error from google');
            } else {
                return $data;
            }
        } else {
            $value = $this->cacheFrontEnd->get($latitude, $longitude);
            if ('place-error' === $value) {
                throw new \Exception('We had stored a place-error in the cache client');
            } else {
                return $value;
            }
        }
    }

    protected function retrieveData($latitude, $longitude)
    {
        return $this->dataProvider->retrieveData($latitude, $longitude);
    }
}
