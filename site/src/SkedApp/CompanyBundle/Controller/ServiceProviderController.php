<?php

namespace SkedApp\CompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\Company;
use SkedApp\CoreBundle\Entity\CompanyPhotos;
use SkedApp\CompanyBundle\Form\CompanyCreateType;
use SkedApp\CompanyBundle\Form\CompanyUpdateType;
use SkedApp\CompanyBundle\Form\CompanyPhotosCreateType;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Events\MouseEvent;

/**
 * Service provider controller
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ServiceProviderController extends Controller
{

    /**
     * List all available agencies
     * 
     * @param Integer $page
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listAction($page = 1)
    {
        $this->get('logger')->info('list all service providers');

        $isDirectionSet = $this->get('request')->query->get('direction', false);
        $searchText = $this->get('request')->query->get('searchText');
        $sort = $this->get('request')->query->get('sort', 'c.id');
        $direction = $this->get('request')->query->get('direction', 'asc');

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction,
        );

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('company.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppCompanyBundle:ServiceProvider:list.html.twig', array(
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet
            ));
    }

    /**
     * Create a new service provider
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new service provider');
        $form = $this->createForm(new CompanyCreateType(), new Company());
        return $this->render('SkedAppCompanyBundle:ServiceProvider:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new service provider
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createAction()
    {

        $this->get('logger')->info('create a new service provider');

        $company = new Company();
        $form = $this->createForm(new CompanyCreateType(), $company);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $this->get('company.manager')->create($company);

                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created service provider successfully');
                return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create service provider');
            }
        }

        return $this->render('SkedAppCompanyBundle:ServiceProvider:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Show service provider
     * 
     * @param Integer $id Service provider id
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function showAction($id)
    {

        $this->get('logger')->info('view service provider');

        try {

            $company = $this->get('company.manager')->getById($id);

            $infoWindow = $this->get('ivory_google_map.info_window');

            // Configure your info window options
            $infoWindow->setPrefixJavascriptVariable('info_window_');
            $infoWindow->setPosition(0, 0, true);
            $infoWindow->setPixelOffset(1.1, 2.1, 'px', 'pt');
            $infoWindow->setContent('<p><strong>' . $company->getName() . '</strong> <br /> Tel:' . $company->getContactNumber() . '</p>');
            $infoWindow->setOpen(false);
            $infoWindow->setAutoOpen(true);
            $infoWindow->setOpenEvent(MouseEvent::CLICK);
            $infoWindow->setAutoClose(false);
            $infoWindow->setOption('disableAutoPan', true);
            $infoWindow->setOption('zIndex', 10);
            $infoWindow->setOptions(array(
                'disableAutoPan' => true,
                'zIndex' => 10
            ));



            $marker = $this->get('ivory_google_map.marker');


            // Configure your marker options
            $marker->setPrefixJavascriptVariable('marker_');
            $marker->setPosition($company->getLat(), $company->getLng(), true);
            $marker->setAnimation(Animation::DROP);
            $marker->setOptions(array(
                'clickable' => true,
                'flat' => true
            ));

            $marker->setIcon($this->container->getParameter('site_url') . 'img/assets/icons/skedapp-map-icon.png');
            $marker->setShadow($this->container->getParameter('site_url') . 'img/assets/icons/skedapp-map-icon.png');

            $map = $this->get('ivory_google_map.map');
            // Configure your map options
            $map->setPrefixJavascriptVariable('map_');
            $map->setHtmlContainerId('map_canvas');

            $map->setAsync(false);

            $map->setAutoZoom(false);

            $map->setCenter($company->getLat(), $company->getLng(), true);
            $map->setMapOption('zoom', 16);

            $map->setBound(0, 0, 0, 0, false, false);

            // Sets your map type
            $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
            $map->setMapOption('mapTypeId', 'roadmap');

            $map->setMapOption('disableDefaultUI', false);
            $map->setMapOption('disableDoubleClickZoom', false);
            $map->setStylesheetOptions(array(
                'width' => '100%',
                'height' => '300px'
            ));

            $map->setLanguage('en');


            $map->addMarker($marker);
            $marker->setInfoWindow($infoWindow);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
        }

        return $this->render('SkedAppCompanyBundle:ServiceProvider:show.html.twig', array(
                'company' => $company,
                'map' => $map
            ));
    }

    /**
     * Edit service provider
     * 
     * @param Integer $id Service provider id
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit service provider id:' . $id);

        try {
            $company = $this->get('company.manager')->getById($id);
            $form = $this->createForm(new CompanyUpdateType(), $company);            
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
        }

        return $this->render('SkedAppCompanyBundle:ServiceProvider:edit.html.twig', array(
                'form' => $form->createView(),
                'company' => $company,
            ));
    }

    /**
     * Update service provider
     * 
     * @param Integer $id Service provider id
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update service provider id:' . $id);

        try {
            $company = $this->get('company.manager')->getById($id);

            $form = $this->createForm(new CompanyUpdateType(), $company);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($company);
                    $em->flush();
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Updated service provider successfully');
                    return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed to update service provider');
                }
            }
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
        }

        return $this->render('SkedAppCompanyBundle:ServiceProvider:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $company->getId()
            ));
    }

    /**
     * Delete service provider
     * 
     * @param Integer $id Service provider id
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete service provider id:' . $id);

        try {
            $company = $this->get('company.manager')->getById($id);
            $this->container->get('company.manager')->delete($company);
            $this->getRequest()->getSession()->setFlash(
                'success', 'Deleted service provider successfully');
            return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_provider_list'));
        }
    }

}
