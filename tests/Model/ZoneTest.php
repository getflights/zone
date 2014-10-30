<?php

namespace CommerceGuys\Zone\Tests\Model;

use CommerceGuys\Zone\Model\Zone;

/**
 * @coversDefaultClass \CommerceGuys\Zone\Model\Zone
 */
class ZoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zone
     */
    protected $zone;

    public function setUp()
    {
        $this->zone = new Zone();
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testId()
    {
        $this->zone->setId('north_america');
        $this->assertEquals('north_america', $this->zone->getId());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     * @covers ::__toString
     */
    public function testName()
    {
        $this->zone->setName('North America');
        $this->assertEquals('North America', $this->zone->getName());
        $this->assertEquals('North America', (string) $this->zone);
    }

    /**
     * @covers ::getScope
     * @covers ::setScope
     */
    public function testScope()
    {
        $this->zone->setScope('shipping');
        $this->assertEquals('shipping', $this->zone->getScope());
    }

    /**
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testPriority()
    {
        $this->zone->setPriority(10);
        $this->assertEquals(10, $this->zone->getPriority());
    }

    /**
     * @covers ::getMembers
     * @covers ::setMembers
     */
    public function testMembers()
    {
        $zoneMember = $this
            ->getMockBuilder('CommerceGuys\Zone\Model\ZoneMember')
            ->getMock();
        $members = array($zoneMember);

        $this->zone->setMembers($members);
        $this->assertEquals($members, $this->zone->getMembers());
    }

    /**
     * @covers ::match
     * @uses \CommerceGuys\Zone\Model\Zone::setMembers
     */
    public function testMatch()
    {
        $address = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $matchingZoneMember = $this
            ->getMockBuilder('CommerceGuys\Zone\Model\ZoneMember')
            ->getMock();
        $matchingZoneMember
            ->expects($this->any())
            ->method('match')
            ->with($address)
            ->will($this->returnValue(true));
        $nonMatchingZoneMember = $this
            ->getMockBuilder('CommerceGuys\Zone\Model\ZoneMember')
            ->getMock();
        $nonMatchingZoneMember
            ->expects($this->any())
            ->method('match')
            ->with($address)
            ->will($this->returnValue(false));

        $members = array($matchingZoneMember, $nonMatchingZoneMember);
        $this->zone->setMembers($members);
        $this->assertEquals(true, $this->zone->match($address));

        $members = array($nonMatchingZoneMember);
        $this->zone->setMembers($members);
        $this->assertEquals(false, $this->zone->match($address));
    }
}