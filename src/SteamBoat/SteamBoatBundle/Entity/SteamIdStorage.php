<?php

namespace SteamBoat\SteamBoatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SteamGameStorage
 */
class SteamIdStorage
{
    /**
     * @var array
     */
    private static $steamIds = array();

    /**
     * @var string
     */
    private $customUrl;

    /**
     * @var integer
     */
    private $fetchTime;

    /**
     * @var array
     */
    private $friends;

    /**
     * @var ArrayCollection
     */
    private $games;

    /**
     * @var boolean
     */
    private $limited;

    /**
     * @var string
     */
    private $nickname;

    /**
     * @var array
     */
    private $playtimes;

    /**
     * @var string
     */
    private $steamId64;

    /**
     * @var string
     */
    private $tradeBanState;
    /**
     * @var integer
     */
    private $id;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->games = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customUrl
     *
     * @param string $customUrl
     * @return SteamIdStorage
     */
    public function setCustomUrl($customUrl)
    {
        $this->customUrl = $customUrl;

        return $this;
    }

    /**
     * Get customUrl
     *
     * @return string
     */
    public function getCustomUrl()
    {
        return $this->customUrl;
    }

    /**
     * Set fetchTime
     *
     * @param integer $fetchTime
     * @return SteamIdStorage
     */
    public function setFetchTime($fetchTime)
    {
        $this->fetchTime = $fetchTime;

        return $this;
    }

    /**
     * Get fetchTime
     *
     * @return integer
     */
    public function getFetchTime()
    {
        return $this->fetchTime;
    }

    /**
     * Set limited
     *
     * @param boolean $limited
     * @return SteamIdStorage
     */
    public function setLimited($limited)
    {
        $this->limited = $limited;

        return $this;
    }

    /**
     * Get limited
     *
     * @return boolean
     */
    public function getLimited()
    {
        return $this->limited;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return SteamIdStorage
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set steamId64
     *
     * @param string $steamId64
     * @return SteamIdStorage
     */
    public function setSteamId64($steamId64)
    {
        $this->steamId64 = $steamId64;

        return $this;
    }

    /**
     * Get steamId64
     *
     * @return string
     */
    public function getSteamId64()
    {
        return $this->steamId64;
    }

    /**
     * Set tradeBanState
     *
     * @param string $tradeBanState
     * @return SteamIdStorage
     */
    public function setTradeBanState($tradeBanState)
    {
        $this->tradeBanState = $tradeBanState;

        return $this;
    }

    /**
     * Get tradeBanState
     *
     * @return string
     */
    public function getTradeBanState()
    {
        return $this->tradeBanState;
    }
    /**
     * @var \SteamBoat\SteamBoatBundle\Entity\SteamGameStorage
     */
    private $category;


    /**
     * Set category
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamGameStorage $category
     * @return SteamIdStorage
     */
    public function setCategory(\SteamBoat\SteamBoatBundle\Entity\SteamGameStorage $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \SteamBoat\SteamBoatBundle\Entity\SteamGameStorage
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add game
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamGameStorage $game
     * @return SteamIdStorage
     */
    public function addGame(\SteamBoat\SteamBoatBundle\Entity\SteamGameStorage $game)
    {
        $this->games[] = $game;

        return $this;
    }

    /**
     * Remove game
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamGameStorage $game
     */
    public function removeGame(\SteamBoat\SteamBoatBundle\Entity\SteamGameStorage $game)
    {
        $this->games->removeElement($game);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Add friends
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $friends
     * @return SteamIdStorage
     */
    public function addFriend(\SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $friends)
    {
        $this->friends[] = $friends;

        return $this;
    }

    /**
     * Remove friends
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $friends
     */
    public function removeFriend(\SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $friends)
    {
        $this->friends->removeElement($friends);
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriends()
    {
        return $this->friends;
    }
}
