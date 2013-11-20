<?php

namespace Zuni\MenuBundle\Builder;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Fábio Lemos Elizandro 
 */
class MenuBuilder extends AbstractMenuBuilder
{
    /**
     * {@inheritDoc}
     */
    public function builder(array $menuArray, Request $request)
    {
        $menuForFactory = array("name" => $this->name, "children" => $menuArray['menu']);
        return $this->factory->createFromArray($menuForFactory);
    }

}
