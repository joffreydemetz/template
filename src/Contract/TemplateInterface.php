<?php

namespace JDZ\Template\Contract;

use JDZ\Template\TemplateData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface TemplateInterface
{
  public function load(): static;

  public function getName(): string;

  public function getData(): TemplateData;
}
