<?php

namespace JDZ\Template\Extension;

/**
 * Renderer extension
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class NoFollowLinksRendererExtension extends RendererExtension
{
  public function render(string $body): string
  {
    return $this->noFollowLinks($body);
  }

  protected function noFollowLinks(string $body): string
  {
    return preg_replace_callback("/<a ([^>]+)>(.*)<\/a>/iUm", function($m){
      $attrs = $this->parseTagAttributes($m[1]);
      $text  = empty($m[2]) ? '' : $m[2];

      if ( !isset($attrs['href']) ){
        $attrs['href'] = '#';
      }

      if ( isset($attrs['class']) ){
        $classes = explode(' ', $attrs['class']);
      }
      else {
        $classes = [];
      }

      if ( preg_match("/^(mailto|tel):.+$/", $attrs['href']) ){
        $attrs['target'] = '_blank';
        $attrs['rel'] = 'nofollow';
      }

      if ( $classes ){
        $attrs['class'] = implode(' ', $classes);
      }

      return '<a'.$this->mergeTagAttributes($attrs).'>'.$text.'</a>';
    }, $body);
  }

  private function parseTagAttributes(string $string): array
  {
    $attr = [];
    $list = [];

    preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

    if ( is_array($attr) ){
      $numPairs = count($attr[1]);
      for ($i = 0; $i < $numPairs; $i++){
        $list[$attr[1][$i]] = $attr[2][$i];
      }
    }

    return $list;
  }

  private function mergeTagAttributes(array $attrs = []): string
  {
    $attrs = (array)$attrs;

    $attributes = [];
    foreach($attrs as $key => $value){
      if ( $key === 'class' && is_array($value) ){
        $value = array_unique($value);
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
    if ( $attrs !== '' ){
      return ' '.$attrs;
    }
    return '';
  }
}
