<?php

namespace Europa\Robo;

use Robo\Robo;
use Robo\Tasks as RoboTasks;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Robo\Config\YamlConfigLoader;
use Robo\Config\ConfigProcessor;

/**
 * Class Tasks.
 *
 * @package Europa\Robo\Task\Build
 */
class Tasks extends RoboTasks {

  use \Boedah\Robo\Task\Drush\loadTasks;

  /**
   * Add default options.
   *
   * @param \Symfony\Component\Console\Command\Command $command
   *   Command object.
   *
   * @hook option
   */
  public function defaultOptions(Command $command) {
    $command->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Configuration file to be used instead of default `robo.yml.dist`.', 'robo.yml');
    $command->addOption('override', 'o', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Configuration value(s) to be overridden. Format: "path.to.key:value"', []);
  }

  /**
   * Command initialization.
   *
   * @param \Symfony\Component\Console\Input\Input $input
   *   Input object.
   *
   * @hook pre-init
   */
  public function initializeConfiguration(Input $input) {
    $config = Robo::config();
    $loader = new YamlConfigLoader();
    $processor = new ConfigProcessor();
    $processor->extend($loader->load('robo.yml.dist'));
    $processor->extend($loader->load($input->getOption('config')));
    $config->import($processor->export());
    foreach ($input->getOption('override') as $override) {
      if (preg_match_all('/^([a-zA-z0-9\-\_\.]+):(.*)/', $override, $matches) !== FALSE) {
        $config->set($matches[1][0], $matches[2][0]);
      }
    }
  }

  /**
   * Setup Behat.
   *
   * @return \Robo\Collection\CollectionBuilder
   *   Collection builder.
   *
   * @command project:setup-behat
   * @aliases psb
   */
  public function projectSetupBehat() {
    $tokens = $this->config('behat.tokens');
    $from = array_map(function ($item) {
      return "!$item";
    }, array_keys($tokens));

    return $this->collectionBuilder()->addTaskList([
      $this->taskFilesystemStack()->copy($this->config('behat.source'), $this->config('behat.destination'), TRUE),
      $this->taskReplaceInFile($this->config('behat.destination'))->from($from)->to($tokens),
    ]);
  }

  /**
   * Install site.
   *
   * @return \Robo\Collection\CollectionBuilder
   *   Collection builder.
   *
   * @command project:install
   * @aliases pi
   */
  public function projectInstall() {
    return $this->taskDrushStack($this->config('bin.drush'))
      ->arg("--root={$this->root()}/build")
      ->siteName($this->config('site.name'))
      ->siteMail($this->config('site.mail'))
      ->locale($this->config('site.locale'))
      ->accountMail($this->config('account.mail'))
      ->accountName($this->config('account.name'))
      ->accountPass($this->config('account.password'))
      ->dbPrefix($this->config('database.prefix'))
      ->dbUrl(sprintf("mysql://%s:%s@%s:%s/%s",
        $this->config('database.user'),
        $this->config('database.password'),
        $this->config('database.host'),
        $this->config('database.port'),
        $this->config('database.name')))
      ->siteInstall($this->config('site.profile'))
      ->run();
  }

  /**
   * Fetch a configuration value.
   *
   * @param string $key
   *   Which config item to look up.
   *
   * @return mixed
   *   Configuration value.
   */
  protected function config($key) {
    return Robo::config()->get($key);
  }

  /**
   * Get root directory.
   *
   * @return string
   *   Root directory.
   */
  protected function root() {
    return getcwd();
  }

}
