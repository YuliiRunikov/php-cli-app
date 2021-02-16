<?php

namespace Fixer;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Bootstrap {

  const CONFIG_FILE = 'config.yml';

  private function getAppBaseDir() {
    return realpath( __DIR__ . '/../');
  }

  private function generateAutoloadPath() {
    return $this->getAppBaseDir() . '/vendor/autoload.php';
  }

  private function checkFile($fileName) {
    return file_exists($fileName) && is_readable($fileName) && is_file($fileName);
  }

  private function initAutoloader() {
    $autoloadFile = $this->generateAutoloadPath();

    if ($this->checkFile($autoloadFile)) {
      require_once $autoloadFile;
    } else {
      throw new \Exception ('Install dependencies!');
    }
  }

  public function __construct() {
    $this->initAutoloader();
  }

  public function run() {
    $container = new ContainerBuilder();
    $locator =  new FileLocator($this->getAppBaseDir());

    (new YamlFileLoader($container, $locator))->load(self::CONFIG_FILE);

    $container->get('cli-app')->run();
  }
}
