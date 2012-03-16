<?php

// define namespace
namespace phira;

interface phiraCacheEngineInterface
{
  function write($key, $data, $options = array());
  function read($key, $options = array());
  function delete($key, $options = array());
  function clear($expired = TRUE);
  function gc();
  public static function &getInstance($configs = array());
}

class phiraCache
{

  private static
    $_instances = array();
  
  public static function &getInstance($configName = null, $engine = null, $configs = array())
  {
    if (empty($configName))
    {
      $configName = 'default';
    }

    if (empty($engine))
    {
      $engine = 'file';
    }

    if (isset(self::$_instances[$configName]))
    {
      return self::$_instances[$configName];
    }

    if (empty(self::$_instances))
    {
      $default = TRUE;
    }

    $engine = strtolower($engine);

    switch ($engine)
    {
      case 'file':
      default:
        self::$_instances[$configName] = new phiraFileCache($configs);
        break;
    }

    return self::$_instances[$configName];
  }

  public static function write($key, $data, $options = array(), $configName = 'default')
  {
    $_this = self::getInstance($configName);
    return $_this->write($key, $data, $options);
  }

  public static function read($key, $options = array(), $configName = 'default')
  {
    $_this = self::getInstance($configName);
    return $_this->read($key, $options);
  }

  public static function delete($key, $options = array(), $configName = 'default')
  {
    $_this = self::getInstance($configName);
    return $_this->delete($key, $options);
  }
  
}


class phiraFileCache implements phiraCacheEngineInterface{
  
  private static
    $_instance;

  protected
    $_configs = array();

  function  __construct($configs = array())
  {
    $this->config($configs);

    // run garbage collection
    if (rand(1, $this->_configs['gc']) === 1) {
      $this->gc();
    }
  }

  public static function &getInstance($configs = array())
  {
    if (is_null(self::$_instance))
    {
      self::$_instance = new self($configs);
    }
    return self::$_instance;
  }

  public function &config($configs = array())
  {
    // default path modified to work with ci cache
    $default = array('path' => './cache/', 'prefix' => 'phiracache_', 'expire' => 10, 'gc' => 100);
    $this->_configs = array_merge($default, $configs);
    return $this;
  }

  public function write($key, $data, $options = array())
  {
    // check is writable
    if (!is_writable($this->_configs['path']))
    {
      echo $this->_configs['path'];
      return FALSE;
    }

    // Prepare data for writing
    if (!empty($options['expire']))
    {
      $expire = $options['expire'];
    }
    else
    {
      $expire = $this->_configs['expire'];
    }

    if (is_string($expire))
    {
      $expire = strtotime($expire);
    }
    else
    {
      $expire = time() + $expire;
    }

    $data = serialize(array('expire' => $expire, 'data' => $data));

    $fileName = $this->_configs['path'] . $this->_configs['prefix'] . $key;

    // Write data to files
    if (file_put_contents($fileName, $data, LOCK_EX))
    {
      return TRUE;
    }
    else
    {
      return FALSE;
    }
  }

  public function read($key, $options = array())
  {
    $fileName = $this->_configs['path'] . $this->_configs['prefix'] . $key;

    if (!file_exists($fileName))
      return FALSE;

    if (!is_readable($fileName))
      return FALSE;

    $data = file_get_contents($fileName);
    if ($data === FALSE)
      return FALSE;

    $data = unserialize($data);

    if ($data['expire'] < time())
    {
      $this->delete($key);
      return FALSE;
    }

    return $data['data'];
  }

  public function delete($key, $options = array())
  {
    $fileName = $this->_configs['path'] . $this->_configs['prefix'] . $key;
    if (!file_exists($fileName) || !is_writable($fileName))
    {
      return FALSE;
    }
    return unlink($fileName);
  }

  public function clear($expired = TRUE)
  {
    $entries = glob($this->_configs['path'] . $this->_configs['prefix'] . "*");

    if (!is_array($entries))
    {
      return FALSE;
    }

    foreach ($entries as $item)
    {
      if (!is_file($item) || !is_writable($item))
      {
        continue;
      }

      if ($expired)
      {
        $expire = file_get_contents($item, null, null, 20, 11);

        $strpos = strpos($expire, ';');
        if ($strpos !== FALSE)
        {
          $expire = substr($expire, 0, $strpos);
        }

        if ($expire > time())
        {
          continue;
        }
      }

      if (!unlink($item))
      {
        return FALSE;
      }
    }

    return TRUE;
  }

  public function gc()
  {
    return $this->clear(TRUE);
  }
}

?>