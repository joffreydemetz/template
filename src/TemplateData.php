<?php

namespace JDZ\Template;

use ArrayAccess;
use JDZ\Utils\Data as jData;

/**
 * Template Data
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TemplateData extends jData implements \ArrayAccess
{
  public function __construct()
  {
    $this->data['typeClass'] = [];
  }

  public function offsetExists(mixed $path): bool
  {
    return $this->has($path);
  }

  public function offsetGet(mixed $path): mixed
  {
    return $this->get($path);
  }

  public function offsetSet(mixed $path, mixed $value): void
  {
    $this->set($path, $value);
  }

  public function offsetUnset(mixed $path): void
  {
    $this->erase($path);
  }

  public function append(array $data)
  {
    $this->data = array_replace_recursive($this->data, $data);
    return $this;
  }

  public function pushToArray(string $var, mixed $value, string|int|null $key = null)
  {
    if (empty($this->data[$var])) {
      $this->data[$var] = [];
    } elseif (!is_array($this->data[$var])) {
      throw new \RuntimeException('TemplateData::pushToArray needs data[var] to be an array');
    }

    if (null !== $key) {
      $this->data[$var][$key] = $value;
    }

    return $this;
  }

  public function addTypeClass(string $typeClass)
  {
    if (!in_array($typeClass, $this->data['typeClass'])) {
      $this->data['typeClass'][] = $typeClass;
    }
    return $this;
  }
}
