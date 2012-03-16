<?php

// define namespace
namespace phira;

// phiraDataStore class
class phiraTestDataStore extends phiraDataStore implements phiraDataStoreInterface
{
  const
    ds_name = 'Test';
    
  static
    $_ds_cache_options = array(
      'path' => '/tmp/',
      'prefix' => 'phira_ds_cache_',
      'expire' => '+10 seconds'
    );

  // disable cloning of this class
  private function __clone() {}

  // return instance of phiraDataStore
  public static function getInstance($cache_config = array())
  {
    if (empty($cache_config))
      $cache_config = self::$_ds_cache_options;

    return parent::getInstance(self::ds_name, self::$_ds_cache_options);
  }

  // return instance of phiraDataStore
  public static function getCache()
  {
    return parent::getCache(self::ds_name);
  }

  // start connection to driver datastore
  public function startDriver()
  {
    return TRUE;
  }

  // get data from cache or raw data from datastore
  public function getData($type = NULL)
  {
    return array('asdasd','asdasdad','asdasdasd');
  }

  // get raw data from the datastore
  public function getRawData($type = NULL)
  {
    return array('asRAWdasd','asdasRAWdad','asdasRAWdasd');
  }

  // force sync the cache with datastore
  public function fsyncCache()
  {
    return TRUE;
  }
}