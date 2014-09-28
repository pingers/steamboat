<?php

namespace SteamBoat\SteamBoatBundle\Entity;

use Doctrine\ORM\EntityRepository;
use SteamId;

/**
 * SteamIdStorageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SteamIdStorageRepository extends EntityRepository
{
/**
 * Retrieve a SteamId by Nickname.
 *
 * @param $nickname
 * @return object|null
 */
    public function findOneByNickname($nickname) {
        $steamIdStorage = null;

        // Check local cache.
        if ($steamIdStorage = $this->findOneBy(array('nickname' => $nickname))) {
            return $steamIdStorage;
        }
        // Fetch remotely.
        elseif ($steamId = SteamId::create($nickname)) {
            if ($steamIdStorage = $this->createSteamIdStorage($steamId)) {
                // Cache in the database.
                $this->writeSteamIdStorage($steamIdStorage);
            }
        }
        return $steamIdStorage ? $steamIdStorage : null;
    }

    /**
     * @param $steamId
     * @return object|null
     */
    public function createSteamIdStorage($steamId) {
        $em = $this->getEntityManager();

        // Map the entity properties.
        $steamIdStorage = new SteamIdStorage();
        $steamIdStorage->setCustomUrl($steamId->getCustomUrl());
        $steamIdStorage->setFetchTime($steamId->getFetchTime());
        $steamIdStorage->setLimited($steamId->isLimited());
        $steamIdStorage->setNickname($steamId->getNickname());
        $steamIdStorage->setSteamId64($steamId->getSteamId64());
        $steamIdStorage->setTradeBanState($steamId->getTradeBanState());

        $this->addGames($steamIdStorage, $steamId->getGames());

        $em->persist($steamIdStorage);
        return $steamIdStorage;
    }

    /**
     * @param $steamIdStorage
     * @return object|null
     */
    public function writeSteamIdStorage($steamIdStorage) {
        // Cache the steamId in the database.
        $em = $this->getEntityManager();
        $em->flush();
        return $steamIdStorage;
    }

    /**
     * @param $steamIdStorage
     * @param $games
     * @return object|null
     */
    public function addGames($steamIdStorage, $games) {
        $em = $this->getEntityManager();

        // Map games.
        $gameEm = $em->getRepository('SteamBoatBundle:SteamGameStorage');
        if ($games) {
            foreach ($games as $game) {
                // Check for existing game.
                if ($existingGame = $gameEm->findOneByAppId($game->getAppId())) {
                    $steamIdStorage->addGame($existingGame);
                }
                else {
                    $steamIdStorage->addGame($gameEm->createSteamGameStorage($game));
                }
            }
        }
        return $steamIdStorage;
    }
}
