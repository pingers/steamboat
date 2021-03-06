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
        if (($steamIdStorage = $repository->findOneByNickname($nickname)) ||
          ($steamIdStorage = $repository->findOneBySteamId64($nickname))) {
            // Add friends if we don't have them yet.
            $this->addFriends($steamIdStorage);

            return $steamIdStorage;
        }
        // Fetch remotely.
        elseif ($steamId = SteamIdFetcher::create($nickname)) {
            // Double check it doesn't exist locally because SteamIdFetcher will
            // also find based on customUrl as opposed to nickname.
            if ($steamIdStorage = $repository->findOneBySteamId64($steamId->getSteamId64())) {
                // Do nothing, we have it cached.
            }
            elseif ($steamIdStorage = $repository->createSteamIdStorage($this->createSteamIdData($steamId, $fetchFriends))) {
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
            // Add friends if we don't have them yet.
            $this->addFriends($steamIdStorage);

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
        $parameters = array_merge([(int) $steamId64], $friendsId64);
        $placeholders = implode(', ', array_fill(0, count($parameters), '?'));

        // Generate the query.
        $sql = <<<SQL
SELECT COUNT(idGames.game_id) AS count, idGames.game_id, games.name
FROM `SteamIdStorage` AS steamIds
LEFT JOIN `SteamIds_SteamGames` AS idGames ON steamIds.id = idGames.steamId_id
LEFT JOIN `SteamGameStorage` AS games ON idGames.game_id = games.id
WHERE steamIds.steamId64 IN (%parameters%)
GROUP BY game_id
HAVING count > 1
ORDER BY count DESC, games.name;
SQL;
        $sql = str_replace('%parameters%', $placeholders, $sql);
        $statement = $this->doctrine->getManager()->getConnection()->prepare($sql);
        foreach ($parameters as $key => $parameter) {
            $statement->bindValue($key + 1, $parameter);
        }
        $statement->execute();
        $results = $statement->fetchAll();

        return $results;
    }

    public function addFriends($steamIdStorage) {
        $repository = $this->doctrine
          ->getRepository('SteamBoatBundle:SteamIdStorage');

        if (!count($steamIdStorage->getFriends())) {
            $steamId = SteamIdFetcher::create(
              $steamIdStorage->getSteamId64(), TRUE, TRUE);
            $friends = $steamId->getFriends();
            foreach ($friends as $friend) {
                $exists = FALSE;
                foreach ($steamIdStorage->getFriends() as $existingFriend) {
                    if ($existingFriend->getSteamId64() == $friend->getSteamId64()) {
                        $exists = TRUE;
                    }
                }
                if (!$exists) {
                    if ($friendSteamId = $this->findOneBySteamId64($friend->getSteamId64(), FALSE)) {
                        $steamIdStorage->addFriend($friendSteamId);
                    }
                }
            }
            $repository->writeSteamIdStorage($steamIdStorage);
        }
    }

}
