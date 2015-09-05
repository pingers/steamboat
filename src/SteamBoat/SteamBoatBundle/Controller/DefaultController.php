<?php

namespace SteamBoat\SteamBoatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('nickname', 'text')
            ->add('Go', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'steam_boat_list_games',
                ['nickname' => $data['nickname']]
            ));
        }

        return $this->render('SteamBoatBundle:Default:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function listGamesAction($nickname)
    {
        $steamId = $this->get('steam_boat.steamid');
        $steamIdStorage = $steamId->findOneByNickname($nickname, TRUE);

        return $this->render('SteamBoatBundle:Default:listGames.html.twig', [
            'id' => $steamIdStorage,
            'games' => $steamIdStorage->getGames(),
         ]);
    }

    public function listFriendsAction(Request $request, $nickname)
    {
        $steamId = $this->get('steam_boat.steamid');
        $steamIdStorage = $steamId->findOneByNickname($nickname, TRUE);

        $friends = $steamIdStorage->getFriends();
        $builder = $this->createFormBuilder()
            ->add('Go', 'submit');

        foreach ($friends as $friendIdStorage) {
            $builder->add($friendIdStorage->getSteamId64(), 'checkbox', [
                'label' => $friendIdStorage->getNickname(),
                'required' => false,
            ]);
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $selectedFriends = array_keys(array_filter($data, function($value) { return $value; }));
            $commonGames = $steamId->findCommonGames($steamIdStorage->getSteamId64(), $selectedFriends);

            return $this->render('SteamBoatBundle:Default:listGamesInCommon.html.twig', [
                'id' => $steamIdStorage,
                'selectedFriends' => $selectedFriends,
                'commonGames' => $commonGames,
            ]);
        }

        return $this->render('SteamBoatBundle:Default:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
