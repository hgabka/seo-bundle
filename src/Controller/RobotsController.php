<?php

namespace Hgabka\SeoBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Hgabka\SeoBundle\Entity\Robots;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RobotsController extends AbstractController
{
    /**
     * Generates the robots.txt content when available in the database and falls back to normal robots.txt if exists.
     *
     * @Route(path="/robots.txt", name="HgabkaSeoBundle_robots", defaults={"_format": "txt"})
     *
     * @return array
     */
    public function indexAction(Request $request, ManagerRegistry $doctrine)
    {
        $entity = $doctrine->getRepository(Robots::class)->findOneBy([]);
        $robots = $this->getParameter('robots_default');

        if ($entity && $entity->getRobotsTxt()) {
            $robots = $entity->getRobotsTxt();
        } else {
            $file = $request->getBasePath() . 'robots.txt';
            if (file_exists($file)) {
                $robots = file_get_contents($file);
            }
        }

        return new Response($robots, Response::HTTP_OK, ['Content-type' => 'text/plain']);
    }
}
