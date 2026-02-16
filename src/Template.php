<?php

namespace JDZ\Template;

use JDZ\Template\Contract\TemplateInterface;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Template implements TemplateInterface
{
  public string $name;
  public TemplateData $data;
  protected array $bodyClasses;
  public string $theme = '';

  public function __construct(?string $name = null, TemplateData|array|null $data = null, array $bodyClasses = [])
  {
    $this->name = $this->parseName($name);
    $this->bodyClasses = $bodyClasses;

    if ($data instanceof TemplateData) {
      $this->data = $data;
    } else {
      $this->data = new TemplateData();

      if ($data) {
        $this->data->sets($data);
      }
    }
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setTheme(string $theme): static
  {
    $this->theme = $theme;
    return $this;
  }

  public function getTheme(): string
  {
    return $this->theme;
  }

  public function setData(TemplateData $data): static
  {
    $this->data = $data;
    return $this;
  }

  public function getData(): TemplateData
  {
    return $this->data;
  }

  public function setBodyClasses(array $bodyClasses): static
  {
    $this->bodyClasses = $bodyClasses;
    return $this;
  }

  public function getBodyClasses(): array
  {
    return $this->bodyClasses;
  }

  public function load(): static
  {
    $this->loadData();
    $this->loadBodyClass();

    if ($typeClass = $this->data->get('typeClass')) {
      $typeClass = array_reverse($typeClass);
      $typeClass = array_unique($typeClass);
      foreach ($typeClass as $class) {
        array_unshift($this->bodyClasses, $class);
      }
    }
    $this->data->erase('typeClass');

    if ('' === $this->theme && 'main' !== $this->name) {
      $this->theme = $this->name;
    }

    if ($this->theme) {
      array_unshift($this->bodyClasses, 'theme-' . $this->theme);
    }

    if ($this->bodyClasses) {
      $this->bodyClasses = array_reverse($this->bodyClasses);
      $this->bodyClasses = array_unique($this->bodyClasses);
      $this->bodyClasses = array_reverse($this->bodyClasses);
    }

    $this->bodyClasses = array_filter($this->bodyClasses, fn($el) => '' !== trim($el));

    $this->data->set('bodyclass', implode(' ', $this->bodyClasses));

    return $this;
  }

  protected function loadData(): void {}

  protected function loadBodyClass(): void {}

  protected function parseName(?string $name): string
  {
    if (!$name) {
      $path = explode('\\', static::class);
      $name = lcfirst(array_pop($path));
    }

    return $name;
  }
}
