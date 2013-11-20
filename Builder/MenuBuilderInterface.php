<?php

namespace Zuni\MenuBundle\Builder;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Fábio Lemos Elizandro 
 */
interface MenuBuilderInterface
{
    /**
     * Método que fabrica o menu
     */
    public function createMenu(Request $request);
    
     /**
     * Nome do menu que ser'a fabricado 
     * 
     * @return string
     */
    public function getName();

    /**
     * Fabrica de menu
     * 
     * @return FactoryInterface
     */
    public function getFactory();

    /**
     * Recurso de onde foi retirado o menu
     * 
     * @return string
     */
    public function getResource();

    /**
     * opcoes de renderizacao do menu
     * 
     * @return array
     */
    public function getOptions();
}
