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
      try
      {
        $data = $this->retrieveData($latitude, $longitude);
      }
      catch (\ErrorException $e)
      {
        $data = 'place-error';        
      }

      $this->cacheFrontEnd->set($latitude, $longitude, $data);
      
      if ($data === 'place-error')
      {
        throw new \Exception('we did get a place-error from google');        
      }
      else
      {
        return $data;
      }
    }
    else 
    {
      $value = $this->cacheFrontEnd->get($latitude, $longitude);
      if ($value === 'place-error')
      {
        throw new \Exception('We had stored a place-error in the cache client');
      }
      else
      {
        return $value;
      }
    }
  }
  
  protected function retrieveData($latitude, $longitude)
  {
    return $this->dataProvider->retrieveData($latitude, $longitude);
  }
  
  
}
