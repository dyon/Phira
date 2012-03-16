<?php

// define namespace
namespace phira;

// jiraIssue class
class phiraCustomField extends phiraObject
{
  protected
    $customfieldId,
    $key,
    $values = array(),
    $cfname,
    $description;

  private
    $_resolver_map_keys = array('ID','CUSTOMFIELDTYPEKEY','CUSTOMFIELDSEARCHERKEY','cfname','DESCRIPTION','defaultvalue','FIELDTYPE','PROJECT','ISSUETYPE'),
    $_resolver_map_values = array('ID','CUSTOMFIELD','CUSTOMFIELDCONFIG','PARENTOPTIONID','SEQUENCE','customvalue','optiontype','disabled');

  public function __construct($fieldValue = null, $translate = TRUE)
  {
    if($fieldValue !== null)
    {
      foreach($fieldValue as $key => $value)
      {
        if ($translate)
        {
          if ($key == 'customfieldId')
          {
            $custom_data = self::getFieldData($value);
            $this->cfname = $custom_data['cfname'];
            $this->description = $custom_data['DESCRIPTION'];
          }
          if ($key == 'values')
          {
            foreach($value AS &$values)
            {
              $values = self::getFieldValueData($values);
            }
          }
        }
        $this->$key = $value;
      }
    }
  }

  // return value for field
  public static function getFieldData($id)
  {
    if (!($resolve_data = phiraDataStore::getInstance()->getData('customfield')))
      return FALSE;

    $id = str_replace('customfield_', '', $id);
    if (!(array_key_exists($id, $resolve_data)))
      return FALSE;

    return $resolve_data[$id];
  }

  // return value for field value
  public static function getFieldValueData($id)
  {
    if (!($resolve_data = phiraDataStore::getInstance()->getData('customfieldoption')))
      return FALSE;
    
    if (!(array_key_exists($id, $resolve_data)))
      return $id;

    return $resolve_data[$id]['customvalue'];
  }

}