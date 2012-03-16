<?php

// define namespace
namespace phira;

// phiraProject class
class phiraProject extends phiraObject
{
  // protected class variables
  protected
    $description,
    $id,
    $issueSecurityScheme,
    $key,
    $lead,
    $name,
    $notificationScheme,
    $permissionScheme,
    $projectUrl,
    $url;

  // class constructor
  public function __construct($project = NULL)
  {
    if ($project !== NULL)
    {
      foreach($project as $key => $value)
      {   
        $this->$key = $value;
      }
    }
  }

}