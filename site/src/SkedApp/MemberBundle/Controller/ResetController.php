<?php

namespace SkedApp\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use SkedApp\MemberBundle\Form\ResetPasswordType;
use SkedApp\MemberBundle\Form\PasswordUpdateType;

/**
 * Reset password controller
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppMemberBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ResetController extends Controller
{

    /**
     * Reset password
     *
     * @return Response
     */
    public function resetPasswordAction()
    {
        $this->get('logger')->info('reset password');

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }

        $form = $this->createForm(new ResetPasswordType());
        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $email = $data['email'];
                $member = $this->container->get('member.manager')->getByEmail($email);
                
                //check consultant
                if(!$member){
                    $member = $this->container->get('consultant.manager')->getByEmail($email); 
                }
                

                if (!$member) {                     
                    //email not found in the system
                    $this->getRequest()
                        ->getSession()
                        ->setFlash('error', "We couldn't find an account associated with $email.");
                } else {

                    $token = $this->container->get('token.generator')->generateToken();
                    $member->setConfirmationToken($token);
                    $member->setPasswordRequestedAt(new \Datetime());
                    $em = $this->getDoctrine()->getEntityManager();

                    $em->persist($member);
                    $em->flush();

                    $params = array(
                        'name' => $member->getFirstName() . ' ' . $member->getLastName(),
                        'link' => $this->generateUrl(
                            'sked_app_member_reset_token', array('token' => $token), true)
                    );

                    $emailBodyHtml = $this->render(
                            'SkedAppCoreBundle:EmailTemplates:member.password.reset.html.twig', $params
                        )->getContent();

                    $emailBodyTxt = $this->render(
                            'SkedAppCoreBundle:EmailTemplates:member.password.reset.txt.twig', $params
                        )->getContent();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Reset Your SkedApp Password')
                        ->setFrom(array($this->container->getParameter ('mailer_from_mail') => $this->container->getParameter ('mailer_from_name')))
                        ->setTo(array($email => $member->getFirstName() . ' ' . $member->getLastName()))
                        ->setBody($emailBodyHtml, 'text/html')
                        ->addPart($emailBodyTxt, 'text/plain');

                    ;

                    $this->get('mailer')->send($message);
                    return $this->redirect($this->generateUrl('sked_app_member_reset_sent',array('email'=>$email)));

                }
            }
        }

        return $this->render('SkedAppMemberBundle:Reset:reset.password.html.twig', array(
                'form' => $form->createView()));
    }

    /**
     * Request password reset success
     * @return Response
     */
    public function resetPasswordSuccessAction($email = null)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }

        return $this->render('SkedAppMemberBundle:Reset:reset.password.request.html.twig');
    }

    /**
     * Check reset token
     * @param string $token
     * @throws AccessDeniedException
     */
    public function resetTokenAction($token)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }

        $member = $this->container->get('member.manager')->getByToken($token);
        
        if(!$member){
           $member = $this->container->get('consultant.manager')->getByToken($token); 
        }

        if ($member) {

            $form = $this->createForm(new PasswordUpdateType());
            $request = $this->getRequest();

            if ('POST' === $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $password = $data['password'];
                    $isValid = true;


                    if (strlen($password) <= 5) {
                        $isValid = false;
                        $this->getRequest()->getSession()->setFlash('error', 'Password must have at least 6 characters.');
                    } elseif (strlen($password) >= 16) {
                        $isValid = false;
                        $this->getRequest()->getSession()->setFlash('error', 'Password has a limit of 16 characters.');
                    }

                    if ($isValid) {
                        $member->setPassword($password);
                        $member->encodePassword();
                        $member->setConfirmationToken('');
                        $this->container->get('member.manager')->update($member);

                        $this->getRequest()->getSession()->setFlash('success', 'Password change was successfully.');
                        return $this->redirect($this->generateUrl('_security_login'));
                    }
                }
            }


            return $this->render('SkedAppMemberBundle:Reset:reset.password.change.html.twig', array(
                    'form' => $form->createView(),
                    'token' => $token));

        }

        return $this->render('SkedAppMemberBundle:Reset:reset.invalid.tokent.html.twig');
    }

}
