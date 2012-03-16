<?php

// define namespace
namespace phira;

// jiraIssue class
class phiraIssue extends phiraObject
{
  // protected class variables
  protected
    $affectsVersions = array(), 
    $assignee,
    $attachmentNames = array(),
    $components = array(),
    $created,
    $customFieldValues = array(),
    $description,
    $duedate,
    $environment,
    $fixVersions,
    $id,
    $key,
    $priority,
    $project,
    $reporter,
    $resolution,
    $status,
    $summary,
    $type,
    $updated,
    $votes;

  // class constructor
  public function __construct($issue = NULL, $translate = TRUE)
  {
    if ($issue !== NULL)
    {
      foreach($issue as $key => $value)
      {
        if ($translate)
        {
          if ($key == 'customFieldValues')
          {
            foreach($value as $fieldValue)
            {
              $this->addCustomFieldValue(new phiraCustomField($fieldValue));
            }
            continue;
          }
          
          if ($key == 'priority')
          {
            $this->$key = phiraPriority::getValueOf($value);
            continue;
          } 
          
          if ($key == 'resolution')
          {
            $this->$key = phiraResolution::getValueOf($value);
            continue;
          }
          
          if ($key == 'status')
          {
            $this->$key = phiraIssueStatus::getValueOf($value);
            continue;
          }
          
          if ($key == 'type')
          {
            $this->$key = phiraIssueType::getValueOf($value);
            continue;
          }
        }        
        $this->$key = $value;
      }
    }
  }
  
  // add a custom fieldVaue
  public function addCustomFieldValue(phiraCustomField $fieldValue)
  {
    $this->customFieldValues[] = $fieldValue;
  }
  
  // set custom fieldValues
  public function setCustomFieldValues(array $fieldValues)
  {
    $this->customFieldValues = array();
    foreach($fieldValues as $fieldValue)
    {
      $this->addCustomFieldValue($fieldValue);
    }
  }
  
  public function addAttachment() {}
  public function removeAttachment() {}
}