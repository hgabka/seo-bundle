<?php

namespace Hgabka\SeoBundle\Tests\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Hgabka\SeoBundle\Controller\RobotsController;
use Hgabka\SeoBundle\Entity\Robots;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

class RobotsControllerTest extends TestCase
{
    private function controller(): RobotsController
    {
        $controller = new RobotsController();
        $controller->setContainer(new ServiceLocator([
            'parameter_bag' => static fn (): ParameterBag => new ParameterBag(['robots_default' => "User-agent: *\nDisallow:"]),
        ]));

        return $controller;
    }

    private function doctrine(?Robots $robots): ManagerRegistry
    {
        $repository = $this->createStub(ObjectRepository::class);
        $repository->method('findOneBy')->willReturn($robots);
        $doctrine = $this->createStub(ManagerRegistry::class);
        $doctrine->method('getRepository')->willReturn($repository);

        return $doctrine;
    }

    public function testStoredRobotsTxtWins(): void
    {
        $response = $this->controller()->indexAction(Request::create('/robots.txt'), $this->doctrine((new Robots())->setRobotsTxt("User-agent: Googlebot\nDisallow: /private")));

        $this->assertSame("User-agent: Googlebot\nDisallow: /private", $response->getContent());
        $this->assertSame('text/plain', $response->headers->get('Content-type'));
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDefaultParameterIsUsedWhenNothingIsStored(): void
    {
        $response = $this->controller()->indexAction(Request::create('/robots.txt'), $this->doctrine(null));

        $this->assertSame("User-agent: *\nDisallow:", $response->getContent());
    }

    public function testEmptyStoredValueFallsBackToDefault(): void
    {
        $response = $this->controller()->indexAction(Request::create('/robots.txt'), $this->doctrine((new Robots())->setRobotsTxt('')));

        $this->assertSame("User-agent: *\nDisallow:", $response->getContent());
    }
}
