<?php

namespace SteamBoat\SteamBoatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SteamId;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $repository = $this->getDoctrine()
          ->getRepository('SteamBoatBundle:SteamIdStorage');
        $steamIdStorage = $repository->findOneByNickname($name);

        return $this->render('SteamBoatBundle:Default:index.html.twig', array(
            'id' => $steamIdStorage,
            'games' => $steamIdStorage->getGames(),
         ));
    }
}
