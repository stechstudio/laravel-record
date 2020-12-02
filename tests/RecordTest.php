<?php
namespace STS\Record\Test;

use STS\Record\Record;
use Illuminate\Support\HigherOrderCollectionProxy;
use PHPUnit\Framework\TestCase;

/**
 * Class RecordTest
 * @package STS\Record\Test
 */
class RecordTest extends TestCase
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

        $this->assertInstanceOf(Record::class, $record->first());
    }

    /** @test */
    public function it_supports_higher_order()
    {
        $record = new Record(["foo" => "bar"]);

        $this->assertInstanceOf(HigherOrderCollectionProxy::class, $record->each);
    }

    /** @test */
    public function helper_method_returns_record()
    {
        $this->assertInstanceOf(Record::class, record([]));
    }

    /** @test */
    public function it_provides_attribute_setter()
    {
        $record = new Record(["foo" => "bar"]);

        $record->baz = "qux";

        $this->assertEquals("qux", $record->baz);
    }

    /** @test */
    public function it_translates_to_snake_case()
    {
        $record = new Record(["my_foo" => "bar"]);

        $this->assertEquals("bar", $record->myFoo);

        // But still prefer exact match if it exists
        $record = new Record(["my_foo" => "bar", "myFoo" => "baz"]);

        $this->assertEquals("baz", $record->myFoo);
    }
}

class RecordDouble extends Record {
    public function getQuxAttribute()
    {
        return $this->foo . " " . $this->bar;
    }
}
