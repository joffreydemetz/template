<?php

namespace JDZ\Template\Tests;

use JDZ\Template\MetasData;
use PHPUnit\Framework\TestCase;

class MetasDataTest extends TestCase
{
    private MetasData $metas;

    protected function setUp(): void
    {
        $this->metas = new MetasData();
    }

    public function testSetAndGet(): void
    {
        $this->metas->set('title', 'Hello World');

        $this->assertSame('Hello World', $this->metas->get('title'));
    }

    public function testGetReturnsDefaultWhenKeyMissing(): void
    {
        $this->assertNull($this->metas->get('missing'));
        $this->assertSame('fallback', $this->metas->get('missing', 'fallback'));
    }

    public function testHas(): void
    {
        $this->assertFalse($this->metas->has('title'));

        $this->metas->set('title', 'Test');

        $this->assertTrue($this->metas->has('title'));
    }

    public function testErase(): void
    {
        $this->metas->set('title', 'Test');
        $this->assertTrue($this->metas->has('title'));

        $this->metas->erase('title');
        $this->assertFalse($this->metas->has('title'));
    }

    public function testEraseNonExistentKeyDoesNotFail(): void
    {
        $this->metas->erase('nonexistent');
        $this->assertFalse($this->metas->has('nonexistent'));
    }

    public function testSets(): void
    {
        $this->metas->sets([
            'title' => 'Page Title',
            'description' => 'Page Description',
        ]);

        $this->assertSame('Page Title', $this->metas->get('title'));
        $this->assertSame('Page Description', $this->metas->get('description'));
    }

    public function testDef(): void
    {
        // def sets a default only if key is not set
        $this->metas->def('title', 'Default Title');
        $this->assertSame('Default Title', $this->metas->get('title'));

        // def should not overwrite an existing value
        $this->metas->set('title', 'Custom Title');
        $this->metas->def('title', 'Default Title');
        $this->assertSame('Custom Title', $this->metas->get('title'));
    }

    public function testAppend(): void
    {
        $this->metas->set('title', 'Original');
        $this->metas->append(['title' => 'Updated', 'description' => 'New']);

        $this->assertSame('Updated', $this->metas->get('title'));
        $this->assertSame('New', $this->metas->get('description'));
    }

    public function testAppendRecursiveMerge(): void
    {
        $this->metas->set('og', ['title' => 'OG Title', 'type' => 'website']);
        $this->metas->append(['og' => ['title' => 'Updated OG']]);

        $og = $this->metas->get('og');
        $this->assertSame('Updated OG', $og['title']);
        $this->assertSame('website', $og['type']);
    }

    public function testAll(): void
    {
        $this->metas->sets([
            'title' => 'Test',
            'description' => 'Desc',
        ]);

        $all = $this->metas->all();

        $this->assertIsArray($all);
        $this->assertArrayHasKey('title', $all);
        $this->assertArrayHasKey('description', $all);
    }

    public function testFluentInterface(): void
    {
        $result = $this->metas
            ->set('a', 1)
            ->set('b', 2)
            ->erase('a');

        $this->assertInstanceOf(MetasData::class, $result);
        $this->assertFalse($this->metas->has('a'));
        $this->assertTrue($this->metas->has('b'));
    }
}
