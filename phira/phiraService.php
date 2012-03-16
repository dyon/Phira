<?php

// define namespace
namespace phira;

// phiraService class
class phiraService extends \SoapClient
{
  // class constants
  const
    JIRA_RPC_PATH = '/rpc/soap/jirasoapservice-v2?wsdl';
  
  // private class variables
  private 
    $_client, $token;
  
  // private static class variables
  private static $default_options = array(
    'soap_version' => SOAP_1_2,
    'encoding' => 'UTF-8',
    'exceptions' => TRUE,
    "trace" => TRUE
  );
  
  // class constructor
  public function __construct($address, array $options = array())
  {
    // soap client options 
    $options = array_merge(self::$default_options, $options);
    
    // start a new client
    try
    {
      $this->_client = new parent($address . self::JIRA_RPC_PATH, $options);
    }
    catch(\SoapFault $e)
    {
      phiraServiceError::captureError($e);
      return FALSE;
    }
  }
  
  // class destructor
  public function __destruct()
  {
    $this->logout();
  }
  
  // all requests go through here
  public function _createRequest($requestName, array $requestParams = array())
  {
    if (!$this->_client)
      return FALSE;
           
    // try and run request
    try
    {
      return call_user_func_array(array($this->_client, $requestName), $requestParams);
    }
    catch(\SoapFault $e)
    {
      phiraServiceError::captureError($e);
      return FALSE;
    }
      
  }
  
  // client login
  public function login($username, $password)
  {
    return $this->token = $this->_createRequest('login', array($username, $password));
  }
  
  // get server information
  public function getServerInfo()
  {
    return $this->_createRequest('getServerInfo');
  }
  
  // client logout
  public function logout()
  {
    if ($this->token)
      return $this->_createRequest('logout', array($this->token));
  }
  
  // get a jira project - by the project key name
  public function getProject($key)
  {
    if (!$this->token)
      return FALSE;
    
    if (!($project_soap = $this->_createRequest('getProjectByKey', array($this->token, $key))))
      return FALSE;
     
    return new phiraProject($project_soap);
  }
  
  // get an array of jira projects
  public function getProjects()
  {
    if (!$this->token)
      return FALSE;

    if (!($retrived_projects = $this->_createRequest('getProjectsNoSchemes', array($this->token))))
      return FALSE;
      
    array_walk($retrived_projects, create_function('&$v,$k', '$v = new phira\phiraProject($v);'));
    
    return $retrived_projects;
  }
  
  // get an issue - by the issue key name
  public function getIssue($key)
  {
    if (!$this->token)
      return FALSE;
    
    if (!($issue_soap = $this->_createRequest('getIssue', array($this->token, $key))))
      return FALSE;

    return new phiraIssue($issue_soap);
  }
  
  // create an issue
  public function createIssue(phiraProject $project, phiraIssue $issue)
  {  
    $query = array_merge($issue->_toArray(), array('project' => $project->key));  
    
    if (!($issue_soap = $this->_createRequest('createIssue', array($this->token, $query))))
      return FALSE;
      
    return $issue_soap;
  }  
  
  // update an issue
  public function updateIssue($issueKeyID, phiraIssue $issue)
  {  
    $query = $issue->_toArray();  

    foreach($query AS $issue_key => $issue_value)
    {
      if (empty($issue_value))
        continue;
        
      if ((($issue_key == 'customFieldValues')) && (is_array($issue_value)))
      {
        foreach($issue_value AS $cf_key => $cf_value)
        {
          $updated_query[] = array('id' => $cf_value->customfieldId, 'values' => $cf_value->values);
        }
        continue;
      }
      $updated_query[] = array('id' => $issue_key, 'values' => array($issue_value));
    }
    
    if (!($issue_soap = $this->_createRequest('updateIssue', array($this->token, $issueKeyID, $updated_query))))
      return FALSE;
      
    return $issue_soap;
  }
  
  // add comment to an issue
  public function addComment($issueKeyID, $comment)
  {
    if (!($issue_soap = $this->_createRequest('addComment', array($this->token, $issueKeyID, array('body' => $comment)))))
      return FALSE;
      
    return $issue_soap;
  } 
  
  // add attachment to an issue
  public function addAttachment($issueKeyID, array $file_paths = array())
  {
    if (empty($file_paths))
      return FALSE;
    
    $fileNames = array();
    $fileData = array();
    foreach($file_paths AS $file)
    {
      if (!file_exists($file))
        continue;
        
      $fileNames[] = end(explode(DIRECTORY_SEPARATOR, $file));
      $fileData[] = base64_encode(fread(fopen($file, "r"), filesize($file)));
    }
    
    if ((empty($fileNames)) || (empty($fileData)))
      return FALSE;
      
    if (!($issue_soap = $this->_createRequest('addBase64EncodedAttachmentsToIssue', array($this->token, $issueKeyID, $fileNames, $fileData))))
      return FALSE;
      
    return $issue_soap;
  }  
  
  
}

/////////////////////////////////////////////////////////////////////

// phiraServiceError class
class phiraServiceError extends \Exception
{
  // class private variables
  private static 
    $error_stack = array();

  // class constructor
  public function __construct()
  {
    parent::__construct();
  }

  // class destructor
  public function __destruct()
  {
    parent::__destruct();
  }
  
  // store errors in stack
  public function captureError($error)
  {
    array_push(self::$error_stack, $error);
  }
  
  // return the last error in stack
  public function getLastError()
  {
    return end(self::$error_stack);
  }
  
}