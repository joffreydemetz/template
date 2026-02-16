<?php

namespace JDZ\Template\Tests;

use JDZ\Template\ViewData;
use PHPUnit\Framework\TestCase;

class ViewDataTest extends TestCase
{
    private ViewData $view;

    protected function setUp(): void
    {
        $this->view = new ViewData();
    }

    public function testAddJsTranslation(): void
    {
        $this->view->addJsTranslation('greeting', 'Hello');

        $all = $this->view->all();
        $this->assertArrayHasKey('i18n', $all);
        $this->assertSame('Hello', $all['i18n']['greeting']);
    }

    public function testAddJsTranslationWithNullValue(): void
    {
        $this->view->addJsTranslation('key_only');

        $all = $this->view->all();
        $this->assertNull($all['i18n']['key_only']);
    }

    public function testAddJsTranslations(): void
    {
        $this->view->addJsTranslations([
            'greeting' => 'Hello',
            'farewell' => 'Goodbye',
        ]);

        $all = $this->view->all();
        $this->assertSame('Hello', $all['i18n']['greeting']);
        $this->assertSame('Goodbye', $all['i18n']['farewell']);
    }

    public function testAddJsTranslationsWithIntKeys(): void
    {
        $this->view->addJsTranslations([
            'key1',
            'key2',
        ]);

        $all = $this->view->all();
        $this->assertArrayHasKey('key1', $all['i18n']);
        $this->assertArrayHasKey('key2', $all['i18n']);
    }

    public function testAddJsTranslationsMixedKeys(): void
    {
        $this->view->addJsTranslations([
            'explicit_key' => 'has value',
            'implicit_key',
        ]);

        $all = $this->view->all();
        $this->assertSame('has value', $all['i18n']['explicit_key']);
        $this->assertArrayHasKey('implicit_key', $all['i18n']);
    }

    public function testFluentInterface(): void
    {
        $result = $this->view
            ->addJsTranslation('a', 'A')
            ->addJsTranslations(['b' => 'B']);

        $this->assertInstanceOf(ViewData::class, $result);
    }
}
