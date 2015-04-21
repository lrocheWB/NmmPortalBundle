<?php

namespace CanalTP\NmmPortalBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use CanalTP\SamCoreBundle\DataFixtures\ORM\CustomerTrait;

class FixturesCustomer extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use CustomerTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $om)
    {
        $navitiaToken = $this->container->getParameter('nmm.navitia.token');
        $this->createCustomer($om, 'CanalTP', 'nmm-ihm@canaltp.fr', 'canaltp');
        $this->addPerimeterToCustomer($om, 'fr-bou', 'network:CGD', 'customer-canaltp');

        $this->addCustomerToApplication($om, 'app-samcore', 'customer-canaltp', $navitiaToken);

        $om->flush();
    }

    /**
    * {@inheritDoc}
    */
    public function getOrder()
    {
        return 2;
    }
}
