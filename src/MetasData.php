<?php

namespace JDZ\Template;

/**
 * Metas Data
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class MetasData
{
  public array $data = [];

  public function append(array $data)
  {
    $this->data = array_replace_recursive($this->data, $data);
    return $this;
  }

  public function sets(array $properties)
  {
    foreach($properties as $key => $value){
      $this->set($key, $value);
    }
    return $this;
  }

  public function set(string $key, mixed $value)
  {
    $this->data[$key] = $value;
    return $this;
  }

  public function get(string $key, mixed $default = null)
  {
    if ( $this->has($key) ){
      return $this->data[$key];
    }

    return $default;
  }

  public function def(string $key, $default = '')
  {
    $value = $this->get($key, (string) $default);
    $this->set($key, $value);
    return $this;
  }

  public function has(string $key): bool
  {
    if ( isset($this->data[$key]) ){
      return true;
    }

    return false;
  }

  public function erase(string $key)
  {
    if ( isset($this->data[$key]) ){
      unset($this->data[$key]);
    }

    return $this;
  }

  public function all(): array
  {
    return $this->data;
  }
}
