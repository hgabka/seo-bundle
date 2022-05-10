<?php

namespace Hgabka\SeoBundle\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Hgabka\SeoBundle\Entity\Robots;
use Hgabka\SeoBundle\Form\RobotsType;
use Hgabka\UtilsBundle\FlashMessages\FlashTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route(
        '/',
        name: 'HgabkaSeoBundle_settings_robots'
    )]
    public function robotsSettingsAction(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository(Robots::class);
        $robot = $repo->findOneBy([]);
        $default = $this->getParameter('robots_default');
        $isSaved = true;

        if (!$robot) {
            $robot = new Robots();
        }

        if (null === $robot->getRobotsTxt()) {
            $robot->setRobotsTxt($default);
            $isSaved = false;
        }

        $form = $this->createForm(RobotsType::class, $robot, [
            'action' => $this->generateUrl('HgabkaSeoBundle_settings_robots'),
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($robot);
                $em->flush();

                return new RedirectResponse($this->generateUrl('HgabkaSeoBundle_settings_robots'));
            }
        }

        if (!$isSaved) {
            $this->addFlash(
                FlashTypes::WARNING,
                $this->get('translator')->trans('seo.robots.warning')
            );
        }

        return $this->render('@HgabkaSeo/Admin/Settings/robotsSettings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
