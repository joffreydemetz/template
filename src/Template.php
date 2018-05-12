<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Template;

/**
 * Template
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Template implements TemplateInterface
{
  /**
   * Template data (key/value pairs)
   * 
   * @var   array 
   */
  protected $data;
  
  /**
   * Construct
   * 
   * @var   array   $data   Key/value pairs of tempalte data
   */
  public function __construct(array $data=[])
  {
    $this->data = $data;
    
    $this->setData();
    $this->setBodyClass();
  }
  
  /**
   * {@inheritDoc}
   */
  public function getData()
  {
    return $this->data;
  }
  
  /**
   * Set the body class
   * 
   * @return  void
   */
  protected function setBodyClass()
  {
    $bodyClass = $this->prepareBodyClass();
    
    $bodyClass = array_unique($bodyClass);
    
    foreach($bodyClass as $_i => $_class){
      if ( trim($_class) === '' ){
        unset($bodyClass[$_i]);
      }
    }
    
    $this->data['bodyclass'] = implode(' ', $bodyClass);
  }
  
  /**
   * Prepare the body class
   * 
   * @param   array  $classes
   * @return  array
   */
  protected function prepareBodyClass(array $classes=[])
  {
    $_classes = [];
    
    if ( !empty($this->data['doc']) && !empty($this->data['doc']['bodyclass']) ){
      $tmp = is_array($this->data['doc']['bodyclass']) ? $this->data['doc']['bodyclass'] : explode(' ', $this->data['doc']['bodyclass']);
      $_classes = array_merge($_classes, $tmp);
    }
    
    if ( !empty($this->data['bodyclass']) ){
      $tmp = is_array($this->data['bodyclass']) ? $this->data['bodyclass'] : explode(' ', $this->data['bodyclass']);
      $_classes = array_merge($_classes, $tmp);
    }
    
    $classes = array_merge($_classes, $classes);
    
    return $classes;
  }
  
  /**
   * Gather infos about modules, etc. for the template display
   * 
   * @return void 
   */
  protected function setData()
  {
  }
}