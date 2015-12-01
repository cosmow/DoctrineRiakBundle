<?php

/*
 * This file is part of the Doctrine RiakBundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CosmoW\Bundle\RiakBundle;

use Doctrine\Common\Util\ClassUtils;
use CosmoW\Bundle\RiakBundle\DependencyInjection\Compiler\CreateHydratorDirectoryPass;
use CosmoW\Bundle\RiakBundle\DependencyInjection\Compiler\CreateProxyDirectoryPass;
use CosmoW\Bundle\RiakBundle\DependencyInjection\DoctrineRiakExtension;
use Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\DoctrineValidationPass;
use Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterEventListenersAndSubscribersPass;
use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Doctrine Riak ODM bundle.
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Kris Wallsmith <kris@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class DoctrineRiakBundle extends Bundle
{
    private $autoloader;

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterEventListenersAndSubscribersPass('doctrine_riak.odm.connections', 'doctrine_riak.odm.%s_connection.event_manager', 'doctrine_riak.odm'), PassConfig::TYPE_BEFORE_OPTIMIZATION);
        $container->addCompilerPass(new CreateProxyDirectoryPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new CreateHydratorDirectoryPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new DoctrineValidationPass('riak'));

        if ($container->hasExtension('security')) {
            $container->getExtension('security')->addUserProviderFactory(new EntityFactory('riak', 'doctrine_riak.odm.security.user.provider'));
        }
    }

    public function getContainerExtension()
    {
        return new DoctrineRiakExtension();
    }

    public function boot()
    {
        // Register an autoloader for proxies to avoid issues when unserializing them
        // when the ODM is used.
        if ($this->container->hasParameter('doctrine_riak.odm.proxy_namespace')) {
            $namespace = $this->container->getParameter('doctrine_riak.odm.proxy_namespace');
            $dir = $this->container->getParameter('doctrine_riak.odm.proxy_dir');
            // See https://github.com/symfony/symfony/pull/3419 for usage of
            // references
            $container =& $this->container;

            $this->autoloader = function($class) use ($namespace, $dir, &$container) {
                if (0 === strpos($class, $namespace)) {
                    $fileName = str_replace('\\', '', substr($class, strlen($namespace) +1));
                    $file = $dir.DIRECTORY_SEPARATOR.$fileName.'.php';

                    if (!is_file($file) && $container->getParameter('doctrine_riak.odm.auto_generate_proxy_classes')) {
                        $originalClassName = ClassUtils::getRealClass($class);
                        $registry = $container->get('doctrine_riak');

                        // Tries to auto-generate the proxy file
                        foreach ($registry->getManagers() as $dm) {

                            if ($dm->getConfiguration()->getAutoGenerateProxyClasses()) {
                                $classes = $dm->getMetadataFactory()->getAllMetadata();

                                foreach ($classes as $classMetadata) {
                                    if ($classMetadata->name == $originalClassName) {
                                        $dm->getProxyFactory()->generateProxyClasses(array($classMetadata));
                                    }
                                }
                            }
                        }

                        clearstatcache($file);
                    }

                    if (is_file($file)) {
                        require $file;
                    }
                }
            };
            spl_autoload_register($this->autoloader);
        }
    }

    public function shutdown()
    {
        if (null !== $this->autoloader) {
            spl_autoload_unregister($this->autoloader);
            $this->autoloader = null;
        }
    }
}
