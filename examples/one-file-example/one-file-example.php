<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

spl_autoload_register(function ($class) {
    $prefix = 'ReverseGeocoderCache\\';
    $base_dir = __DIR__ . '/../../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});


class RedisCache
{
  protected $hash=array();
  
  public function set($key, $value)
  {
    $this->hash[$key] = $value;  
  }
  
  public function get($key)
  {
    return $this->hash[$key];
  }
}


$cacheBackend = new RedisCache();

$placesProvider = new \ReverseGeocoderCache\Provider\GooglePlacesProvider();
$placesProvider->setLanguage('en');

$cacheFrontEnd = new \ReverseGeocoderCache\CacheFrontEnd();
$cacheFrontEnd->setKeySize(50);
$cacheFrontEnd->setPrefix('PlacesCache_');
$cacheFrontEnd->setCacheBackend($cacheBackend);

$placesClient = new \ReverseGeocoderCache\CacheClient();
$placesClient->setDataProvider($placesProvider);
$placesClient->setCacheFrontEnd($cacheFrontEnd);

echo "<br>";
echo $placesClient->get(48.1333, 11.5668);




