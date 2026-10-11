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
            }
            else {
              $value = '';
            }

            // HTML-escaped (D15: a quote used to become \", which is no escape in
            // HTML); entities already in the value are kept, the stored values
            // carry &amp; and the like and must not turn into a visible "&amp;"
            $value = \htmlspecialchars((string)$value, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8', false);

            $attributes[] = $key.'="'.$value.'"';
          }

          $attrs = implode(' ', $attributes);
          if ( '' !== $attrs ){
            return ' '.$attrs;
          }
        }

        return '';
      }, ['is_safe' => ['html']]),
    ];
  }
}
