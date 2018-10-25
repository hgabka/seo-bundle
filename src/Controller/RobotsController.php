<?php

namespace Hgabka\SeoBundle\Controller;

use Hgabka\SeoBundle\Entity\Robots;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RobotsController extends Controller
{
    /**
     * Generates the robots.txt content when available in the database and falls back to normal robots.txt if exists.
     *
     * @Route(path="/robots.txt", name="HgabkaSeoBundle_robots", defaults={"_format": "txt"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $entity = $this->getDoctrine()->getRepository(Robots::class)->findOneBy([]);
        $robots = $this->getParameter('robots_default');

        if ($entity && $entity->getRobotsTxt()) {
            $robots = $entity->getRobotsTxt();
        } else {
            $file = $request->getBasePath().'robots.txt';
            if (file_exists($file)) {
                $robots = file_get_contents($file);
            }
        }

        return new Response($robots, Response::HTTP_OK, ['Content-type' => 'text/plain']);
    }
}
