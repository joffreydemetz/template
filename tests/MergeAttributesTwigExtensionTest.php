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

    /**
     * D15 (2026-10-10): a double quote was escaped as \" — not HTML: the
     * attribute ended at the quote and the browser showed a backslash. Every
     * value is HTML-escaped now; entities already in the value are kept (the
     * stored values carry &amp; and the like, and render right today).
     */
    public static function escapedValues(): array
    {
        return [
            'double quotes' => ['He said "hello"', 'He said &quot;hello&quot;'],
            'single quotes' => ["l'été", 'l&#039;été'],
            'an ampersand' => ['Tom & Jerry', 'Tom &amp; Jerry'],
            'angle brackets' => ['a <b> c', 'a &lt;b&gt; c'],
            'an attribute break-out' => ['x" onmouseover="alert(1)', 'x&quot; onmouseover=&quot;alert(1)'],
            'an entity already there' => ['Tom &amp; Jerry', 'Tom &amp; Jerry'],
            'a numeric entity already there' => ['caf&#233;', 'caf&#233;'],
            'invalid utf-8' => ["caf\xE9", "caf\u{FFFD}"],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('escapedValues')]
    public function testMergeHtmlAttributesEscapesEveryValueAsHtml(string $value, string $escaped): void
    {
        $callable = $this->ext->getFunctions()[0]->getCallable();

        $this->assertSame(' title="' . $escaped . '"', $callable(['title' => $value]));
    }

    public function testAClassListIsEscapedToo(): void
    {
        $callable = $this->ext->getFunctions()[0]->getCallable();

        $this->assertSame(' class="a&quot;b c"', $callable(['class' => ['a"b', 'c']]));
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
