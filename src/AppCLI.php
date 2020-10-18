<?php

namespace Proximify;

class AppCLI extends CLIActions
{
    /**
     * @inheritDoc
     * 
     * @see https://github.com/composer/composer/blob/master/src/Composer/Config.php
     */
    public function runComposerAction(array $options): void
    {
        // postPackageInstall(PackageEvent $event)
        // $installedPackage = $event->getOperation()->getPackage();

        $env = $options[self::ENV_OPTIONS];
        $event = $env['event'];
        $env['event'] = 'EVENT';

        print_r($env);

        $lockFilename = dirname(__DIR__) . '/composer.lock';

        $composerInfo = new \ComposerLockParser\ComposerInfo($lockFilename);
        $own = [];

        foreach ($composerInfo->getPackages() as $key => $pkg) {
            $name = $pkg->getName();

            if (self::startsWith($name, 'proximify/')) {
                // Get the first autoload PSR-4 key
                $ns = $pkg->getNamespace();

                if (self::startsWith($ns, 'Proximify')) {
                    $own[] = [
                        'name' => substr($name, strpos($name, '/') + 1),
                        'type' => $pkg->getType(),
                        'version' => $pkg->getVersion()
                    ];
                }
            }
        }

        foreach ($own as $key => $pkg) {
            echo "\n > ", print_r($pkg, true);
        }
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        // https://github.com/symfony/polyfill/blob/master/src/Php80/Php80.php
        return 0 === strncmp($haystack, $needle, strlen($needle));
    }
}
