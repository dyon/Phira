<?php

// define namespace
namespace phira;

// interface for each datastore
interface phiraDataStoreInterface
{
  function startDriver();
  function getData();
  function getRawData();
  function fsyncCache();
}


// phiraDataStore class
abstract class phiraDataStore
{        
  static
    $_ds_instances = array(),
    $_ds_caches = array(),
    $_ds_cache_options = array(
      'path' => '/tmp/',
      'prefix' => 'phira_ds_cache_',
      'expire' => '+10 seconds'
    ),
    $allowed_data_types = array('resolution', 'priority', 'issuestatus', 'issuetype', 'customfield', 'customfieldoption');

  // disable cloning of this class
  private function __clone() {}
  
  // return instance of a datastore
  public static function getInstance($data_store = 'MySQL', $cache_config = array())
  {
    $phiraDSName = "\phira\phira".$data_store."DataStore";

    if ((!isset(self::$_ds_instances[$data_store])) OR (!(self::$_ds_instances[$data_store] instanceof self::$_ds_instances[$data_store])))
    {
      if (empty($cache_config))
        $cache_config = self::$_ds_cache_options;
      
      self::$_ds_instances[$data_store] = new $phiraDSName();
      self::$_ds_caches[$data_store] = phiraCache::getInstance()->config($cache_config);
    }
      
    if (!self::$_ds_instances[$data_store]->startDriver())
      return FALSE;
      
    return self::$_ds_instances[$data_store];
  }
  
  // return cache instance of a datastore
  public static function getCache($data_store = 'MySQL')
  {
    if (!isset(self::$_ds_caches[$data_store]))
      self::$_ds_caches[$data_store] = phiraCache::getInstance()->config($cache_config);
      
    return self::$_ds_caches[$data_store];
  }
  
  // get data from the datastore
  public function getData($type = NULL, $data_store = 'MySQL')
  {
    return self::getInstance($data_store)->getData($type);
  }
  
  // get raw data from the datastore
  public function getRawData($type = NULL)
  {
    return $this->getRawData($type);
  }
  
  // force sync the cache with the datastore
  public function fsyncCache()
  {
    return $this->fsyncCache();
  }
  
}