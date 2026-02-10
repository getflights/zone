<?php

namespace CommerceGuys\Zone\Tests\Model;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Zone\Model\ZoneMemberCountry;
use CommerceGuys\Zone\Model\ZoneMemberZone;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Zone\Model\ZoneMemberCountry
 */
class ZoneMemberCountryTest extends TestCase
{
    /**
     * @var ZoneMemberZone
     */
    protected $zoneMember;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->zoneMember = new ZoneMemberCountry();
    }

    /**
     * @covers ::getCountryCode
     * @covers ::setCountryCode
     */
    public function testCountryCode()
    {
        $this->zoneMember->setCountryCode('CN');
        $this->assertEquals('CN', $this->zoneMember->getCountryCode());
    }

    /**
     * @covers ::getAdministrativeArea
     * @covers ::setAdministrativeArea
     */
    public function testAdministrativeArea()
    {
        $administrativeArea = $this
            ->getMockBuilder('CommerceGuys\Addressing\Subdivision\Subdivision')
            ->disableOriginalConstructor()
            ->getMock();

        $this->zoneMember->setAdministrativeArea($administrativeArea);
        $this->assertSame($administrativeArea, $this->zoneMember->getAdministrativeArea());
    }

    /**
     * @covers ::getLocality
     * @covers ::setLocality
     */
    public function testLocality()
    {
        $locality = $this
            ->getMockBuilder('CommerceGuys\Addressing\Subdivision\Subdivision')
            ->disableOriginalConstructor()
            ->getMock();

        $this->zoneMember->setLocality($locality);
        $this->assertSame($locality, $this->zoneMember->getLocality());
    }

    /**
     * @covers ::getDependentLocality
     * @covers ::setDependentLocality
     */
    public function testDependentLocality()
    {
        $dependentLocality = $this
            ->getMockBuilder('CommerceGuys\Addressing\Subdivision\Subdivision')
            ->disableOriginalConstructor()
            ->getMock();

        $this->zoneMember->setDependentLocality($dependentLocality);
        $this->assertSame($dependentLocality, $this->zoneMember->getDependentLocality());
    }

    /**
     * @covers ::getIncludedPostalCodes
     * @covers ::setIncludedPostalCodes
     */
    public function testIncludedPostalCodes()
    {
        $this->zoneMember->setIncludedPostalCodes('123, 456, 789');
        $this->assertEquals('123, 456, 789', $this->zoneMember->getIncludedPostalCodes());
    }

    /**
     * @covers ::getExcludedPostalCodes
     * @covers ::setExcludedPostalCodes
     */
    public function testExcludedPostalCodes()
    {
        $this->zoneMember->setExcludedPostalCodes('123, 456, 789');
        $this->assertEquals('123, 456, 789', $this->zoneMember->getExcludedPostalCodes());
    }

    /**
     * @covers ::match
     *
     * @uses         \CommerceGuys\Zone\Model\ZoneMemberCountry::setCountryCode
     * @uses         \CommerceGuys\Zone\Model\ZoneMemberCountry::setAdministrativeArea
     * @uses         \CommerceGuys\Zone\Model\ZoneMemberCountry::setLocality
     * @uses         \CommerceGuys\Zone\Model\ZoneMemberCountry::setDependentLocality
     * @uses         \CommerceGuys\Zone\Model\ZoneMemberCountry::setIncludedPostalCodes
     * @uses         \CommerceGuys\Zone\Model\ZoneMemberCountry::setExcludedPostalCodes
     * @uses         \CommerceGuys\Addressing\PostalCodeHelper
     * @dataProvider addressProvider
     */
    public function testMatch($address, $expectedResult)
    {
        $this->zoneMember->setCountryCode('CN');
        $this->zoneMember->setAdministrativeArea('Hebei Sheng');
        $this->zoneMember->setLocality('Handan Shi');
        $this->zoneMember->setDependentLocality('Ci Xian');
        $this->zoneMember->setIncludedPostalCodes('123456');

        $this->assertEquals($expectedResult, $this->zoneMember->match($address));
    }

    /**
     * Provides addresses and the expected match results.
     */
    public static function addressProvider()
    {
        $emptyAddress = self::getAddress();
        $countryAddress = self::getAddress('CN');
        $administrativeAreaAddress = self::getAddress('CN', 'Hebei Sheng');
        $localityAddress = self::getAddress('CN', 'Hebei Sheng', 'Handan Shi');
        $dependentLocalityAddress = self::getAddress('CN', 'Hebei Sheng', 'Handan Shi', 'Ci Xian');
        $fullAddress = self::getAddress('CN', 'Hebei Sheng', 'Handan Shi', 'Ci Xian', '123456');

        return [
            [$emptyAddress, false],
            [$countryAddress, false],
            [$administrativeAreaAddress, false],
            [$localityAddress, false],
            [$dependentLocalityAddress, false],
            [$fullAddress, true],
        ];
    }

    /**
     * Returns a mock address.
     *
     * @param string|null $countryCode The country code.
     * @param string|null $administrativeArea The administrative area id.
     * @param string|null $locality The locality id.
     * @param string|null $dependentLocality The dependent locality id.
     * @param string|null $postalCode The postal code.
     *
     * @return Address
     */
    protected static function getAddress(
        string|null $countryCode = null,
        string|null $administrativeArea = null,
        string|null $locality = null,
        string|null $dependentLocality = null,
        string|null $postalCode = null
    )
    {
        return new Address(
            $countryCode ?? '',
            $administrativeArea ?? '',
            $locality ?? '',
            $dependentLocality ?? '',
            $postalCode ?? ''
        );
    }
}
