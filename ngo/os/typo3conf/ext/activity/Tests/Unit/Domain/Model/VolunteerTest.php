<?php
namespace Jykj\Activity\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class VolunteerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Activity\Domain\Model\Volunteer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Activity\Domain\Model\Volunteer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getBirthdayReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getBirthday()
        );
    }

    /**
     * @test
     */
    public function setBirthdayForStringSetsBirthday()
    {
        $this->subject->setBirthday('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'birthday',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEmailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->subject->setEmail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'email',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTelephoneReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTelephone()
        );
    }

    /**
     * @test
     */
    public function setTelephoneForStringSetsTelephone()
    {
        $this->subject->setTelephone('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'telephone',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getQqcodeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getQqcode()
        );
    }

    /**
     * @test
     */
    public function setQqcodeForStringSetsQqcode()
    {
        $this->subject->setQqcode('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'qqcode',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getWeiboReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getWeibo()
        );
    }

    /**
     * @test
     */
    public function setWeiboForStringSetsWeibo()
    {
        $this->subject->setWeibo('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'weibo',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDescritpionReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDescritpion()
        );
    }

    /**
     * @test
     */
    public function setDescritpionForStringSetsDescritpion()
    {
        $this->subject->setDescritpion('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'descritpion',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getIsexperienceReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getIsexperience()
        );
    }

    /**
     * @test
     */
    public function setIsexperienceForIntSetsIsexperience()
    {
        $this->subject->setIsexperience(12);

        self::assertAttributeEquals(
            12,
            'isexperience',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSkillReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSkill()
        );
    }

    /**
     * @test
     */
    public function setSkillForStringSetsSkill()
    {
        $this->subject->setSkill('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'skill',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDutyReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDuty()
        );
    }

    /**
     * @test
     */
    public function setDutyForStringSetsDuty()
    {
        $this->subject->setDuty('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'duty',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getOrgReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getOrg()
        );
    }

    /**
     * @test
     */
    public function setOrgForStringSetsOrg()
    {
        $this->subject->setOrg('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'org',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getRanksReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getRanks()
        );
    }

    /**
     * @test
     */
    public function setRanksForStringSetsRanks()
    {
        $this->subject->setRanks('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'ranks',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getWcheatReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getWcheat()
        );
    }

    /**
     * @test
     */
    public function setWcheatForStringSetsWcheat()
    {
        $this->subject->setWcheat('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'wcheat',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getIdcardReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getIdcard()
        );
    }

    /**
     * @test
     */
    public function setIdcardForStringSetsIdcard()
    {
        $this->subject->setIdcard('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'idcard',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEmcontactReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmcontact()
        );
    }

    /**
     * @test
     */
    public function setEmcontactForStringSetsEmcontact()
    {
        $this->subject->setEmcontact('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'emcontact',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEmtelephoneReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmtelephone()
        );
    }

    /**
     * @test
     */
    public function setEmtelephoneForStringSetsEmtelephone()
    {
        $this->subject->setEmtelephone('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'emtelephone',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSexReturnsInitialValueForDictitem()
    {
    }

    /**
     * @test
     */
    public function setSexForDictitemSetsSex()
    {
    }

    /**
     * @test
     */
    public function getProvinceReturnsInitialValueForArea()
    {
    }

    /**
     * @test
     */
    public function setProvinceForAreaSetsProvince()
    {
    }

    /**
     * @test
     */
    public function getCommunityReturnsInitialValueForDictitem()
    {
    }

    /**
     * @test
     */
    public function setCommunityForDictitemSetsCommunity()
    {
    }

    /**
     * @test
     */
    public function getIdentityReturnsInitialValueForDictitem()
    {
    }

    /**
     * @test
     */
    public function setIdentityForDictitemSetsIdentity()
    {
    }
}
