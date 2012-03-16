<?php

// defines
define('PHIRA_SERVICE_URL', '<url-to-jira>');
define('PHIRA_BASE_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);

// dont override other autoloads
if (FALSE === spl_autoload_functions())
{
  if (function_exists('__autoload'))
  {
    spl_autoload_register('__autoload', FALSE);
  }
}

// autoload class function
function __autoload($className)
{
  $path = str_replace('\\', '/', ltrim($className, '\\'));
  if (file_exists(PHIRA_BASE_PATH . $path . '.php'))
  {
    require_once(PHIRA_BASE_PATH . $path . '.php');
  }
}

// return phiraService
return new phira\phiraService(PHIRA_SERVICE_URL);