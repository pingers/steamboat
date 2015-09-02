<?php

namespace SteamBoat\SteamBoatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/steam');

        $this->assertTrue($crawler->filter('html:contains("Nickname")')->count() > 0);
    }
}
