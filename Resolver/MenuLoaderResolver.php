<?php

namespace Zuni\MenuBundle\Resolver;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Resolvedor de recursos de menu
 * Busca todos os services que possuim a tag 
 * de carregador de menu, e identifica qual o responsavel por
 * fazer a importação do menu
 * 
 * @author Fábio Lemos Elizandro 
 */
class MenuLoaderResolver implements MenuLoaderResolveInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var LoaderResolver
     */
    private $loaderResolver;
    
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->loaderResolver = new LoaderResolver();
    }
    
    /**
     * Resolve a importação de recursos 
     * 
     * @param string $resource
     * @param string $type
     * @return array Lista de menus
     */
    public function load($resource, $type = null)
    {
        $locator = new FileLocator();
        
        $this->filterResource($resource);
        
        return $this->createDelegatingLoader()->load($locator->locate($resource), $type);
    }
    
    /**
     * Adiciona loader
     * 
     * @param LoaderInterface $loader
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaderResolver->addLoader($loader);
    }
    
    /**
     * Cria um delegating loader com os loaders já adicionados
     * 
     * @return \Symfony\Component\Config\Loader\DelegatingLoader
     */
    public function createDelegatingLoader()
    {
        return new DelegatingLoader($this->loaderResolver);
    }
    
    /**
     * Filtra o resrouce caso. Caso ele seja um apelido de 
     * resource será convertido para um resource real 
     * 
     * @param string $resource passado por referencia
     */
    private function filterResource(&$resource)
    {
        $resource = $this->container->get("kernel")->locateResource($resource);
    }

}
