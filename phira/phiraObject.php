<?php

// define namespace
namespace phira;

// phiraObject class
abstract class phiraObject
{

  // setter function
  public function __set($property, $value)
  {
    if ($this->_check($property))
      $this->$property = $value;
  }
  
  // getter function
  public function __get($property)
  {
    if ($this->_check($property))
      return $this->$property;
  }
  
  // isset function
  public function __isset($property)
  {
    return $this->$property !== NULL;
  }
  
  // unset function
  public function __unset($property)
  {
    if ($this->__check($property))
      unset($this->$property);
  }
  
  // tostring function
  public function __toString()
  {
    $buffer = '';
    foreach($this as $property => $value)
    {
      $buffer .= $property . '=>' . (string) $value . PHP_EOL;
    }
    return $buffer;    
  }
  
  // toarray function
  public function _toArray()
  {
    $classArray = array();
    foreach($this as $property => $value)
    {
      $v = $value;
      if ($value instanceof self)
      {
        $v = $value->_toArray();
      }
      $classArray[$property] = $v;
    }
    return $classArray;  
  }
  
  // check function
  private function _check($property)
  {
    return property_exists($this, $property);
  }
  
}