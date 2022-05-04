<?php

namespace Hgabka\SeoBundle\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Hgabka\SeoBundle\Entity\Robots;
use Hgabka\SeoBundle\Form\RobotsType;
use Hgabka\UtilsBundle\FlashMessages\FlashTypes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsController extends AbstractController
{
    /**
     * Generates the robots administration form and fills it with a default value if needed.
     *
     * @Route(path="/", name="KunstmaanSeoBundle_settings_robots")
     * @Template(template="@KunstmaanSeo/Admin/Settings/robotsSettings.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function robotsSettingsAction(Request $request, ManagerRegistry $doctrine, TranslatorInterface $translator)
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
                $translator->trans('seo.robots.warning')
            );
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
