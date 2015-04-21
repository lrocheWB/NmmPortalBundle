<?php

namespace CanalTP\NmmPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * NavitiaEntity
 */
class NavitiaToken extends \CanalTP\SamCoreBundle\Entity\AbstractEntity
{
    protected $id;
    protected $token;
    protected $isActive;

    protected $navitiaEntity;

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setActive()
    {
        $this->isActive = true;

        return $this;
    }

    public function setNotActive()
    {
        $this->isActive = false;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNavitiaEntity()
    {
        return $this->navitiaEntity;
    }

    public function setNavitiaEntity($navE)
    {
        $this->navitiaEntity = $navE;

        return $this;
    }
}
