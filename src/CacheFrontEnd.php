<?php
namespace ReverseGeocoderCache;

class CacheFrontEnd
{
  public static $EARTH_RADIUS = 6371000;
  
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

  public function set($latitude,$longitude,$data)
  {
    $key = $this->getCacheKey($latitude, $longitude);
    $this->cacheBackend->set($key, $data);
    if(method_exists($this->cacheBackend, 'expireAt') && isset($this->TTLInSeconds)) {
        $this->cacheBackend->expireAt($key, time() + intval($this->TTLInSeconds));
    }
  }
  
  public function get($latitude,$longitude)
  {
    $key = $this->getCacheKey($latitude, $longitude);
    return $this->cacheBackend->get($key);
  }

  public function exists($latitude,$longitude)
  {
    $key = $this->getCacheKey($latitude, $longitude);
    return $this->cacheBackend->get($key);
  }
 
  protected function getCacheKey($latitude,$longitude)
  {
    $keySizeRadians = $this->getRadiansByDistance($this->keySize);
    
    $latitudeRadians = $this->getRadiansByDegree($latitude);
    $longitudeRadians = $this->getRadiansByDegree($longitude);
    
    $keyLatitude = round($latitudeRadians / $this->getLatitudeKeyLength());
    $keyLongitude = round($longitudeRadians / $this->getLongitudeKeyLength($latitude));
    
    return $this->prefix."key-slot-size-".$this->keySize."-lat-".$keyLatitude."-lng-".$keyLongitude."";
  }
  
  
  protected function getRadiansKeySize()
  {
    return $this->getRadiansByDistance($this->keySize);
  }
  
  protected function getRadiansByDegree($degree)
  {
    return (M_PI/180)*$degree;
  }
  
  protected function getRadiansByDistance($distance)
  {
    return $distance / self::$EARTH_RADIUS;
  }
  
  protected function getLatitudeKeyLength()
  {
    return $this->getRadiansKeySize();
  }

  protected function getLongitudeKeyLength($latitude)
  {
    $operand = (2*sin($this->getRadiansKeySize()/2)) / cos($this->getRadiansByDegree($latitude)); 
    if ($operand > M_PI/2)
    {
      return 1;
    }
    else
    {
      return asin( $operand );
    }
  }

  
}
