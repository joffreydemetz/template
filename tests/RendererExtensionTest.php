<?php

namespace JDZ\Template\Tests;

use JDZ\Template\Contract\RendererExtensionInterface;
use JDZ\Template\Extension\RendererExtension;
use PHPUnit\Framework\TestCase;

class RendererExtensionTest extends TestCase
{
    public function testRenderReturnsBodyUnchanged(): void
    {
        $ext = new RendererExtension();
        $body = '<p>Hello World</p>';

        $this->assertSame($body, $ext->render($body));
    }
}
