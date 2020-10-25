<?php

namespace Bluehouseapp\Bundle\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BanedIPsControllerTest extends WebTestCase
{

    public function getIP()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $fake = $container->get('davidbadura_faker.faker');
        return $fake->ipv4;
    }
    
    public function testAddBanedIPScenario()
    {
        $client = static::createClient();
        
        // Create a new entry in the database
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /login/");
        
        // Fill in the form and submit it
        $form = $crawler->selectButton('_submit')->form(array(
            '_username'  => 'michael',
            '_password'  => '111111',          
        ));

        $client->submit($form);
        $crawler = $client->followRedirect(true);
        $this->assertTrue(
            $client->getResponse()->isRedirect()
        );
        $crawler = $client->request('GET', '/admin/banedIPs/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/banedips");
        $crawler = $client->click($crawler->selectLink('创建')->link());

        $container = $client->getContainer();
        $fake = $container->get('davidbadura_faker.faker');
        $ipAddress = $fake->ipv4;
        
        $form = $crawler->selectButton('保存')->form(array(
          'blackhouseapp_bluehouseapp_banedIPs[ip]'=> $ipAddress,
          'blackhouseapp_bluehouseapp_banedIPs[fromDate][date][year]'  => '2015',
          'blackhouseapp_bluehouseapp_banedIPs[fromDate][date][month]'  =>'12',
          'blackhouseapp_bluehouseapp_banedIPs[fromDate][date][day]' => '21',
          'blackhouseapp_bluehouseapp_banedIPs[toDate][date][year]' => '2017',
          'blackhouseapp_bluehouseapp_banedIPs[toDate][date][month]' => '1',
          'blackhouseapp_bluehouseapp_banedIPs[toDate][date][day]' => '12' ,   
       )); 

        $client->submit($form);
        $crawler = $client->followRedirect(true);
        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.strval($ipAddress).'")')->count(), 'Missing element td:contains("'.strval($ipAddress).'")');        
    }
    
    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/login/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /login/");
        
        // Fill in the form and submit it
        $form = $crawler->selectButton('Login')->form(array(
            'blackhouseapp_bundle_bluehouseappbundle_banedips[field_name]'  => 'Test',
            'blackhouseapp_bundle_bluehouseappbundle_banedips[field_name]'  => 'Test',          
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
/*
        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'blackhouseapp_bundle_bluehouseappbundle_banedips[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent()); 
    } */


}
