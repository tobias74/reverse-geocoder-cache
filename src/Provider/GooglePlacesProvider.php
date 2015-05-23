<?php
namespace ReverseGeocoderCache\Provider;

class GooglePlacesProvider
{
  
  public function __construct($browserLanguage)
  {
    $this->browserLanguage = $browserLanguage;
  }
  
  public function retrieveData($latitude, $longitude)
  {
    $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&language=".$this->getBrowserLanguage()."&sensor=true";
    $dataString = @file_get_contents($url);
    $data = json_decode($dataString,true);
    return $data['results'][0]['formatted_address'];
    
  }
  
  protected function getBrowserLanguage()
  {
    return $this->browserLanguage;
    //$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    //return $lang;
  }
  
}
