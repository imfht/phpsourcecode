<?php

namespace Lxj\Yii2\Tars\Route;

use Lxj\Yii2\Tars\Boot;
use Lxj\Yii2\Tars\Util;
use Tars\core\Request;
use Tars\core\Response;
use Tars\route\Route;
use Yii;
use yii\base\Application as Yii2App;
use yii\base\Event;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\web\ResponseFormatterInterface;

class TarsRoute implements Route
{
    public function dispatch(Request $request, Response $response)
    {
        Boot::handle();

        try {
            clearstatcache();

            list($yii2Request, $yii2Response) = $this->handle($request);

            $this->terminate($yii2Request, $yii2Response);

            $this->clean($yii2Request);

            //send response event
            $this->response($response, $yii2Response);

            Util::app()->state = Yii2App::STATE_END;
        } catch (\Exception $e) {
            $response->status(500);
            $response->send($e->getMessage() . '|' . $e->getTraceAsString());
        }
    }

    /**
     * @param Request $tarsRequest
     * @return array
     */
    protected function handle(Request $tarsRequest)
    {
        ob_start();
        $isObEnd = false;

        $yii2Request = \Lxj\Yii2\Tars\Request::make($tarsRequest)->toYii2();

        $application = Util::app();

        $tarsRequestingEvent = new Event();
        $tarsRequestingEvent->data = [$yii2Request];
        $application->trigger('tarsRequesting', $tarsRequestingEvent);
        $application->state = Yii2App::STATE_BEFORE_REQUEST;
        $application->trigger(Yii2App::EVENT_BEFORE_REQUEST);
        $application->state = Yii2App::STATE_HANDLING_REQUEST;

        $application->set('request', $yii2Request);
        $yii2Response = $application->handleRequest($yii2Request);

        $application->state = Yii2App::STATE_AFTER_REQUEST;
        $application->trigger(Yii2App::EVENT_AFTER_REQUEST);
        $application->state = Yii2App::STATE_SENDING_RESPONSE;

        ob_start();
        $yii2Response->trigger(\yii\web\Response::EVENT_BEFORE_SEND);
        $this->prepareResponse($yii2Response);
        $yii2Response->trigger(\yii\web\Response::EVENT_AFTER_PREPARE);
        $this->getResponseContent($yii2Response);
        $yii2Response->trigger(\yii\web\Response::EVENT_AFTER_SEND);
        $yii2Response->isSent = true;
        $responseContent = ob_get_contents();
        ob_end_clean();

        if (strlen($responseContent) === 0 && ob_get_length() > 0) {
            $yii2Response->content = ob_get_contents();
            ob_end_clean();
            $isObEnd = true;
        }

        if (!$isObEnd) {
            ob_end_flush();
        }

        return [$yii2Request, $yii2Response];
    }

    protected function prepareResponse(\yii\web\Response $response)
    {
        if ($response->statusCode === 204) {
            $response->content = '';
            $response->stream = null;
            return;
        }

        if ($response->stream !== null) {
            return;
        }

        if (isset($response->formatters[$response->format])) {
            $formatter = $response->formatters[$response->format];
            if (!is_object($formatter)) {
                $response->formatters[$response->format] = $formatter = Yii::createObject($formatter);
            }
            if ($formatter instanceof ResponseFormatterInterface) {
                $formatter->format($response);
            } else {
                throw new InvalidConfigException("The '{$response->format}' response formatter is invalid. It must implement the ResponseFormatterInterface.");
            }
        } elseif ($response->format === \yii\web\Response::FORMAT_RAW) {
            if ($response->data !== null) {
                $response->content = $response->data;
            }
        } else {
            throw new InvalidConfigException("Unsupported response format: {$response->format}");
        }

        if (is_array($response->content)) {
            throw new InvalidArgumentException('Response content must not be an array.');
        } elseif (is_object($response->content)) {
            if (method_exists($response->content, '__toString')) {
                $response->content = $response->content->__toString();
            } else {
                throw new InvalidArgumentException('Response content must be a string or an object implementing __toString().');
            }
        }
    }

    protected function getResponseContent(\yii\web\Response $response)
    {
        if ($response->stream === null) {
            echo $response->content;

            return;
        }

        set_time_limit(0); // Reset time limit for big files
        $chunkSize = 8 * 1024 * 1024; // 8MB per chunk

        if (is_array($response->stream)) {
            list($handle, $begin, $end) = $response->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                echo fread($handle, $chunkSize);
                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }
            fclose($handle);
        } else {
            while (!feof($response->stream)) {
                echo fread($response->stream, $chunkSize);
                flush();
            }
            fclose($response->stream);
        }
    }

    protected function terminate($yii2Request, $yii2Response)
    {
        $tarsRequestedEvent = new Event();
        $tarsRequestedEvent->data = [$yii2Request, $yii2Response];
        Util::app()->trigger('tarsRequested', $tarsRequestedEvent);
    }

    protected function clean($yii2Request)
    {
        $app = Util::app();

        if ($app->has('session', true)) {
            $app->getSession()->close();
        }
        if($app->state == -1){
            $app->getLog()->logger->flush(true);
        }
    }

    protected function response($tarsResponse, $yii2Response)
    {
        $application = Util::app();

        $application->state = Yii2App::STATE_SENDING_RESPONSE;

        \Lxj\Yii2\Tars\Response::make($yii2Response, $tarsResponse)->send();
    }
}
