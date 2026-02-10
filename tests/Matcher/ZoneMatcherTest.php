<?php

namespace CommerceGuys\Addressing\Tests\Repository;

use CommerceGuys\Zone\Model\Zone;
use CommerceGuys\Zone\Matcher\ZoneMatcher;
use CommerceGuys\Zone\Repository\ZoneRepository;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Zone\Matcher\ZoneMatcher
 */
class ZoneMatcherTest extends TestCase
{
    /**
     * Zones.
     *
     * Used for constructing the mocks.
     *
     * @var array
     */
    protected $zones = [
        'fr' => [
            'id' => 'fr',
            'priority' => 0,
            'match' => false,
        ],
        'de' => [
            'id' => 'de',
            'priority' => 0,
            'match' => true,
        ],
        'de2' => [
            'id' => 'de2',
            'priority' => 0,
            'match' => true,
        ],
        'de3' => [
            'id' => 'de3',
            'priority' => 2,
            'match' => true,
        ],
    ];

    /**
     * The zone matcher.
     *
     * @var ZoneMatcher
     */
    protected $matcher;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $zones = [];
        foreach ($this->zones as $definition) {
            $zones[] = $this->getZone($definition['id'], $definition['priority'], $definition['match']);
        }
        $repository = $this
            ->getMockBuilder('CommerceGuys\Zone\Repository\ZoneRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->any())
            ->method('getAll')
            ->willReturn($zones);
        $this->matcher = new ZoneMatcher($repository);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        // Note: other tests use $this->matcher instead of depending on
        // testConstructor because of a phpunit bug with dependencies and mocks:
        // https://github.com/sebastianbergmann/phpunit-mock-objects/issues/127
        $repository = $this
            ->getMockBuilder('CommerceGuys\Zone\Repository\ZoneRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $matcher = new ZoneMatcher($repository);
        // Confirm that the repository was properly set.
        $reflected_constraint = (new \ReflectionObject($matcher))->getProperty('repository');
        $reflected_constraint->setAccessible(TRUE);
        $constraint = $reflected_constraint->getValue($matcher);
        $this->assertInstanceOf(ZoneRepository::class, $constraint);
    }

    /**
     * @covers ::match
     *
     * @uses \CommerceGuys\Zone\Matcher\ZoneMatcher::__construct
     * @uses \CommerceGuys\Zone\Matcher\ZoneMatcher::matchAll
     */
    public function testMatch()
    {
        $address = $this
            ->getMockBuilder('CommerceGuys\Addressing\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $zone = $this->matcher->match($address);
        $this->assertInstanceOf('CommerceGuys\Zone\Model\Zone', $zone);
        $this->assertEquals('de3', $zone->getId());
    }

    /**
     * @covers ::matchAll
     *
     * @uses \CommerceGuys\Zone\Matcher\ZoneMatcher::__construct
     */
    public function testMatchAll()
    {
        $address = $this
            ->getMockBuilder('CommerceGuys\Addressing\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $zones = $this->matcher->matchAll($address);
        $this->assertCount(3, $zones);
        // de3 must come first because it has the highest priority.
        $this->assertEquals('de3', $zones[0]->getId());
        // The other two zones have the same priority, so their order is
        // undefined and different between PHP and HHVM.
        $otherIds = [];
        $otherIds[] = $zones[1]->getId();
        $otherIds[] = $zones[2]->getId();
        $this->assertContains('de2', $otherIds);
        $this->assertContains('de', $otherIds);
    }

    /**
     * Returns a mock zone based on the provided data.
     *
     * @param string $id       The zone id.
     * @param int    $priority The zone priority.
     * @param bool   $match    Whether the zone should match.
     *
     * @return \CommerceGuys\Zone\Model\Zone
     */
    protected function getZone($id, $priority, $match)
    {
        $zone = $this
            ->getMockBuilder('CommerceGuys\Zone\Model\Zone')
            ->disableOriginalConstructor()
            ->getMock();
        $zone
            ->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $zone
            ->expects($this->any())
            ->method('getPriority')
            ->willReturn($priority);
        $zone
            ->expects($this->any())
            ->method('match')
            ->with($this->anything())
            ->willReturn($match);

        return $zone;
    }
}
