<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Template;

/**
 * Template interface
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface TemplateInterface 
{
  /**
   * Get the template data
   * 
   * @return array 
   */
  public function getData();
}