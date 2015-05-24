<?php
namespace ReverseGeocoderCache\Provider;

class GooglePlacesProvider
{
  
  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }
  
  public function setLanguage($lang)
  {
    $this->language = $lang;
  }

  protected function getLanguage()
  {
    return $this->language;
    //$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    //return $lang;
  }

  public function retrieveData($latitude, $longitude)
  {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&language=".$this->getLanguage()."&sensor=true&key=".$this->apiKey;
    $dataString = @file_get_contents($url);
    $data = json_decode($dataString,true);
    return $data['results'][0]['formatted_address'];
    
  }
  
  
}
