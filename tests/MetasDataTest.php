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

    public function testAppendRecursiveMerge(): void
    {
        $this->metas->set('og', ['title' => 'OG Title', 'type' => 'website']);
        $this->metas->append(['og' => ['title' => 'Updated OG']]);

        $og = $this->metas->get('og');
        $this->assertSame('Updated OG', $og['title']);
        $this->assertSame('website', $og['type']);
    }
}
