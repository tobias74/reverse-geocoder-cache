<?php

namespace ReverseGeocoderCache;

class CacheFrontEnd
{
    protected $prefix = 'CACHE_';

    public function setCacheBackend($cacheBackend)
    {
        $this->cacheBackend = $cacheBackend;
    }

    public function setKeySize($keySize)
    {
        $this->keySize = $keySize;
    }

    public function setDefaultKeyTTL($TTLInSeconds)
    {
        $this->TTLInSeconds = $TTLInSeconds;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function set($latitude, $longitude, $data)
    {
        $key = $this->getCacheKey($latitude, $longitude);
        $this->cacheBackend->set($key, $data);
        if (method_exists($this->cacheBackend, 'expireAt') && isset($this->TTLInSeconds)) {
            $this->cacheBackend->expireAt($key, time() + intval($this->TTLInSeconds));
        }
    }

    public function get($latitude, $longitude)
    {
        $key = $this->getCacheKey($latitude, $longitude);

        return $this->cacheBackend->get($key);
    }

    public function exists($latitude, $longitude)
    {
        $key = $this->getCacheKey($latitude, $longitude);

        return $this->cacheBackend->get($key);
    }

    protected function getCacheKey($latitude, $longitude)
    {
        $latitudeGrid = $this->keySize / 111111;
        $longitudeGrid = $this->keySize / (111111 * $this->cos($latitude));

        $keyLatitude = round($latitude / $latitudeGrid);
        $keyLongitude = round($longitude / $longitudeGrid);

        return $this->prefix.'key-slot-size-'.$this->keySize.'_lat_'.$keyLatitude.'_lng_'.$keyLongitude.'';
    }

    protected function cos($degree)
    {
        return cos($this->getRadiansByDegree($degree));
    }

    protected function getRadiansByDegree($degree)
    {
        return (M_PI / 180) * $degree;
    }
}
