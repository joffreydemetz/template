<?php

namespace JDZ\Template\Tests;

use JDZ\Template\Template;
use PHPUnit\Framework\TestCase;

/**
 * Concrete stub for testing the abstract Template class
 */
class ConcreteTemplate extends Template
{
    public array $mockBodyClasses = [];
    public array $mockData = [];

    protected function loadData(): void
    {
        if ($this->mockData) {
            $this->data->sets($this->mockData);
        }
    }

    protected function loadBodyClass(): void
    {
        foreach ($this->mockBodyClasses as $class) {
            $this->bodyClasses[] = $class;
        }
    }

    // Expose protected bodyClasses for testing
    public function getBodyClasses(): array
    {
        return $this->bodyClasses;
    }
}

class TemplateTest extends TestCase
{
    public function testParseNameFromClassName(): void
    {
        $template = new ConcreteTemplate();

        $this->assertSame('concreteTemplate', $template->getName());
    }

    public function testParseNameFromExplicitName(): void
    {
        $template = new ConcreteTemplate('custom');

        $this->assertSame('custom', $template->getName());
    }

    public function testGetDataReturnsEmptyByDefault(): void
    {
        $template = new ConcreteTemplate();

        $this->assertIsArray($template->getData());
        $this->assertEmpty($template->getData());
    }

    public function testLoadSetsBodyClass(): void
    {
        $template = new ConcreteTemplate('main', null, ['page-home', 'dark-mode']);
        $template->load();

        $data = $template->getData();
        $this->assertArrayHasKey('bodyclass', $data);
        $this->assertStringContainsString('page-home', $data['bodyclass']);
        $this->assertStringContainsString('dark-mode', $data['bodyclass']);
    }

    public function testLoadReturnsStaticForChaining(): void
    {
        $template = new ConcreteTemplate();
        $result = $template->load();

        $this->assertSame($template, $result);
    }

    public function testLoadWithTheme(): void
    {
        $template = new ConcreteTemplate('page');
        $template->setTheme('blue');
        $template->load();

        $data = $template->getData();
        $this->assertStringContainsString('theme-blue', $data->get('bodyclass'));
    }

    public function testLoadWithoutThemeUsesNameForNonMain(): void
    {
        $template = new ConcreteTemplate('about');
        $template->load();

        $data = $template->getData();
        $this->assertStringContainsString('theme-about', $data->get('bodyclass'));
    }

    public function testLoadMainTemplateWithoutTheme(): void
    {
        $template = new ConcreteTemplate('main');
        $template->load();

        $data = $template->getData();
        // 'main' template without a theme should not add theme- class
        $this->assertStringNotContainsString('theme-', $data->get('bodyclass'));
    }

    public function testLoadWithTypeClassData(): void
    {
        $template = new ConcreteTemplate('main');
        $template->mockData = ['typeClass' => ['type-article', 'type-blog']];
        $template->load();

        $data = $template->getData();
        $this->assertArrayNotHasKey('typeClass', $data);
        $this->assertStringContainsString('type-article', $data['bodyclass']);
        $this->assertStringContainsString('type-blog', $data['bodyclass']);
    }

    public function testLoadDeduplicatesBodyClasses(): void
    {
        $template = new ConcreteTemplate('main');
        $template->mockBodyClasses = ['page', 'page', 'home'];
        $template->load();

        $data = $template->getData();
        $classes = explode(' ', $data['bodyclass']);
        $this->assertCount(count(array_unique($classes)), $classes);
    }

    public function testLoadTrimsEmptyBodyClasses(): void
    {
        $template = new ConcreteTemplate('main');
        $template->mockBodyClasses = ['page', '  ', 'home'];
        $template->load();

        $data = $template->getData();
        $classes = explode(' ', $data['bodyclass']);
        foreach ($classes as $class) {
            $this->assertNotEmpty(trim($class));
        }
    }

    public function testLoadMergesTypeClassAndBodyClasses(): void
    {
        $template = new ConcreteTemplate('main');
        $template->mockData = ['typeClass' => ['type-post']];
        $template->mockBodyClasses = ['page-blog'];
        $template->load();

        $data = $template->getData();
        $this->assertStringContainsString('type-post', $data['bodyclass']);
        $this->assertStringContainsString('page-blog', $data['bodyclass']);
    }
}
