<?php

namespace JDZ\Template\Tests;

use JDZ\Template\Extension\RendererExtension;
use JDZ\Template\Extension\NoFollowLinksRendererExtension;
use JDZ\Template\Extension\MergeAttributesTwigExtension;
use JDZ\Template\Contract\RendererExtensionInterface;
use PHPUnit\Framework\TestCase;

class RendererExtensionTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $ext = new RendererExtension();
        $this->assertInstanceOf(RendererExtensionInterface::class, $ext);
    }

    public function testRenderReturnsBodyUnchanged(): void
    {
        $ext = new RendererExtension();
        $body = '<p>Hello World</p>';

        $this->assertSame($body, $ext->render($body));
    }

    public function testHtmlPropertyDefaultsFalse(): void
    {
        $ext = new RendererExtension();
        $this->assertFalse($ext->html);
    }
}

class NoFollowLinksRendererExtensionTest extends TestCase
{
    private NoFollowLinksRendererExtension $ext;

    protected function setUp(): void
    {
        $this->ext = new NoFollowLinksRendererExtension();
    }

    public function testExtendsRendererExtension(): void
    {
        $this->assertInstanceOf(RendererExtension::class, $this->ext);
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

class MergeAttributesTwigExtensionTest extends TestCase
{
    private MergeAttributesTwigExtension $ext;

    protected function setUp(): void
    {
        $this->ext = new MergeAttributesTwigExtension();
    }

    public function testGetName(): void
    {
        $this->assertSame('jdz.mergeAttributes', $this->ext->getName());
    }

    public function testGetFunctionsReturnsTwigFunctions(): void
    {
        $functions = $this->ext->getFunctions();

        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
        $this->assertInstanceOf(\Twig\TwigFunction::class, $functions[0]);
    }

    public function testMergeHtmlAttributesFunction(): void
    {
        $functions = $this->ext->getFunctions();
        $callable = $functions[0]->getCallable();

        $result = $callable(['class' => 'btn', 'id' => 'my-btn']);

        $this->assertStringContainsString('class="btn"', $result);
        $this->assertStringContainsString('id="my-btn"', $result);
        // Should start with a space
        $this->assertStringStartsWith(' ', $result);
    }

    public function testMergeHtmlAttributesWithArrayClass(): void
    {
        $functions = $this->ext->getFunctions();
        $callable = $functions[0]->getCallable();

        $result = $callable(['class' => ['btn', 'primary', 'btn']]);

        // Should deduplicate and join
        $this->assertStringContainsString('class="btn primary"', $result);
    }

    public function testMergeHtmlAttributesWithBoolValues(): void
    {
        $functions = $this->ext->getFunctions();
        $callable = $functions[0]->getCallable();

        $result = $callable(['disabled' => true, 'hidden' => false]);

        $this->assertStringContainsString('disabled="true"', $result);
        $this->assertStringContainsString('hidden="false"', $result);
    }

    public function testMergeHtmlAttributesEmptyReturnsEmptyString(): void
    {
        $functions = $this->ext->getFunctions();
        $callable = $functions[0]->getCallable();

        $result = $callable([]);
        $this->assertSame('', $result);

        $result = $callable(null);
        $this->assertSame('', $result);
    }

    public function testMergeHtmlAttributesEscapesQuotes(): void
    {
        $functions = $this->ext->getFunctions();
        $callable = $functions[0]->getCallable();

        $result = $callable(['title' => 'He said "hello"']);

        $this->assertStringContainsString('title="He said \"hello\""', $result);
    }

    public function testIntegrationWithTwig(): void
    {
        $loader = new \Twig\Loader\ArrayLoader([
            'test' => '<div{{ mergeHtmlAttributes(attrs) }}>Content</div>',
        ]);
        $twig = new \Twig\Environment($loader);
        $twig->addExtension($this->ext);

        $output = $twig->render('test', [
            'attrs' => ['class' => 'container', 'data-id' => '42'],
        ]);

        $this->assertStringContainsString('class="container"', $output);
        $this->assertStringContainsString('data-id="42"', $output);
        $this->assertStringContainsString('>Content</div>', $output);
    }
}
