<?php

namespace JDZ\Template\Tests;

use JDZ\Template\TwigRenderer;
use JDZ\Template\Extension\MergeAttributesTwigExtension;
use PHPUnit\Framework\TestCase;

class TwigRendererTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures';
    }

    private function createRenderer(bool $debug = true): TwigRenderer
    {
        $renderer = new TwigRenderer($debug);
        $renderer->layoutPath = $this->fixturesPath;
        $renderer->layoutFolder = 'views';

        return $renderer;
    }

    public function testConstructorDefaults(): void
    {
        $renderer = new TwigRenderer();

        $this->assertNull($renderer->cacheDir);
        $this->assertSame(date_default_timezone_get(), $renderer->timezone);
    }

    public function testConstructorWithDebugAndCache(): void
    {
        $renderer = new TwigRenderer(true, '/tmp/cache');

        $this->assertSame('/tmp/cache', $renderer->cacheDir);
    }

    public function testLoadTwigAndRenderSimpleTemplate(): void
    {
        $renderer = $this->createRenderer();
        $renderer->viewLayouts = ['simple'];
        $renderer->data = ['name' => 'World'];

        $renderer->loadTwig();
        $output = $renderer->render();

        $this->assertSame('Hello, World!', trim($output));
    }

    public function testRenderWithVariables(): void
    {
        $renderer = $this->createRenderer();
        $renderer->viewLayouts = ['variables'];
        $renderer->data = [
            'title' => 'My Page',
            'items' => ['Alpha', 'Beta', 'Gamma'],
        ];

        $renderer->loadTwig();
        $output = $renderer->render();

        $this->assertStringContainsString('My Page', $output);
        $this->assertStringContainsString('Alpha', $output);
        $this->assertStringContainsString('Beta', $output);
        $this->assertStringContainsString('Gamma', $output);
    }

    public function testAddTwigExtension(): void
    {
        $renderer = $this->createRenderer();
        $renderer->viewLayouts = ['with_extension'];
        $renderer->data = [
            'attrs' => ['class' => 'btn', 'id' => 'submit'],
        ];

        $renderer->loadTwig();
        $renderer->addTwigExtension(new MergeAttributesTwigExtension());
        $output = $renderer->render();

        // Twig auto-escapes the output, so check for escaped quotes
        $this->assertStringContainsString('class=', $output);
        $this->assertStringContainsString('btn', $output);
        $this->assertStringContainsString('id=', $output);
        $this->assertStringContainsString('submit', $output);
    }

    public function testLayoutFallback(): void
    {
        $renderer = $this->createRenderer();
        $renderer->viewLayouts = ['nonexistent', 'simple'];
        $renderer->data = ['name' => 'Fallback'];

        $renderer->loadTwig();
        $output = $renderer->render();

        $this->assertSame('Hello, Fallback!', trim($output));
    }

    public function testThrowsOnMissingLayout(): void
    {
        $renderer = $this->createRenderer();
        $renderer->viewLayouts = ['nonexistent'];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Template Error');

        $renderer->loadTwig();
    }
}
