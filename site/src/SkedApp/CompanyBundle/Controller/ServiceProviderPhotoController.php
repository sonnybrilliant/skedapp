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

}
