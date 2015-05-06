<?php

namespace CanalTP\NmmPortalBundle\Tests\Units;

use Symfony\Bundle\FrameworkBundle\Tests\Translation\TranslatorTest;
use Symfony\Component\Translation\MessageSelector;

class CustomerTranslatorTest extends TranslatorTest
{
    private $securityContext;

    protected function setUp()
    {
        parent::setUp();

        $this->mockSecurityContext();
    }

    private function mockSecurityContext()
    {
        $tokenStub = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenStub
            ->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue('anon.'));

        $this->securityContext = $this
            ->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->securityContext
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($tokenStub));
    }

    protected function getContainer($loader)
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function($arg) use ($loader) {
                if ($arg == 'security.context') {
                    return ($this->securityContext);
                }
                return ($loader);
            }))
        ;

        return $container;
    }

    public function getTranslator($loader, $options = array(), $translatorClass = '\CanalTP\NmmPortalBundle\Services\CustomerTranslator')
    {
        $translator = new $translatorClass(
            $this->getContainer($loader),
            new MessageSelector(),
            array('loader' => array('loader')),
            $options
        );

        $translator->addResource('loader', 'foo', 'fr');
        $translator->addResource('loader', 'foo', 'en');
        $translator->addResource('loader', 'foo', 'es');
        $translator->addResource('loader', 'foo', 'pt-PT'); // European Portuguese
        $translator->addResource('loader', 'foo', 'pt_BR'); // Brazilian Portuguese
        $translator->addResource('loader', 'foo', 'fr.UTF-8');
        $translator->addResource('loader', 'foo', 'sr@latin'); // Latin Serbian

        return $translator;
    }
}
