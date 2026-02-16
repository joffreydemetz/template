<?php

namespace JDZ\Template\Extension;

use Twig\Extension\AbstractExtension as TwigAbstractExtension;

/**
 * Twig extension
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class MergeAttributesTwigExtension extends TwigAbstractExtension
{
  public function getName()
  {
    return 'jdz.mergeAttributes';
  }

  public function getFunctions()
  {
    return [
      new \Twig\TwigFunction('mergeHtmlAttributes', function($attrs){
        if ( !empty($attrs) ){
          $attrs = (array)$attrs;

          $attributes = [];
          foreach($attrs as $key => $value){
            if ( $key === 'class' && \is_array($value) ){
              $value = \array_unique($value);
              $value = implode(' ', $value);
              if ( empty($value) ){
                continue;
              }
            }

            if ( true === $value ){
              $value = 'true';
            }
            elseif ( false === $value ){
              $value = 'false';
            }
            elseif ( $value ){
              $value = trim($value);
              $value = str_replace('"', '\"', $value);
            }
            else {
              $value = '';
            }

            $attributes[] = $key.'="'.$value.'"';
          }

          $attrs = implode(' ', $attributes);
          if ( '' !== $attrs ){
            return ' '.$attrs;
          }
        }

        return '';
      }),
    ];
  }
}
