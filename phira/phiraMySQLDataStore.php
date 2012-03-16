<?php

// define namespace
namespace phira;

// phiraDataStore class
class phiraMySQLDataStore extends phiraDataStore implements phiraDataStoreInterface
{
  const
    ds_name = 'MySQL',
    ds_driver_db = 'dbname',
    ds_driver_username = 'dbnameusername',
    ds_driver_password = 'dbnamepassword',
    ds_driver_host = 'hosttodb';

  static
    $_ds_cache_options = array(
      'path' => '/tmp/',
      'prefix' => 'phira_ds_mysql_cache_',
      'expire' => '+10 seconds'
    );

  static
    $_ds_driver = FALSE;

  // disable cloning of this class
  private function __clone() {}

  // return instance of this datastore
  public static function getInstance($cache_config = array())
  {
    if (empty($cache_config))
      $cache_config = self::$_ds_cache_options;

    return parent::getInstance(self::ds_name, self::$_ds_cache_options);
  }

  // return instance this datastore
  public static function getCache()
  {
    return parent::getCache(self::ds_name);
  }

  // start driver connection to this datastore
  public function startDriver()
  {
    if (!self::$_ds_driver)
    {
      if (!(self::$_ds_driver = @mysql_connect(self::ds_driver_host, self::ds_driver_username, self::ds_driver_password)))
        return FALSE;
    }
    return mysql_select_db(self::ds_driver_db, self::$_ds_driver);
  }

  // get data from cache or raw data from this datastore
  public function getData($type = NULL)
  {
    if (!in_array($type, parent::$allowed_data_types))
      return FALSE;

    if (!($data = self::getCache()->read($type)))
    {
      if (!($data = self::getInstance()->getRawData($type)))
        return FALSE;

      self::getCache()->write($type, $data);
    }
    return $data;
  }

  // get raw data from this datastore
  public function getRawData($type = NULL)
  {
    if (!self::$_ds_driver)
      return FALSE;

    if (!in_array($type, parent::$allowed_data_types))
      return FALSE;

    if (!($ds_result = mysql_query("SELECT * FROM $type", self::$_ds_driver)))
      return FALSE;

    while($row = mysql_fetch_assoc($ds_result))
    {
      $data["{$row['ID']}"] = $row;
    }
    return $data;
  }

  // force sync the cache with this datastore
  public function fsyncCache()
  {
    foreach(parent::$allowed_data_types AS $type)
    {
      if (!($data = self::getInstance()->getRawData($type)))
        return FALSE;

      self::getCache()->write($type, $data);
    }
  }
}