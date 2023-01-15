<?php

namespace Weebel\Configuration;

use Weebel\Contracts\Configuration as ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected string $basePath;
    protected string $configPath;

    protected array $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->basePath = $config['basePath'] ?? pathinfo($_SERVER["SCRIPT_FILENAME"], PATHINFO_DIRNAME);
        $this->configPath = removeDuplicateSlashes($config['configPath'] ?? $this->basePath . '/config');
        isset($config['env']) && $this->config['app']['env'] = $config['env'];
        isset($config['debug']) && $this->config['app']['debug'] = $config['debug'];

    }

    public function getEnv(): string
    {
        return $this->get('app.env');
    }

    public function get(string $string, mixed $default = null): mixed
    {
        $array = explode(".", $string);
        return $this->searchInArray($array, $this->config) ??
            $this->searchInConfigFile($array) ?? $default;
    }

    public function isDebug(): bool
    {
        return $this->get('app.debug');
    }

    /**
     * @return array|mixed|string|string[]
     */
    public function getBasePath(): mixed
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }


    private function searchInConfigFile(array $array)
    {
        $filePath = removeDuplicateSlashes($this->configPath . '/' . array_shift($array) . '.php');
        if (!file_exists($filePath)) {
            return null;
        }

        $configInFile = require $filePath;
        if (!is_array($configInFile)) {
            throw new ConfigurationException("Configuration defined in the file " . $configInFile . " is not array");
        }

        return $this->searchInArray($array, $configInFile);
    }

    private function searchInArray(array $array, array $config): mixed
    {
        foreach ($array as $item) {
            if (!array_key_exists($item, $config)) {
                return null;
            }
            $config = $config[$item];
        }

        return $config;
    }
}