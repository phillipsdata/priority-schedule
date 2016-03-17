<?php
namespace PhillipsData\PrioritySchedule\Tests\Unit;

use PhillipsData\PrioritySchedule\RoundRobin;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \PhillipsData\PrioritySchedule\RoundRobin
 */
class RoundRobinTest extends PHPUnit_Framework_TestCase
{
    private $rr;

    public function setUp()
    {
        $this->rr = new RoundRobin();
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\PhillipsData\PrioritySchedule\ScheduleInterface',
            new RoundRobin()
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
                    ['c', 0],['e', 0],['a', 1],['d', 2],['b', 2]
                ],
                // Rule
                function ($a, $b) {
                    if ($a[1] === $b[1]) {
                        return 0;
                    }
                    return $a[1] < $b[1]
                        ? 1
                        : -1;
                }
            ]
        ];
    }

    /**
     *
     * @covers ::setCallback
     * @covers ::compare
     * @dataProvider sampleItemsProvider
     */
    public function testCallbackSetsPriority($items, $expected, $rule)
    {
        $this->rr->setCallback($rule);

        foreach ($items as $item) {
            $this->rr->insert($item);
        }

        $actual = [];
        foreach ($this->rr as $item) {
            $actual[] = $item;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::extract
     */
    public function testExtract()
    {
        $item = 'a';
        $this->rr->insert($item);
        $this->assertEquals($item, $this->rr->extract());
    }

    /**
     * @covers ::extract
     * @expectedException \PhillipsData\PrioritySchedule\Exceptions\NoSuchElementException
     */
    public function testExtractException()
    {
        $this->rr->extract();
    }
}
