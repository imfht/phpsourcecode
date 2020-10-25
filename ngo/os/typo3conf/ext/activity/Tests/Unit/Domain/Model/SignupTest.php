<?php
namespace Jykj\Activity\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class SignupTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Activity\Domain\Model\Signup
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Activity\Domain\Model\Signup();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getSigntimeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSigntime()
        );
    }

    /**
     * @test
     */
    public function setSigntimeForIntSetsSigntime()
    {
        $this->subject->setSigntime(12);

        self::assertAttributeEquals(
            12,
            'signtime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getChecktimeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getChecktime()
        );
    }

    /**
     * @test
     */
    public function setChecktimeForIntSetsChecktime()
    {
        $this->subject->setChecktime(12);

        self::assertAttributeEquals(
            12,
            'checktime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStatusReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusForIntSetsStatus()
    {
        $this->subject->setStatus(12);

        self::assertAttributeEquals(
            12,
            'status',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getActivityuidReturnsInitialValueForActivity()
    {
        self::assertEquals(
            null,
            $this->subject->getActivityuid()
        );
    }

    /**
     * @test
     */
    public function setActivityuidForActivitySetsActivityuid()
    {
        $activityuidFixture = new \Jykj\Activity\Domain\Model\Activity();
        $this->subject->setActivityuid($activityuidFixture);

        self::assertAttributeEquals(
            $activityuidFixture,
            'activityuid',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getVolunteerReturnsInitialValueForVolunteer()
    {
        self::assertEquals(
            null,
            $this->subject->getVolunteer()
        );
    }

    /**
     * @test
     */
    public function setVolunteerForVolunteerSetsVolunteer()
    {
        $volunteerFixture = new \Jykj\Activity\Domain\Model\Volunteer();
        $this->subject->setVolunteer($volunteerFixture);

        self::assertAttributeEquals(
            $volunteerFixture,
            'volunteer',
            $this->subject
        );
    }
}
