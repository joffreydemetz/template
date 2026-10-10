<?php

namespace JDZ\Template\Tests;

use JDZ\Template\Extension\NoFollowLinksRendererExtension;
use JDZ\Template\Extension\RendererExtension;
use PHPUnit\Framework\TestCase;

class NoFollowLinksRendererExtensionTest extends TestCase
{
    private NoFollowLinksRendererExtension $ext;

    protected function setUp(): void
    {
        $this->ext = new NoFollowLinksRendererExtension();
    }

    public function testAddsNoFollowToMailtoLinks(): void
    {
        $html = '<a href="mailto:test@example.com">Email us</a>';
        $result = $this->ext->render($html);

        $this->assertStringContainsString('rel="nofollow"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertStringContainsString('Email us', $result);
    }

    public function testAddsNoFollowToTelLinks(): void
    {
        $html = '<a href="tel:+1234567890">Call us</a>';
        $result = $this->ext->render($html);

        $this->assertStringContainsString('rel="nofollow"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
    }

    public function testDoesNotAddNoFollowToRegularLinks(): void
    {
        $html = '<a href="https://example.com">Visit</a>';
        $result = $this->ext->render($html);

        $this->assertStringNotContainsString('rel="nofollow"', $result);
    }

    public function testPreservesExistingAttributes(): void
    {
        $html = '<a href="mailto:test@example.com" class="link primary">Email</a>';
        $result = $this->ext->render($html);

        $this->assertStringContainsString('class="link primary"', $result);
        $this->assertStringContainsString('rel="nofollow"', $result);
    }

    public function testHandlesLinkWithoutHref(): void
    {
        $html = '<a class="btn">Button</a>';
        $result = $this->ext->render($html);

        $this->assertStringContainsString('href="#"', $result);
    }

    public function testHandlesMultipleLinks(): void
    {
        $html = '<a href="mailto:a@b.com">A</a> <a href="https://example.com">B</a> <a href="tel:123">C</a>';
        $result = $this->ext->render($html);

        // Count nofollow occurrences — should be 2 (mailto + tel)
        $this->assertSame(2, substr_count($result, 'rel="nofollow"'));
    }

    public function testHandlesEmptyBody(): void
    {
        $result = $this->ext->render('');
        $this->assertSame('', $result);
    }

    public function testHandlesBodyWithoutLinks(): void
    {
        $html = '<p>No links here</p>';
        $result = $this->ext->render($html);

        $this->assertSame($html, $result);
    }
}
