<?php


class YuntongxunSmsTest extends \Laravel\Lumen\Testing\TestCase
{

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function test_send_template_sms()
    {
        YuntongxunSms::templateSMS(122850, ['你好', 1878], [18999999999]);
    }
}
