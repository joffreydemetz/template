<?php

namespace JDZ\Template\Contract;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface RendererExtensionInterface
{
  public function render(string $body): string;
}
