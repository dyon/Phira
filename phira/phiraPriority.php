<?php

// define namespace
namespace phira;

// jiraIssue class
class phiraPriority extends phiraDataStore
{
  // class constants
  const
    RESOLVE_TYPE = 'priority',
    RESOLVE_NAME = 'pname';
    
  private
    $_resolver_map_keys = array('ID','SEQUENCE','pname','DESCRIPTION','ICONURL','STATUS_COLOR');
  
  // return value for constant    
  public static function getValueOf($id)
  {
    if (!($resolve_data = parent::getData(self::RESOLVE_TYPE)))
      return FALSE;

    if (!(array_key_exists($id, $resolve_data)))
      return FALSE;
      
    return $resolve_data[$id][self::RESOLVE_NAME];
  }

}