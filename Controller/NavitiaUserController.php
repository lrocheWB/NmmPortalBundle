<?php

namespace CanalTP\NmmPortalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use CanalTP\NmmPortalBundle\Entity\NavitiaEntity;
use CanalTP\SamCoreBundle\Entity\Application as ApplicationEntity;
use CanalTP\SamCoreBundle\Entity\Perimeter;
use CanalTP\SamCoreBundle\Form\Type\CustomerType;
use CanalTP\NmmPortalBundle\Form\Type\NavitiaEntityType;
use Doctrine\Common\Collections\Criteria;

/**
 * Description of CustomerController
 *
 * @author Kévin ZIEMIANSKI <kevin.ziemianski@canaltp.fr>
 */
class NavitiaUserController extends \CanalTP\SamCoreBundle\Controller\AbstractController
{
    public function listAction()
    {
//        $this->isGranted('BUSINESS_MANAGE_NAVITIA_USER');

        $users = $this->getDoctrine()
            ->getManager()
            ->getRepository('CanalTPNmmPortalBundle:NavitiaEntity')
            ->findNotCustomer();

        return $this->render(
            'CanalTPNmmPortalBundle:NavitiaUser:list.html.twig',
            array(
                'users' => $users,
            )
        );
    }

    public function editAction(Request $request, NavitiaEntity $navEntity = null)
    {
//        $this->isGranted(array('BUSINESS_MANAGE_NAVITIA_USER', 'BUSINESS_CREATE_NAVITIA_USER'));

        $coverage = $this->get('sam_navitia')->getCoverages();
        $form = $this->createForm(
            new NavitiaEntityType(
                $coverage->regions,
                $this->get('sam_navitia'),
                false
            ),
            $navEntity
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('sam_core.navitia_user')->save($form->getData());
            $this->addFlashMessage('success', 'customer.flash.edit.success');

            return $this->redirect($this->generateUrl('nmm_navitiaio_user'));
        }

        return $this->render(
            'CanalTPNmmPortalBundle:NavitiaUser:form.html.twig',
            array(
                'title' => 'customer.edit.title',
                'form' => $form->createView()
            )
        );
    }

    public function newAction(Request $request)
    {
//        $this->isGranted('BUSINESS_CREATE_NAVITIA_USER');

        $coverage = $this->get('sam_navitia')->getCoverages();
        $form = $this->createForm(
            new NavitiaEntityType(
                $coverage->regions,
                $this->get('sam_navitia'),
                false
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('sam_core.navitia_user')->save($form->getData());
            $this->addFlashMessage('success', 'customer.flash.creation.success');

            return $this->redirect($this->generateUrl('nmm_navitiaio_user'));
        }

        return $this->render(
            'CanalTPNmmPortalBundle:NavitiaUser:form.html.twig',
            array(
                'logoPath' => null,
                'title' => 'navitia_user.new.title',
                'form' => $form->createView()
            )
        );
    }

    public function listTokensAction(NavitiaEntity $navEntity)
    {
        $criteriaOrder = Criteria::create()
            ->orderBy(array('created' => Criteria::DESC));

        return $this->render(
            'CanalTPNmmPortalBundle:NavitiaUser:listToken.html.twig',
            array(
                'tokens' => $navEntity->getTokens()->matching($criteriaOrder),
                'perimeters' => $navEntity->getPerimeters(),
                'navEntityId' => $navEntity->getId()
            )
        );
    }

    public function regenerateTokenAction(NavitiaEntity $navEntity)
    {
        $ntm = $this->get('navitia_token_manager');
        $ntm->initUser($navEntity->getNameCanonical(), $navEntity->getEmailCanonical());
        $ntm->initInstanceAndAuthorizations($navEntity->getPerimeters());

        $om = $this->getDoctrine()->getManager();
        $tokenRepo = $om->getRepository('CanalTPNmmPortalBundle:NavitiaToken');
        $tokenRepo->disableToken($navEntity);

        $tokenEntity = new \CanalTP\NmmPortalBundle\Entity\NavitiaToken();
        $tokenEntity->setActive();
        $tokenEntity->setNavitiaEntity($navEntity);
        $tokenEntity->setToken($ntm->generateToken('NMM navitia.io'));

        $om->persist($navEntity);
        $om->persist($tokenEntity);
        $om->flush($tokenEntity);

        return $this->redirect($this->generateUrl('nmm_navitiaio_user_listtokens', array('id' => $navEntity->getId())));
    }
}
