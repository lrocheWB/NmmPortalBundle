<?php

namespace CanalTP\NmmPortalBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use CanalTP\NmmPortalBundle\Entity\CustomerApplication;
use CanalTP\NmmPortalBundle\Entity\Customer;
use CanalTP\SamCoreBundle\Entity\Application;
use CanalTP\NmmPortalBundle\Services\NavitiaTokenManager;

class ApplicationToCustomerApplicationTransformerWithToken implements DataTransformerInterface
{
    private $om;
    private $withTyr;
    private $lastCustomerApplications;

    public function __construct(ObjectManager $om, $tyrUrl)
    {
        $this->om = $om;
        $this->withTyr = ($tyrUrl != null);
        $this->lastCustomerApplications = array();
    }

    public function transform($customer)
    {
        if ($customer === null) {
            $customer = new Customer();
        }
        $applications = $this->om->getRepository('CanalTPSamCoreBundle:Application')->findAllOrderedByName();

        foreach ($customer->getApplications() as $customerApplication) {
            if (!$customerApplication->getIsActive()) {
                $customer->getApplications()->removeElement($customerApplication);
                continue;
            }
            $this->lastCustomerApplications[$customerApplication->getApplication()->getId()] = clone $customerApplication;
        }
        foreach ($applications as $application) {
            if (!array_key_exists($application->getId(), $this->lastCustomerApplications)) {
                $customerApplication = new CustomerApplication();
                $customerApplication->setIsActive(false);
                $customerApplication->setCustomer($customer);
                $customerApplication->setApplication($application);
                $customer->getApplications()->add($customerApplication);
            }
        }

        return $customer;
    }

    private function isEmpty(CustomerApplication $customerApplication)
    {
        return (($customerApplication->getIsActive() == false && $customerApplication->getId() == null) || $customerApplication->getToken() == null);
    }

    private function isNew(CustomerApplication $customerApplication)
    {
        return ($customerApplication->getIsActive() == true && $customerApplication->getToken() != null && $customerApplication->getId() == null && !array_key_exists($customerApplication->getApplication()->getId(), $this->lastCustomerApplications));
    }

    private function isDisabled(CustomerApplication $customerApplication)
    {
        return ($customerApplication->getIsActive() == false && $customerApplication->getId() != null);
    }

    private function isUpdated(Customer $customer, CustomerApplication $lastCustomerApplication, CustomerApplication $customerApplication)
    {
        $newCustomerApplication = null;
        $isUpdated = $lastCustomerApplication->getToken() != $customerApplication->getToken();

        if ($customerApplication->getIsActive() == true && $customerApplication->getId() != null && $customerApplication->getToken() != null && $isUpdated) {
            $newCustomerApplication = new CustomerApplication();

            $newCustomerApplication->setIsActive(true);
            $newCustomerApplication->setToken($customerApplication->getToken());
            $newCustomerApplication->setCustomer($customer);
            $newCustomerApplication->setApplication($lastCustomerApplication->getApplication());
            $customerApplication->setIsActive(false);
            $customerApplication->setToken($lastCustomerApplication->getToken());
        }

        return ($newCustomerApplication);
    }

    public function reverseTransform($customer)
    {
        if (!$customer) {
            return $customer;
        }

        foreach ($customer->getApplications() as $customerApplication) {
            if ($this->isNew($customerApplication)) {
                continue;
            }

            if ($this->isEmpty($customerApplication)) {
                $customer->getApplications()->removeElement($customerApplication);
                continue;
            }

            if ($this->isDisabled($customerApplication)) {
                $lastCustomerApplication = $this->lastCustomerApplications[$customerApplication->getApplication()->getId()];

                $customerApplication->setToken($lastCustomerApplication->getToken());
                $customer->getApplications()->removeElement($customerApplication);
                continue;
            }

            $newCustomerApplication = $this->isUpdated($customer, $this->lastCustomerApplications[$customerApplication->getApplication()->getId()], $customerApplication);
            if ($newCustomerApplication != null) {
                $customer->getApplications()->add($newCustomerApplication);
                continue;
            }
        }

        return $customer;
    }
}
