<?php

namespace Zuni\MenuBundle\Resolver;

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @author Fábio Lemos Elizandro 
 */
interface MenuLoaderResolveInterface
{
    /**
     * Retorna o loader responsavel por carregar o recurso 
     * 
     * @param string $resource 
     * @param string $type 
     * 
     * @return array
     */
    public function load($resource, $type = null);
    
    /**
     * Adiciona um loader 
     */
    public function addLoader(LoaderInterface $loader);
    
    /**
     * Cria um delegating loader com os loaders que o resolve possui
     * até o momento 
     * 
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createDelegatingLoader();
}
