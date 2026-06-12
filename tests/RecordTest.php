<?php
namespace STS\Record\Test;

use Illuminate\Contracts\Support\Arrayable;
use STS\Record\Record;
use Illuminate\Support\HigherOrderCollectionProxy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    #[Test]
    public function it_provides_attribute_access()
    {
        $record = new Record(["foo" => "bar"]);

        $this->assertEquals("bar", $record->foo);
    }

    #[Test]
    public function it_returns_null_for_invalid_attributes()
    {
        $record = new Record(["foo" => "bar"]);

        $this->assertNull($record->baz);
    }

    #[Test]
    public function it_uses_mutators()
    {
        $record = new RecordDouble(["foo" => "hi", "bar" => "there"]);

        $this->assertEquals("hi there", $record->qux);
    }

    #[Test]
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

    #[Test]
    public function it_supports_higher_order()
    {
        $record = new Record(["foo" => "bar"]);

        $this->assertInstanceOf(HigherOrderCollectionProxy::class, $record->each);
    }

    #[Test]
    public function helper_method_returns_record()
    {
        $this->assertInstanceOf(Record::class, record([]));
    }

    #[Test]
    public function it_provides_attribute_setter()
    {
        $record = new Record(["foo" => "bar"]);

        $record->baz = "qux";

        $this->assertEquals("qux", $record->baz);
    }

    #[Test]
    public function it_translates_to_snake_case()
    {
        $record = new Record(["my_foo" => "bar"]);

        $this->assertEquals("bar", $record->myFoo);

        // But still prefer exact match if it exists
        $record = new Record(["my_foo" => "bar", "myFoo" => "baz"]);

        $this->assertEquals("baz", $record->myFoo);
    }

    #[Test]
    public function it_serializes_to_array()
    {
        $record = new Record(["foo" => "bar", "baz" => ["qux" => "corge"]]);

        $array = $record->toArray();

        $this->assertIsArray($array);
        $this->assertIsArray($array['baz']);
        $this->assertEquals(["qux" => "corge"], $array['baz']);
    }
}

class RecordDouble extends Record {
    public function getQuxAttribute()
    {
        return $this->foo . " " . $this->bar;
    }
}
