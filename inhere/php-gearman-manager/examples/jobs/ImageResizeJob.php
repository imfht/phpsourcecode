<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/4/28
 * Time: 下午9:23
 */

namespace inhere\gearman\examples\jobs;

/**
 * Class ImageResizeJob
 * @package inhere\gearman\examples\jobs
 */
class ImageResizeJob extends Job
{
    /**
     * {@inheritDoc}
     */
    protected function doRun($workload, \GearmanJob $job)
    {
        $data = unserialize($workload);

        if (!$data['src'] || !$data['dst'] || !$data['x']) {
            $job->sendFail();
            print_r($data);

            return false;
        }

        echo $job->handle() . " - creating: $data[dest] x:$data[x] y:$data[y]\n";

        $im = new \Imagick();
        $im->readImage($data['src']);
        $im->thumbnailImage($data['x'], $data['y']);
        $im->writeImage($data['dst']);
        $im->destroy();

        $job->sendStatus(1, 1);

        return $data['dst'];
    }
}
