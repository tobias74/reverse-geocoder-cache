<?php
namespace ReverseGeocoderCache\Provider;

class GoogleTimezonesProvider
{
  
  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }
  
  public function retrieveData($lat,$lng)
  {
    try
    {
      $url = "https://maps.googleapis.com/maps/api/timezone/json?location=$lat,$lng&timestamp=0&key=".$this->apiKey;
      $values = json_decode(file_get_contents($url));
      if (!isset($values->timeZoneId))
      {
        throw new \ErrorException('no timezone from google');
      }
      $timezoneId = $values->timeZoneId;
    }
    catch (\ErrorException $e)
    {
      $timezoneId = "Europe/Paris";
    }
    
    return $timezoneId;
  }
  


  
}
