<?php

namespace SkedApp\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SkedApp\CoreBundle\Form\InviteFriendsType;
use SkedApp\CoreBundle\Entity\InviteFriends;

/**
 * Invite friends controller
 *
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppCoreBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class InviteController extends Controller
{

    /**
     * Invite friend
     * @return type
     */
    public function inviteLoggedInAction()
    {
        $this->get('logger')->info('invite a friend');

        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_security_login'));
        }

        $form = $this->createForm(new InviteFriendsType());

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();

                $invite = new InviteFriends();
                $invite->setFriendName($data['friendName']);
                $invite->setEmail($data['email']);

                $usr = $this->get('security.context')->getToken()->getUser();

                if (get_class($usr) == "SkedApp\CoreBundle\Entity\Member") {
                    $invite->setMember($usr);
                } elseif (get_class($usr) == "SkedApp\CoreBundle\Entity\Customer") {
                    $invite->setCustomer($usr);
                } elseif (get_class($usr) == "SkedApp\CoreBundle\Entity\Consultant") {
                    $invite->setConsultant($usr);
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($invite);
                $em->flush();

                $arguments = array(
                    'friendName' => $data['friendName'],
                    'senderName' => $usr->getFullName(),
                    'link' => $this->generateUrl('_welcome', array(), true),
                    'email' => $data['email']
                );

                $this->get('notification.manager')->sendInviteFriendLoggedIn($arguments);
                 $this->getRequest()->getSession()->setFlash(
                    'success', 'You invite has been sent.');
                return $this->redirect($this->generateUrl('_welcome'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to send invite');
            }
        }

        return $this->render('SkedAppCoreBundle:Invite:invite.friend.loggedin.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * Invite friend to a consultant
     * @return type
     */
    public function inviteConsultantAction($slug)
    {
        $this->get('logger')->info('invite a friend to a consultant');

        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_security_login'));
        }

        $form = $this->createForm(new InviteFriendsType());


        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();

                try {

                    $consultant = $this->get('consultant.manager')->getBySlug($slug);

                    $invite = new InviteFriends();
                    $invite->setFriendName($data['friendName']);
                    $invite->setEmail($data['email']);

                    $usr = $this->get('security.context')->getToken()->getUser();

                    if (get_class($usr) == "SkedApp\CoreBundle\Entity\Member") {
                        $invite->setMember($usr);
                    } elseif (get_class($usr) == "SkedApp\CoreBundle\Entity\Customer") {
                        $invite->setCustomer($usr);
                    }

                    $invite->setConsultant($consultant);
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($invite);
                    $em->flush();

                    $arguments = array(
                        'friendName' => $data['friendName'],
                        'senderName' => $usr->getFullName(),
                        'link' => $this->generateUrl('sked_app_consultant_view_with_slug', array('slug'=>$slug), true).'.html',
                        'email' => $data['email'],
                        'consultantName' => $consultant->getFullName()
                    );

                    $this->get('notification.manager')->sendInviteFriendConsultant($arguments);
                    $this->getRequest()->getSession()->setFlash(
                    'success', 'You invite has been sent.');
                    return $this->redirect($this->generateUrl('sked_app_consultant_view_with_slug', array('slug'=>$slug)).'.html');
                    
                } catch (\Exception $e) {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed, invalid url:'.$e->getMessage());
                }
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to send invite');
            }
        }

        return $this->render('SkedAppCoreBundle:Invite:invite.friend.consultant.html.twig', array(
                'form' => $form->createView(),
                'slug' => $slug
            ));
    }

}

