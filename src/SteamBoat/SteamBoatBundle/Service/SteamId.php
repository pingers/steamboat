<?php

namespace SteamBoat\SteamBoatBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use SteamCondenser\Community\SteamGame;
use SteamCondenser\Community\SteamId as SteamIdFetcher;
use SteamCondenser\Community\WebApi;
use SteamCondenser\Exceptions\SteamCondenserException;

/**
 * SteamId
 *
 * Wraps the Steam Condenser library with a persistent database cache layer.
 */
class SteamId
{

    private $doctrine;

    public function __construct($webapi_key, Registry $doctrine) {
        WebApi::setApiKey($webapi_key);
        $this->doctrine = $doctrine;
    }

    /**
     * Retrieve a SteamId by Nickname. Fetches friends.
     *
     * @param $nickname
     * @param $fetchFriends
     * @return object|null
     */
    public function findOneByNickname($nickname, $fetchFriends = FALSE) {
        $steamIdStorage = null;
        $repository = $this->doctrine
            ->getRepository('SteamBoatBundle:SteamIdStorage');

        // Check local cache.
        if ($steamIdStorage = $repository->findOneByNickname($nickname)) {
            // @TODO: Add friends if we don't have them yet.
            if (!count($steamIdStorage->getFriends())) {
                $steamId = SteamIdFetcher::create($steamIdStorage->getSteamId64(), true, true);
                $friends = $steamId->getFriends();
                foreach ($friends as $friend) {
                    $exists = false;
                    foreach ($steamIdStorage->getFriends() as $existingFriend) {
                        if ($existingFriend->getSteamId64() == $friend->getSteamId64()) {
                            $exists = true;
                        }
                    }
                    if (!$exists) {
                        if ($friendSteamId = $this->findOneBySteamId64($friend->getSteamId64(), false)) {
                            $steamIdStorage->addFriend($friendSteamId);
                        }
                    }
                }
                $repository->writeSteamIdStorage($steamIdStorage);
            }

            return $steamIdStorage;
        }
        // Fetch remotely.
        elseif ($steamId = SteamIdFetcher::create($nickname)) {
            if ($steamIdStorage = $repository->createSteamIdStorage($this->createSteamIdData($steamId, $fetchFriends))) {
                // Cache in the database.
                $repository->writeSteamIdStorage($steamIdStorage);
            }
        }
        return $steamIdStorage ?: null;
    }

    /**
     * Retrieve a SteamId by SteamId64. Does not fetch friends by default.
     *
     * @param $steamId64
     * @param $fetchFriends
     * @return object|null
     */
    public function findOneBySteamId64($steamId64, $fetchFriends = FALSE) {
        $steamIdStorage = null;
        $repository = $this->doctrine
            ->getRepository('SteamBoatBundle:SteamIdStorage');

        // Check local cache.
        if ($steamIdStorage = $repository->findOneBySteamId64($steamId64)) {
            return $steamIdStorage;
        }
        // Fetch remotely.
        try {
            $steamId = SteamIdFetcher::create($steamId64, TRUE, TRUE);
        }
        catch (SteamCondenserException $e) {
            return null;
        }
        $steamIdStorage = $repository->createSteamIdStorage($this->createSteamIdData($steamId, $fetchFriends));
        return $steamIdStorage ?: null;
    }

    function createSteamIdData($steamId, $fetchFriends = FALSE) {
        // Fetch and format game data.
        $steamGamesData = [];
        $friendsData = [];
        if ($steamId->isPublic()) {
            $games = $steamId->getGames();
            if (count($games)) {
                foreach ($games as $game) {
                    $steamGamesData[] = $this->createSteamGameData($game);
                }
            }
            if ($fetchFriends) {
                $friends = $steamId->getFriends();
                foreach ($friends as $friend) {
                    $friendsData[$friend->getSteamId64()] = $this->findOneBySteamId64($friend->getSteamId64(), false);
                }
            }
        }

        // Map the entity properties.
        $steamIdData = [
            'CustomUrl'     => $steamId->getCustomUrl() ? 'id/' . $steamId->getCustomUrl() : 'profiles/' . $steamId->getSteamId64(),
            'FetchTime'     => $steamId->getFetchTime(),
            'Limited'       => $steamId->isLimited(),
            'Nickname'      => $steamId->getNickname(),
            'SteamId64'     => $steamId->getSteamId64(),
            'TradeBanState' => $steamId->getTradeBanState(),
            'Games'         => $steamGamesData,
            'Friends'       => $friendsData,
        ];

        return $steamIdData;
    }

    /**
     * @param SteamGame $steamGame
     * @return array
     */
    public function createSteamGameData(SteamGame $steamGame) {
        $steamGameData = [
            'Name'    => $steamGame->getName(),
            'AppId'   => $steamGame->getAppId(),
            'LogoUrl' => $steamGame->getLogoUrl(),
        ];

        return $steamGameData;
    }

    public function findCommonGames($steamId64, $friendsId64) {
        // MYSQL?
        $repository = $this->doctrine
            ->getRepository('SteamBoatBundle:SteamIdStorage');

//SELECT COUNT(idGames.game_id) AS count, idGames.game_id, games.name
//FROM `SteamIdStorage` AS steamIds
//LEFT JOIN `SteamIds_SteamGames` AS idGames ON steamIds.id = idGames.steamId_id
//LEFT JOIN `SteamGameStorage` AS games ON idGames.game_id = games.id
//WHERE steamIds.steamId64 IN (76561197994407102, 76561197990231263)
//GROUP BY game_id
//HAVING count > 1
//ORDER BY count DESC, games.name

    }

}
