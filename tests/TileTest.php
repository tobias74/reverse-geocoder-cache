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
        $frontEnd->setKeySize(2000);
        $getCacheKeyMethod = $this->getMethod('getCacheKey');

        $cacheKeyBridge_A = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7080078, -73.9998796));
        $cacheKeyBridge_B = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7037924, -73.9942927));

        $this->assertEquals($cacheKeyBridge_A, $cacheKeyBridge_B);
    }

    public function testDifferentTileHit(): void
    {
        $frontEnd = new \ReverseGeocoderCache\CacheFrontEnd();
        $frontEnd->setKeySize(1000);
        $getCacheKeyMethod = $this->getMethod('getCacheKey');

        $cacheKeyBridge_A = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7080078, -73.9998796));
        $cacheKeyBridge_B = $getCacheKeyMethod->invokeArgs($frontEnd, array(40.7037924, -73.9942927));

        $this->assertNotEquals($cacheKeyBridge_A, $cacheKeyBridge_B);
    }
}
