<?php

namespace Zuni\MenuBundle\Loaders;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Loader de menu, carrega o menu do arquivo 
 *
 * @author Fábio Lemos Elizandro 
 */
class MenuLoaderYaml extends FileLoader
{

    /**
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        parent::__construct(new \Symfony\Component\Config\FileLocator());
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $menu = Yaml::parse($resource);
        
        $this->setCurrentDir(\pathinfo($resource, PATHINFO_DIRNAME));
        
        if (array_key_exists("imports", $menu)) {
            foreach ($menu['imports'] as $import) {
                $menuImported = $this->import($import['resource']);
                $menu['menu'] = \array_merge($menu['menu'], $menuImported['menu']);
            }
            
            unset($menu['imports']);
        }
        
        
        return $menu;
    }

    /**
     * Verifica se é um recurso e se é um yml
     * 
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        $rc = \trim($resource);

        return \is_string($rc) && \substr($rc, -3) === "yml";
    }


}
