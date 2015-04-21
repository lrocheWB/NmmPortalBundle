<?php

namespace CanalTP\NmmPortalBundle\Services;

use CanalTP\NmmPortalBundle\Entity\NavitiaEntity;
use Doctrine\Common\Persistence\ObjectManager;

class NavitiaEntityManager
{
    protected $om = null;
    protected $repository = null;
    protected $navitiaTokenManager = null;

    public function __construct(ObjectManager $om, $navitiaTokenManager)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository('CanalTPSamCoreBundle:Customer');
        $this->navitiaTokenManager = $navitiaTokenManager;
    }

    public function save(NavitiaEntity $navUser)
    {
        $this->navitiaTokenManager->initUser($navUser->getNameCanonical(), $navUser->getEmailCanonical());
        $this->navitiaTokenManager->initInstanceAndAuthorizations($navUser->getPerimeters());

        if ($navUser->getId() != null) {
            $perimeterRepo = $this->om->getRepository('CanalTPNmmPortalBundle:Perimeter');
            $perimeters = $perimeterRepo->findBy(array('navitiaEntity' => $navUser));
            $officialPerimeterIds = array();

            foreach ($navUser->getPerimeters() as $perimeter) {
                if ($perimeter->getId() != null) {
                    $officialPerimeterIds[] = $perimeter->getId();
                }
            }

            foreach ($perimeters as $perimeter) {
                if (!in_array($perimeter->getId(), $officialPerimeterIds)) {
                    $this->om->remove($perimeter);
                }
            }

            $navUser->refreshPerimeters();
        } else {
            $this->om->persist($navUser);

            $tokenEntity = new \CanalTP\NmmPortalBundle\Entity\NavitiaToken();
            $tokenEntity->setActive();
            $tokenEntity->setNavitiaEntity($navUser);
            $tokenEntity->setToken($this->navitiaTokenManager->generateToken('NMM navitia.io'));

            $this->om->persist($tokenEntity);
        }

        $this->om->flush($navUser);
    }
}
