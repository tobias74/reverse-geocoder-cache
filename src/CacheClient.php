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

    public function get($latitude, $longitude)
    {
        if (!$this->cacheFrontEnd->exists($latitude, $longitude)) {
            return $this->produceData($latitude, $longitude);
        } else {
            $value = $this->getData($latitude, $longitude);
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
        try {
            $data = $this->retrieveData($latitude, $longitude);
        } catch (\ErrorException $e) {
            $this->setData($latitude, $longitude, array(
          'status' => 'error',
          'timestamp' => time(),
        ));

            throw new \Exception('we did get a place-error from google');
        }

        $this->setData($latitude, $longitude, array(
        'status' => 'ok',
        'payload' => $data,
      ));

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
