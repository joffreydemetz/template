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
  public function getFunctions()
  {
    $functions = [];
    
    if ( function_exists('Str') ){
      $functions[] = new Twig_Function('str', function(){
        $args = func_get_args();
        return call_user_func_array('Str', $args);
      });
    }
    
    if ( function_exists('Route') ){
      $functions[] = new Twig_Function('route', function(){
        $args = func_get_args();
        return call_user_func_array('Route', $args);
      });
    }
    
    if ( function_exists('i18n') ){
      $functions[] = new Twig_Function('i18n', function(){
        $args = func_get_args();
        return call_user_func_array('i18n', $args);
      });
    }
    
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
    
    $functions[] = new Twig_Function('preg_match', function($regex, $value){
      return preg_match("/$regex/", $value);
    });
    
    if ( defined('IAPP') ){
      $functions[] = new Twig_Function('auth', function($action){
        return Callisto()->user->authorise($action);
      });
    }
    
    return $functions;
  }
}
