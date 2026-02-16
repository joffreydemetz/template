<?php

namespace JDZ\Template\Extension;

use JDZ\Template\Contract\RendererExtensionInterface;

/**
 * Renderer extension
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class RendererExtension implements RendererExtensionInterface
{
  public bool $html = false;

  public function render(string $body): string
  {
    return $body;
  }
}
