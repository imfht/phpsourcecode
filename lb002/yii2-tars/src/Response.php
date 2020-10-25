<?php

namespace Lxj\Yii2\Tars;

use Tars\core\Response as TarsResponse;

class Response
{
    /**
     * @var TarsResponse
     */
    protected $tarsResponse;

    /**
     * @var \yii\web\Response
     */
    protected $yii2Response;

    /**
     * Make a response.
     *
     * @param \yii\web\Response $yii2Response
     * @param TarsResponse $tarsResponse
     * @return Response
     */
    public static function make(\yii\web\Response $yii2Response, TarsResponse $tarsResponse)
    {
        return new static($yii2Response, $tarsResponse);
    }

    /**
     * Response constructor.
     * @param \yii\web\Response $yii2Response
     * @param TarsResponse $tarsResponse
     */
    public function __construct(\yii\web\Response $yii2Response, TarsResponse $tarsResponse)
    {
        $this->setYii2Response($yii2Response);
        $this->setTarsResponse($tarsResponse);
    }

    /**
     * Sends HTTP headers and content.
     *
     * @throws \InvalidArgumentException
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Sends HTTP headers.
     *
     * @throws \InvalidArgumentException
     */
    protected function sendHeaders()
    {
        $yii2Response = $this->getYii2Response();

        /* RFC2616 - 14.18 says all Responses need to have a Date */
        if (! $yii2Response->headers->has('Date')) {
            $yii2Response->headers->set('Date', \DateTime::createFromFormat('U', time()));
        }

        // headers
        foreach ($yii2Response->headers->getIterator() as $name => $values) {
            foreach ($values as $value) {
                $this->tarsResponse->header($name, $value);
            }
        }

        // status
        $this->tarsResponse->status($yii2Response->getStatusCode());
    }

    /**
     * Sends HTTP content.
     */
    protected function sendContent()
    {
        $yii2Response = $this->getYii2Response();

        $this->tarsResponse->resource->end($yii2Response->content);
    }

    /**
     * @param TarsResponse $tarsResponse
     * @return $this
     */
    protected function setTarsResponse(TarsResponse $tarsResponse)
    {
        $this->tarsResponse = $tarsResponse;

        return $this;
    }

    /**
     * @return tarsResponse
     */
    public function getTarsResponse()
    {
        return $this->tarsResponse;
    }

    /**
     * @param $yii2Response
     * @return $this
     */
    protected function setYii2Response($yii2Response)
    {
        $this->yii2Response = $yii2Response;

        return $this;
    }

    /**
     * @return \yii\web\Response
     */
    public function getYii2Response()
    {
        return $this->yii2Response;
    }
}
