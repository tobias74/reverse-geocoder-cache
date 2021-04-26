<?php

namespace ReverseGeocoderCache;

class CacheClient
{
    protected $cacheFrontEnd = false;
    protected $dataProvider = false;
    protected $profiler = false;

    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function setCacheFrontEnd($frontEnd)
    {
        $this->cacheFrontEnd = $frontEnd;
    }

    public function setProfiler($val)
    {
        $this->profiler = $val;
    }

    protected function getProfiler()
    {
        return $this->profiler;
    }

    protected function startTimer($name)
    {
        $profiler = $this->getProfiler();
        if ($profiler) {
            return $profiler->startTimer($name);
        } else {
            $obj = new class() {
                public function stop()
                {
                }
            };

            return $obj;
        }
    }

    public function get($latitude, $longitude)
    {
        $timer = $this->startTimer('cache hit retrieval');
        $cachedValue = $this->getData($latitude, $longitude);
        $timer->stop();

        if (!$cachedValue) {
            $value = $this->produceData($latitude, $longitude);

            return $value;
        } else {
            $value = $cachedValue;
            if ('error' === $value['status']) {
                if ($value['timestamp'] + 3600 < time()) {
                    return $this->produceData($latitude, $longitude);
                } else {
                    throw new \Exception('We had stored a place-error in the cache client, and the timeout has not yet allowed for a new retrieval...');
                }
            } else {
                return $value['payload'];
            }
        }
    }

    protected function produceData($latitude, $longitude)
    {
        $timer = $this->startTimer('producing data for geocache');

        try {
            $data = $this->retrieveData($latitude, $longitude);
        } catch (\ErrorException $e) {
            $this->setData($latitude, $longitude, array(
              'status' => 'error',
              'timestamp' => time(),
            ));
            throw new \Exception('we did get a place-error from google');
        } finally {
            $timer->stop();
        }

        $timer = $this->startTimer('storing data in geocache');

        $this->setData($latitude, $longitude, array(
            'status' => 'ok',
            'payload' => $data,
        ));

        $timer->stop();

        return $data;
    }

    protected function setData($latitude, $longitude, $data)
    {
        $this->cacheFrontEnd->set($latitude, $longitude, json_encode($data));
    }

    protected function getData($latitude, $longitude)
    {
        return json_decode($this->cacheFrontEnd->get($latitude, $longitude), true);
    }

    protected function retrieveData($latitude, $longitude)
    {
        return $this->dataProvider->retrieveData($latitude, $longitude);
    }
}
