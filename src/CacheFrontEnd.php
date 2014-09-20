<?php
namespace ReverseGeocoderCache;

class CacheFrontEnd
{
  public static $EARTH_RADIUS = 6371000;
  
  
  public function __construct($keySize)
  {
    $this->keySize = $keySize;
  }
  
  public function save($latitude,$longitude,$data)
  {
    
  }
  
  public function load($latitude,$longitude)
  {
    
  }
 
 
  public function getCacheKey($latitude,$longitude)
  {
    $keySizeRadians = $this->getRadiansByDistance($this->keySize);
    
    $latitudeRadians = $this->getRadiansByDegree($latitude);
    $longitudeRadians = $this->getRadiansByDegree($longitude);
    
    $keyLatitude = round($latitudeRadians / $this->getLatitudeKeyLength());
    $keyLongitude = round($longitudeRadians / $this->getLongitudeKeyLength($latitude));
    
    return "key-slot-size-".$this->keySize."-lat-".$keyLatitude."-lng-".$keyLongitude."";
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
