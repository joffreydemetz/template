<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Template;

use Twig_Extension;
use Twig_Function;

/**
 * Twig extension
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TwigExtension extends Twig_Extension
{
  /**
   * Returns a list of functions to add to the existing list
   *
   * @return array  An array of functions
   */
  public function getFunctions()
  {
    $functions = [];
    
    if ( is_callable(['\\JDZ\\Helpers\\AttributesHelper', 'merge']) ){
      $functions[] = new Twig_Function('mergeHtmlAttributes', function($attrs){
        if ( empty($attrs) ){
          return '';
        }
        return \JDZ\Helpers\AttributesHelper::merge($attrs);
      });
    }
    
    $functions[] = new Twig_Function('is_array', function($value){
      return is_array($value);
    });
    
    $functions[] = new Twig_Function('preg_match', function($regex, $value, $flags=''){
      return preg_match("/$regex/$flags", $value);
    });
    // $functions[] = new Twig_Function('preg_match', function($regex, $value){
      // return preg_match("/$regex/", $value);
    // });
    
    return $functions;
  }
}
