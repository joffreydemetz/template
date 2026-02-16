<?php

namespace JDZ\Template;

use JDZ\Utils\Data;

/**
 * View Data
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ViewData extends Data
{
  public function addJsTranslations(array $translations)
  {
    foreach($translations as $key => $value){
      if ( is_int($key) ){
        $this->addJsTranslation($value);
      }
      else {
        $this->addJsTranslation($key, $value);
      }
    }
    return $this;
  }

  public function addJsTranslation(string $key, ?string $value = null)
  {
    $this->data['i18n'][$key] = $value;
    return $this;
  }
}
