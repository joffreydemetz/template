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

    public function testCacheDirReceivesCompiledTemplates(): void
    {
        // A view no other test renders: Twig skips the cache write for a class already loaded
        $root = sys_get_temp_dir() . '/jdz-template-' . uniqid();
        $view = 'cache_' . uniqid();
        mkdir($root . '/views', 0777, true);
        file_put_contents($root . '/views/' . $view . '.tmpl', 'Hello, {{ name }}!');

        $renderer = new TwigRenderer(true, $root . '/cache');
        $renderer->layoutPath = $root;
        $renderer->viewLayouts = [$view];
        $renderer->data = ['name' => 'Cache'];

        $renderer->loadTwig();
        $output = $renderer->render();

        $compiled = 0;
        if (is_dir($root . '/cache')) {
            $compiled = iterator_count(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root . '/cache', \FilesystemIterator::SKIP_DOTS)));
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($root);

        $this->assertSame('Hello, Cache!', trim($output));
        $this->assertGreaterThan(0, $compiled);
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
