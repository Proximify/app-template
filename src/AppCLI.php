<?php

namespace Proximify;

class AppCLI extends CLIActions
{
    private $handle;

    /**
     * @inheritDoc
     * 
     * @see https://github.com/composer/composer/blob/master/src/Composer/Config.php
     */
    public function runComposerAction(array $options, array $env): void
    {
        // $this->startServer();

        // postPackageInstall(PackageEvent $event)
        // $installedPackage = $event->getOperation()->getPackage();

        if ($env['type'] == 'package') {
            echo "\nOp:\n:";
            // print_r($env['event']->getOperation());
            // print_r($env['event']->getOperations());
        }

        //$env = $options[self::ENV_OPTIONS];
        // $event = $env['event'] ?? false;
        $env['event'] = isset($env['event']) ? 'EVENT' : 'NO EVENT';

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
                    // $own[] = [
                    //     'name' => substr($name, strpos($name, '/') + 1),
                    //     'type' => $pkg->getType(),
                    //     'version' => $pkg->getVersion()
                    // ];
                }
            }
        }

        foreach ($own as $key => $pkg) {
            echo "\n > ", print_r($pkg, true);
        }

        if ($env['name'] == 'post-update-cmd') {
            $this->stopServer();
        }
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        // https://github.com/symfony/polyfill/blob/master/src/Php80/Php80.php
        return 0 === strncmp($haystack, $needle, strlen($needle));
    }

    function startServer($port = '8080', $target = 'public')
    {
        if (!$this->handle) {
            $domain = "localhost:$port";
            $this->handle = proc_open("php -S $domain -t '$target'", [], $pipes);
        }

        return $this->handle;
    }

    function stopServer()
    {
        if ($this->handle) {
            echo 'CLOSING';
            proc_close($this->handle);
            $this->handle = null;
        }
    }
}
