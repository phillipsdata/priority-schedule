<?php
namespace PhillipsData\PrioritySchedule\Tests\Unit;

use PhillipsData\PrioritySchedule\FirstAvailable;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \PhillipsData\PrioritySchedule\FirstAvailable
 */
class FirstAvailableTest extends PHPUnit_Framework_TestCase
{
    private $fa;

    public function setUp()
    {
        $this->fa = new FirstAvailable();
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\PhillipsData\PrioritySchedule\ScheduleInterface',
            new FirstAvailable()
        );
    }

    /**
     * Creates sample items to add to the schedule
     *
     * @return array
     */
    public function sampleItemsProvider()
    {
        return [
            [
                // Items
                [
                    ['a', 1],['b', 2],['c', 0],['d', 2],['e', 0]
                ],
                // Expected
                [
                    ['a', 1],['b', 2],['d', 2]
                ],
                // Rule
                function ($item) {
                    return $item[1] > 0;
                }
            ]
        ];
    }

    /**
     * @covers ::setCallback
     * @covers ::insert
     * @covers ::rewind
     * @covers ::next
     * @covers ::current
     * @covers ::valid
     * @covers ::key
     * @dataProvider sampleItemsProvider
     */
    public function testCallbackSetsPriority($items, $expected, $rule)
    {
        $this->fa->setCallback($rule);

        foreach ($items as $item) {
            $this->fa->insert($item);
        }

        $actual = [];
        foreach ($this->fa as $key => $item) {
            $this->assertGreaterThanOrEqual(0, $key);
            $actual[] = $item;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::count
     * @covers ::insert
     * @dataProvider sampleItemsProvider
     */
    public function testCount($items)
    {
        foreach ($items as $item) {
            $this->fa->insert($item);
        }

        $this->assertEquals(count($items), $this->fa->count());
    }

    /**
     * @covers ::extract
     * @covers ::current
     * @covers ::next
     * @covers ::insert
     */
    public function testExtract()
    {
        $item = 'a';
        $this->fa->insert($item);
        $this->assertEquals($item, $this->fa->extract());
    }

    /**
     * @covers ::extract
     * @covers ::current
     * @expectedException \PhillipsData\PrioritySchedule\Exceptions\NoSuchElementException
     */
    public function testExtractException()
    {
        $this->fa->extract();
    }
}
