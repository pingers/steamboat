<?php

namespace SteamBoat\SteamBoatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', 'text')
            ->add('Go', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'steam_boat_list_games',
                array('name' => $data['name'])
            ));
        }

        return $this->render('SteamBoatBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function listGamesAction($nickname)
    {
        $steamId = $this->get('steam_boat.steamid');
        $steamIdStorage = $steamId->findOneByNickname($nickname);

        return $this->render('SteamBoatBundle:Default:listGames.html.twig', array(
            'id' => $steamIdStorage,
            'games' => $steamIdStorage->getGames(),
         ));
    }

    public function listFriendsAction($nickname)
    {
        $repository = $this->getDoctrine()
            ->getRepository('SteamBoatBundle:SteamIdStorage');
        $steamIdStorage = $repository->findOneByNickname($nickname);

        return $this->render('SteamBoatBundle:Default:listFriends.html.twig', array(
            'id' => $steamIdStorage,
            'friends' => $steamIdStorage->getFriends(),
        ));
    }
}
