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
  }
  
  /**
   * {@inheritDoc}
   */
  public function getData()
  {
    return $this->data;
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