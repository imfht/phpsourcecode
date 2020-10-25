<?php
namespace ImiApp\Module\Api\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Validate\Annotation\Required;
use Imi\Server\Route\Annotation\Action;
use Imi\Controller\SingletonHttpController;
use Imi\Server\Route\Annotation\Controller;
use Imi\Validate\Annotation\AutoValidation;

/**
 * @Controller("/api/")
 */
class ApiController extends SingletonHttpController
{
    /**
     * @Inject("StatisticsService")
     *
     * @var \ImiApp\Service\StatisticsService
     */
    protected $statisticsService;

    /**
     * @Inject("AreaDataService")
     *
     * @var \ImiApp\Service\AreaDataService
     */
    protected $areaDataService;

    /**
     * @Inject("CityDataService")
     *
     * @var \ImiApp\Service\CityDataService
     */
    protected $cityDataService;

    /**
     * @Action
     *
     * @return void
     */
    public function statistics()
    {
        return [
            'data'  =>  $this->statisticsService->getLastModify(),
        ];
    }

    /**
     * @Action
     *
     * @AutoValidation
     * @Required(name="beginDate")
     * @Required(name="endDate")
     * @return void
     */
    public function statisticsDateSpan(string $beginDate, string $endDate)
    {
        return [
            'list'  =>  $this->statisticsService->selectByDateSpan($beginDate, $endDate),
        ];
    }

    /**
     * @Action
     *
     * @param int $city
     * @return void
     */
    public function areas(int $city = 1)
    {
        return $this->areaDataService->selectLastModify(1 == $city);
    }

    /**
     * @Action
     *
     * @AutoValidation
     * @Required(name="countryType")
     * @Required(name="provinceName")
     * @Required(name="beginDate")
     * @Required(name="endDate")
     * @return void
     */
    public function areasDateSpan(int $countryType, string $provinceName, string $beginDate, string $endDate)
    {
        return [
            'list'  =>  $this->areaDataService->selectAreasDateSpan($countryType, $provinceName, $beginDate, $endDate),
        ];
    }

    /**
     * @Action
     *
     * @AutoValidation
     * @Required(name="parentId")
     * @Required(name="cityName")
     * @Required(name="beginDate")
     * @Required(name="endDate")
     * @return void
     */
    public function cityDateSpan(int $parentId, string $cityName, string $beginDate, string $endDate)
    {
        return [
            'list'  =>  $this->cityDataService->selectAreasDateSpan($parentId, $cityName, $beginDate, $endDate),
        ];
    }

}
