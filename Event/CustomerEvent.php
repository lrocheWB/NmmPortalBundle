<?php

namespace CanalTP\NmmPortalBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use CanalTP\SamCoreBundle\Entity\CustomerInterface;

class CustomerEvent extends Event
{
    private $customer;

    public function __construct(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return ($this->customer);
    }
}
