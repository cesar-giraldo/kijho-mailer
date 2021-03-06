<?php

namespace Kijho\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\MailerBundle\Form\EmailSettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller {

    /**
     * Permite desplegar el formulario de edicion de configuraciones de emails
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 19/11/2015
     * @return Response formulario de edicion de las settings
     */
    public function editAction() {

        $settingsStorage = $this->container->getParameter('kijho_mailer.storage')['settings'];
        
        $settings = $this->get('email_manager')->getMailerSettings();

        $form = $this->createForm(new EmailSettingsType($this->container), $settings);

        $swiftMailerSettings = $this->get('email_manager')->getSwiftMailerSettings();
        
        return $this->render('KijhoMailerBundle:Settings:edit.html.twig', array(
                    'settings' => $settings,
                    'swiftMailerSettings' => $swiftMailerSettings,
                    'form' => $form->createView(),
                    'menu' => 'settings'
        ));
    }

    /**
     * Permite validar y almacenar los cambios en los settings
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 19/11/2015
     * @param Request $request datos de la solicitud
     * @return Response en caso de exito redirecciona a la homepage, 
     * en caso de error despliega nuevamente el formulario de settings
     */
    public function updateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $settingsStorage = $this->container->getParameter('kijho_mailer.storage')['settings'];

        $settings = $this->get('email_manager')->getMailerSettings();

        $form = $this->createForm(new EmailSettingsType($this->container), $settings);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($settings);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageHomeSuccess', $this->get('translator')->trans('kijho_mailer.setting.update_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_homepage'));
        }

        $swiftMailerSettings = $this->get('email_manager')->getSwiftMailerSettings();

        return $this->render('KijhoMailerBundle:Settings:edit.html.twig', array(
                    'settings' => $settings,
                    'swiftMailerSettings' => $swiftMailerSettings,
                    'form' => $form->createView(),
                    'menu' => 'settings'
        ));
    }
}
