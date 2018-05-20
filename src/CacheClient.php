<?php
namespace ReverseGeocoderCache;

class CacheClient
{
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
  
  
  public function get($latitude, $longitude){
    if (!$this->cacheFrontEnd->exists($latitude,$longitude))
    {
      $data = $this->retrieveData($latitude, $longitude);
      $this->cacheFrontEnd->set($latitude, $longitude, $data);
      return $data;
    }
    else 
    {
      return $this->cacheFrontEnd->get($latitude, $longitude);  
    }
  }
  
  protected function retrieveData($latitude, $longitude)
  {
    return $this->dataProvider->retrieveData($latitude, $longitude);
  }
  
  
}
