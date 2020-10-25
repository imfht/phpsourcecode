<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/1/29
 * Time: 16:56
 */
class NotifyTypeControllerTest extends TestCase
{
  public function setUp()
  {
    parent::setUp();

    Artisan::call('migrate');

    $this->notifyTypeController = $this->app->make('NotifyTypeController');
  }

  public function testIndex()
  {
    $resp = $this->notifyTypeController->index();
    $this->assertEquals(200, $resp->getStatusCode());
  }

  private $notifyTypeController;
}