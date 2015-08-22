<?php

namespace SteamBoat\SteamBoatBundle\Service;


use Doctrine\Bundle\DoctrineBundle\Registry;
use SteamCondenser\Community\SteamId as SteamIdFetcher;
use SteamCondenser\Community\WebApi;
use SteamCondenser\Exceptions\SteamCondenserException;
use Symfony\Component\DependencyInjection\ContainerAware;

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
     * @return object|null
     */
    public function findOneByNickname($nickname) {
        $steamIdStorage = null;
        $repository = $this->doctrine
          ->getRepository('SteamBoatBundle:SteamIdStorage');

        // Check local cache.
        if ($steamIdStorage = $repository->findOneByNickname($nickname)) {
            return $steamIdStorage;
        }
        // Fetch remotely.
        elseif ($steamId = SteamIdFetcher::create($nickname)) {
            if ($steamIdStorage = $repository->createSteamIdStorage($steamId, TRUE)) {
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
     * @return object|null
     */
    public function findOneBySteamId64($steamId64) {
        $steamIdStorage = null;
        $repository = $this->doctrine
            ->getRepository('SteamBoatBundle:SteamIdStorage');

        // Check local cache.
        if ($steamIdStorage = $repository->findOneBySteamId64($steamId64)) {
            return $steamIdStorage;
        }
        // Fetch remotely.
        try {
            $steamId = SteamIdFetcher::create($steamId64);
        }
        catch (SteamCondenserException $e) {
            return null;
        }
        if ($steamIdStorage = $repository->createSteamIdStorage($steamId)) {
            // Cache in the database.
            $repository->writeSteamIdStorage($steamIdStorage);
        }
        return $steamIdStorage ?: null;
    }

}
