<?php
namespace STS\Record\Test;

use STS\Record\Record;

/**
 * Class RecordTest
 * @package STS\Record\Test
 */
class RecordTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_provides_attribute_access()
    {
        $record = new Record(["foo" => "bar"]);

        $this->assertEquals("bar", $record->foo);
    }

    /** @test */
    public function it_returns_null_for_invalid_attributes()
    {
        $record = new Record(["foo" => "bar"]);

        $this->assertNull($record->baz);
    }

    /** @test */
    public function it_uses_mutators()
    {
        $record = new RecordDouble(["foo" => "hi", "bar" => "there"]);

        $this->assertEquals("hi there", $record->qux);
    }

    /** @test */
    public function it_returns_record_for_sub_arrays()
    {
        $record = new Record([
            "foo" => [
                "bar" => [
                    "qux" => ["a", "b", "c"]
                ]
            ]
        ]);

        $this->assertInstanceOf(Record::class, $record->foo);
        $this->assertInstanceOf(Record::class, $record->foo->bar);
        $this->assertEquals(3, $record->foo->bar->qux->count());
    }

    /** @test */
    public function helper_method_returns_record()
    {
        $this->assertInstanceOf(Record::class, record([]));
    }
}

class RecordDouble extends Record {
    public function getQuxAttribute()
    {
        return $this->foo . " " . $this->bar;
    }
}