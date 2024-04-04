<?php

namespace WpPlus\ComposerAutoload;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\Event;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    const VERSION = '1.0.0';

    private ?Config $composerConfig = null;

    private ?array $pluginConfig = null;

    private array $pluginConfigDefaults = [
        'ABSPATH' => './',
        'filename' => '___composer_autoload',
    ];

    /**
     * @inheritDoc
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * @inheritDoc
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * When this composer plugin is uninstalled, remove the generated mu-plugin file, if exists.
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->setupConfigs($composer);

        $path = $this->buildAutoloadMuPluginFilePath();

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'post-autoload-dump' => 'onPostAutoloadDump',
        ];
    }

    /**
     * Create or update the mu-plugin file, which requires Composer's autoload.php.
     */
    public function onPostAutoloadDump(Event $event): void
    {
        $this->setupConfigs($event->getComposer());

        $fileContent = $this->buildAutoloadMuPluginFileContent();
        $filePath = $this->buildAutoloadMuPluginFilePath();

        if (!file_exists($filePath) || (file_get_contents($filePath) !== $fileContent)) {
            file_put_contents($filePath, $fileContent);
        }
    }

    /**
     * Set up the used configuration variables (Composer scope + plugin scope).
     */
    private function setupConfigs(Composer $composer): void
    {
        $this->composerConfig = $composer->getConfig();
        $this->pluginConfig = $this->getPluginConfig($composer);
    }

    /**
     * Get the (optional) configuration for this plugin, stored in the `extra` section of the (root) project's
     * composer.json.
     *
     * ```
     *  "extra": {
     *      "wp-plus": {
     *          "ABSPATH": "./",
     *          "WPMU_PLUGIN_DIR": "./wp-content/mu-plugins"
     *      },
     *      "wp-plus/composer-autoload": {
     *          "filename": "___composer_autoload"
     *      }
     *  }
     *
     * ```
     */
    private function getPluginConfig(Composer $composer): array
    {
        $extra = $composer->getPackage()->getExtra();
        return array_merge(
            $this->pluginConfigDefaults,
            $extra['wp-plus'] ?? [],
            $extra['wp-plus/composer-autoload'] ?? []
        );
    }

    /**
     * Build the path of the mu-plugin file, which requires Composer's autoload.php.
     */
    private function buildAutoloadMuPluginFilePath(): string
    {
        $wpConfig = Wp\Config::createFromPluginConfig($this->pluginConfig);
        $path = $this->isAbsolutePath($wpConfig->WPMU_PLUGIN_DIR)
            ? $wpConfig->WPMU_PLUGIN_DIR
            : realpath(dirname($this->composerConfig->getConfigSource()->getName()) . '/' . $wpConfig->WPMU_PLUGIN_DIR);
        $path .= '/' . $this->pluginConfig['filename'] . '.php';
        return $path;
    }

    /**
     * Build the content of the mu-plugin file.
     */
    private function buildAutoloadMuPluginFileContent(): string
    {
        $vendorDir = $this->composerConfig->get('vendor-dir');
        $version = static::VERSION;

        return include dirname(__DIR__) . '/template/mu-plugin.php';
    }

    /**
     * Check if a given path is an absolute path.
     * Works only with *nix-like paths (Windows paths are currently not supported).
     */
    private function isAbsolutePath($path): bool
    {
        // The classic absolute path that start with a slash (e.g. /var/www/html).
        if (str_starts_with($path, '/')) {
            return true;
        }

        // The relative to the user's home directory (e.g. ~/www/html).
        if (str_starts_with($path, '~/')) {
            return true;
        }

        return false;
    }
}
