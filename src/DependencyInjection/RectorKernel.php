<?php declare(strict_types=1);

namespace Rector\DependencyInjection;

use Rector\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Rector\DependencyInjection\CompilerPass\AutowireDefaultCompilerPass;
use Rector\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Rector\NodeTypeResolver\DependencyInjection\CompilerPass\NodeTypeResolverCollectorCompilerPass;
use Rector\YamlRector\DependencyInjection\YamlRectorCollectorCompilerPass;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\BetterPhpDocParser\DependencyInjection\CompilerPass\CollectDecoratorsToPhpDocInfoFactoryCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;

final class RectorKernel extends Kernel
{
    /**
     * @var string
     */
    private $configFile;

    public function __construct(?string $configFile = '')
    {
        if ($configFile) {
            $this->configFile = $configFile;
        }

        // debug: true is require to invalidate container on service files change
        parent::__construct('cli' . sha1((string) $configFile), true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');

        if ($this->configFile) {
            $loader->load($this->configFile);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_rector_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_rector_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
        $containerBuilder->addCompilerPass(new YamlRectorCollectorCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireDefaultCompilerPass());
        $containerBuilder->addCompilerPass(new NodeTypeResolverCollectorCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());

        // for symplify/better-php-doc-parser
        $containerBuilder->addCompilerPass(new CollectDecoratorsToPhpDocInfoFactoryCompilerPass());

        // for tests
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());

        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
    }

    /**
     * This allows to use "%vendor%" variables in imports
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        return (new DelegatingLoaderFactory())->createFromContainerBuilderAndKernel($container, $this);
    }
}
