<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class TileTest extends TestCase
{
    protected function getMethod($name)
    {
        $class = new ReflectionClass(\ReverseGeocoderCache\CacheFrontEnd::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testSanity(): void
    {
        $this->assertEquals(true, true);
    }

    public function testSameTileHit(): void
    {
        $frontEnd = new \ReverseGeocoderCache\CacheFrontEnd();
        $frontEnd->setKeySize(1500);
        $getCacheKeyMethod = $this->getMethod('getCacheKey');

        $cacheKeyBridge_A1 = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7119693, -74.0062215));
        $cacheKeyBridge_A2 = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7080078, -73.9998796));
        $cacheKeyBridge_B1 = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7037924, -73.9942927));
        $cacheKeyBridge_B2 = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.696042, -73.9849284));

        $keys = [$cacheKeyBridge_A1, $cacheKeyBridge_A2, $cacheKeyBridge_B1, $cacheKeyBridge_B2];
        $uniqueKeys = array_unique($keys);

        $this->assertTrue(count($uniqueKeys) <= 2);
    }

    public function testDifferentTileHit(): void
    {
        $frontEnd = new \ReverseGeocoderCache\CacheFrontEnd();
        $frontEnd->setKeySize(500);
        $getCacheKeyMethod = $this->getMethod('getCacheKey');

        $cacheKeyBridge_A = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7080078, -73.9998796));
        $cacheKeyBridge_B = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7037924, -73.9942927));

        $this->assertNotEquals($cacheKeyBridge_A, $cacheKeyBridge_B);
    }
}
