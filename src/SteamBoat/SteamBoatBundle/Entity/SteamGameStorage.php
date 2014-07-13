<?php

namespace SteamBoat\SteamBoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SteamGameStorage
 */
class SteamGameStorage
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $appId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $logoHash;

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
     * Set appId
     *
     * @param integer $appId
     * @return SteamGameStorage
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Get appId
     *
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SteamGameStorage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @var string
     */
    private $shortName;


    /**
     * Set logoHash
     *
     * @param string $logoHash
     * @return SteamGameStorage
     */
    public function setLogoHash($logoHash)
    {
        $this->logoHash = $logoHash;

        return $this;
    }

    /**
     * Get logoHash
     *
     * @return string
     */
    public function getLogoHash()
    {
        return $this->logoHash;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     * @return SteamGameStorage
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $steamIdStorages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->steamIdStorages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add steamIdStorages
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIdStorages
     * @return SteamGameStorage
     */
    public function addSteamIdStorage(\SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIdStorages)
    {
        $this->steamIdStorages[] = $steamIdStorages;

        return $this;
    }

    /**
     * Remove steamIdStorages
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIdStorages
     */
    public function removeSteamIdStorage(\SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIdStorages)
    {
        $this->steamIdStorages->removeElement($steamIdStorages);
    }

    /**
     * Get steamIdStorages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSteamIdStorages()
    {
        return $this->steamIdStorages;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $steamIds;


    /**
     * Add steamIds
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIds
     * @return SteamGameStorage
     */
    public function addSteamId(\SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIds)
    {
        $this->steamIds[] = $steamIds;

        return $this;
    }

    /**
     * Remove steamIds
     *
     * @param \SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIds
     */
    public function removeSteamId(\SteamBoat\SteamBoatBundle\Entity\SteamIdStorage $steamIds)
    {
        $this->steamIds->removeElement($steamIds);
    }

    /**
     * Get steamIds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSteamIds()
    {
        return $this->steamIds;
    }
}
