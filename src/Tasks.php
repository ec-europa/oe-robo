<?php

namespace Europa\Robo;

use Robo\Tasks as RoboTasks;

/**
 * Class Tasks.
 *
 * @package Europa\Robo\Task\Build
 */
class Tasks extends RoboTasks {

  use \Boedah\Robo\Task\Drush\loadTasks;
  use \NuvoleWeb\Robo\Task\Config\loadTasks;

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
    return $this->collectionBuilder()->addTaskList([
      $this->taskFilesystemStack()->copy($this->config('behat.source'), $this->config('behat.destination'), TRUE),
      $this->taskReplaceInFile($this->config('behat.destination'))->from(array_keys($tokens))->to($tokens),
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
   * Get root directory.
   *
   * @return string
   *   Root directory.
   */
  protected function root() {
    return getcwd();
  }

}
