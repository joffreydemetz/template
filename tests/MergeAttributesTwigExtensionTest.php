<?php

namespace JDZ\Template\Tests;

use JDZ\Template\Extension\MergeAttributesTwigExtension;
use PHPUnit\Framework\TestCase;

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
