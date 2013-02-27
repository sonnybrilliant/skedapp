<?php

namespace SkedApp\CompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\CompanyPhotos;
use SkedApp\CompanyBundle\Form\CompanyPhotosCreateType;
use SkedApp\CompanyBundle\Form\CompanyPhotosUpdateType;

/**
 * Service provider photo controller
 *
 * @author Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ServiceProviderPhotoController extends Controller
{
    
    /**
     * Add service provider photo
     * 
     * @param integer $serviceProviderId
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function addPhotoAction($serviceProviderId)
    {
        $this->get('logger')->info('Add service provider photo');

        $companyPhoto = new CompanyPhotos();

        $form = $this->createForm(new CompanyPhotosCreateType(), $companyPhoto);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $this->get('company.photos.manager')->upload($companyPhoto, $serviceProviderId);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Uploaded service provider photo sucessfully');
                    return $this->redirect($this->generateUrl('sked_app_service_provider_show', array('id' => $serviceProviderId)));
                } catch (\Exception $e) {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Exception occured: '.$e->getMessage());
                    return $this->redirect($this->generateUrl('sked_app_service_provider_show', array('id' => $serviceProviderId)));
                }
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to upload service provider photo');
            }
        }

        return $this->render('SkedAppCompanyBundle:ServiceProviderPhotos:add.photo.html.twig', array(
                'form' => $form->createView(),
                'serviceProviderId' => $serviceProviderId
            ));
    }

    /**
     * Update company photo
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function photo_updateAction($company_id, $id)
    {
        $this->get('logger')->info('create or update service provider photo id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create or update service provider photo id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();

        if ($id > 0)
            $company_photo = $em->getRepository('SkedAppCoreBundle:CompanyPhotos')->find($id);
        else
            $company_photo = new CompanyPhotos ();

        if ((!$company_photo) && ($id > 0)) {
            $this->get('logger')->warn("service provider photo not found $id");
            return $this->createNotFoundException();
        }

        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($company_id);

        if (!$company) {
            $this->get('logger')->warn("Invalid service provider $company_id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new CompanyPhotosUpdateType(), $company_photo);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $company_photo->setCompany($company);
                $em->persist($company_photo);
                $em->flush();
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Uploaded service provider photo sucessfully');
                return $this->redirect($this->generateUrl('sked_app_company_edit', array('id' => $company_id)));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to upload service provider photo');
            }
        }

        return $this->editAction($company_id);
    }

    /**
     * Delete company photo
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function photo_deleteAction($company_id, $id)
    {
        $this->get('logger')->info('delete service provider photo id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('delete  service provider photo id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $company = $em->getRepository('SkedAppCoreBundle:CompanyPhotos')->find($id);

        if (!$company) {
            $this->get('logger')->warn("service provider photo not found $id");
            return $this->createNotFoundException();
        }

        $this->container->get('company_photos.manager')->delete($company);
        $this->getRequest()->getSession()->setFlash(
            'success', 'Deleted service provider photo sucessfully');
        return $this->redirect($this->generateUrl('sked_app_company_edit', array('id' => $company_id)));
    }

}
