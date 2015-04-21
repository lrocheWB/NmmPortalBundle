<?php

namespace CanalTP\NmmPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Perimeter
 */
class Perimeter
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $externalCoverageId;

    /**
     * @var string
     */
    private $externalNetworkId;

    private $navitiaEntity;

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
     * Set externalCoverageId
     *
     * @param string $externalCoverageId
     * @return Perimeter
     */
    public function setExternalCoverageId($externalCoverageId)
    {
        $this->externalCoverageId = $externalCoverageId;

        return $this;
    }

    /**
     * Get externalCoverageId
     *
     * @return string
     */
    public function getExternalCoverageId()
    {
        return $this->externalCoverageId;
    }

    /**
     * Set externalNetworkId
     *
     * @param string $externalNetworkId
     * @return Perimeter
     */
    public function setExternalNetworkId($externalNetworkId)
    {
        $this->externalNetworkId = $externalNetworkId;

        return $this;
    }

    /**
     * Get externalNetworkId
     *
     * @return string
     */
    public function getExternalNetworkId()
    {
        return $this->externalNetworkId;
    }

    /**
     * Set customer
     *
     * @param string $customer
     * @return Perimeter
     */
    public function setNavitiaEntity($navitiaEntity)
    {
        $this->navitiaEntity = $navitiaEntity;

        return $this;
    }

    /**
     * Get customer
     *
     * @return string
     */
    public function getNavitiaEntity()
    {
        return $this->navitiaEntity;
    }

    public function __toString()
    {
        return ($this->getId() . ' ' . $this->getExternalCoverageId() . ' ' . $this->getExternalNetworkId());
    }
}
