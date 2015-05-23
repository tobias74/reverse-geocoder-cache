<?php
namespace ReverseGeocoderCache;

class CacheClient
{
  public function __construct($options){
    $this->cacheFrontEnd = new CacheFrontEnd(
      $options['cacheBackend'], 
      $options['keySize'],
      $options['prefix']
      );
      
    $this->dataProvider = $options['dataProvider'];  
  }
  
  
  public function get($latitude, $longitude){
    if (!$this->cachFrontEnd->exists($latitude,$longitude))
    {
      $data = $this->retrieveData($latitude, $longitude);
      $this->cacheFrontEnd->set($latitude, $longitude, $data);
      return $data;
    }
    else 
    {
      return $this->cachFrontEnd->get($latitude, $longitude);  
    }
  }
  
  protected function retrieveData($latitude, $longitude)
  {
    return $this->dataProvider->retrieveData($latitude, $longitude);
  }
  
  
}
