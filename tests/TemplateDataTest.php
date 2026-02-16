<?php

namespace JDZ\Template\Tests;

use JDZ\Template\TemplateData;
use PHPUnit\Framework\TestCase;

class TemplateDataTest extends TestCase
{
    private TemplateData $data;

    protected function setUp(): void
    {
        $this->data = new TemplateData();
    }

    public function testConstructorInitializesTypeClass(): void
    {
        $all = $this->data->all();
        $this->assertArrayHasKey('typeClass', $all);
        $this->assertIsArray($all['typeClass']);
        $this->assertEmpty($all['typeClass']);
    }

    public function testAppendMergesData(): void
    {
        $this->data->append(['title' => 'Hello']);
        $this->data->append(['description' => 'World']);

        $all = $this->data->all();
        $this->assertSame('Hello', $all['title']);
        $this->assertSame('World', $all['description']);
    }

    public function testAppendRecursiveMerge(): void
    {
        $this->data->append(['nested' => ['a' => 1, 'b' => 2]]);
        $this->data->append(['nested' => ['b' => 3, 'c' => 4]]);

        $all = $this->data->all();
        $this->assertSame(1, $all['nested']['a']);
        $this->assertSame(3, $all['nested']['b']);
        $this->assertSame(4, $all['nested']['c']);
    }

    public function testPushToArrayCreatesNewArray(): void
    {
        $this->data->pushToArray('items', 'value1', 'key1');

        $all = $this->data->all();
        $this->assertArrayHasKey('items', $all);
        $this->assertSame('value1', $all['items']['key1']);
    }

    public function testPushToArrayAppendsToExistingArray(): void
    {
        $this->data->pushToArray('items', 'value1', 'key1');
        $this->data->pushToArray('items', 'value2', 'key2');

        $all = $this->data->all();
        $this->assertSame('value1', $all['items']['key1']);
        $this->assertSame('value2', $all['items']['key2']);
    }

    public function testPushToArrayThrowsWhenNotArray(): void
    {
        $this->data->set('scalar', 'string_value');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('TemplateData::pushToArray needs data[var] to be an array');

        $this->data->pushToArray('scalar', 'value', 'key');
    }

    public function testAddTypeClass(): void
    {
        $this->data->addTypeClass('page-home');

        $all = $this->data->all();
        $this->assertContains('page-home', $all['typeClass']);
    }

    public function testAddTypeClassPreventsDuplicates(): void
    {
        $this->data->addTypeClass('page-home');
        $this->data->addTypeClass('page-home');
        $this->data->addTypeClass('page-about');

        $all = $this->data->all();
        $this->assertCount(2, $all['typeClass']);
        $this->assertContains('page-home', $all['typeClass']);
        $this->assertContains('page-about', $all['typeClass']);
    }

    public function testFluentInterface(): void
    {
        $result = $this->data
            ->append(['title' => 'Test'])
            ->addTypeClass('my-class')
            ->pushToArray('items', 'val', 'key');

        $this->assertInstanceOf(TemplateData::class, $result);
    }
}
