reverse-geocoder-cache
======================

Cache for reverse geocoding request
-----------------------------------

This "reverse geocoder cache" can be used to cache results of reverse geocoding services like Googles Places or Timezones and thereby reducing the amount of requests made to these services. The accuracy of this cache can be dynamically adjusted by setting the internally used key-size. The key-size determines the size of the tiles in which the results will be placed. Larger key-size will result in lower accuracy. Smaller key-size will make the cache consume more memory at increased accuracy.

Example: Requesting the address of a given latitude-longitude while having a key-size of 1000 meters will most likely return a wrong address, since any previous request to a latitude-longitude-combination within a 1000 meter radius will already have been written into the according tile.


```php

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


class RedisCacheMimic
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

$cacheBackend = new RedisCacheMimic();



// caching googles places

$placesProvider = new \ReverseGeocoderCache\Provider\GooglePlacesProvider();
$placesProvider->setLanguage('en');

$cacheFrontEnd = new \ReverseGeocoderCache\CacheFrontEnd();
$cacheFrontEnd->setKeySize(50);
$cacheFrontEnd->setPrefix('PlacesCache_');
$cacheFrontEnd->setCacheBackend($cacheBackend);

$placesClient = new \ReverseGeocoderCache\CacheClient();
$placesClient->setDataProvider($placesProvider);
$placesClient->setCacheFrontEnd($cacheFrontEnd);

echo '<html>';
echo '<meta charset="UTF-8">';
echo '<br>';

//Albuquerque, N.M
echo $placesClient->get(35.05,-106.39);




// caching googles timezone

$timezonesProvider = new \ReverseGeocoderCache\Provider\GoogleTimezonesProvider();

$cacheFrontEnd = new \ReverseGeocoderCache\CacheFrontEnd();
$cacheFrontEnd->setKeySize(1000);
$cacheFrontEnd->setPrefix('TimezonesCache_');
$cacheFrontEnd->setCacheBackend($cacheBackend);

$timezonesClient = new \ReverseGeocoderCache\CacheClient();
$timezonesClient->setDataProvider($timezonesProvider);
$timezonesClient->setCacheFrontEnd($cacheFrontEnd);

echo '<html>';
echo '<meta charset="UTF-8">';
echo '<br>';

//Albuquerque, N.M
echo $timezonesClient->get(35.05,-106.39);





```
