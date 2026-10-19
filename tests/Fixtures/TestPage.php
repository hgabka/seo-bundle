<?php

namespace Hgabka\SeoBundle\Tests\Fixtures;

use Hgabka\NodeBundle\Entity\AbstractPage;
use Hgabka\NodeBundle\Helper\RenderContext;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

class TestPage extends AbstractPage
{
    public function __construct(?int $id = null, ?string $title = null)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getDefaultAdminType(): string
    {
        return '';
    }

    public function getPossibleChildTypes(): array
    {
        return [];
    }

    public function isStructureNode(): bool
    {
        return false;
    }

    public function service(ServiceLocator $container, Request $request, RenderContext $context)
    {
    }
}
