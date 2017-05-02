# Robo integration

[Robo](http://robo.li/) commands and dependencies for Drupal 8 POC build.

## Usage

Make sure your `RoboFile` class extends `Europa\Robo\Tasks`:

```php
<?php

use Europa\Robo\Tasks;

/**
 * Class RoboFile.
 */
class RoboFile extends Tasks {

}
```

## Available commands

List available commands by running:

```
$ ./vendor/bin/robo
```

| Command | Description |
|---|---|
| `project:install` | Install project |
| `project:setup-behat` | Setup Behat |

## Configuration

Build commands can be configured by providing the following configuration parameters in a `robo.yml.dist` file: 

```yaml
# Site information.
site:
  name: Site name
  mail: info@example.org
  profile: poc_profile
  update: false
  locale: en

# Administrator account.
account:
  name: admin
  password: admin
  mail: admin@example.org

# Database parameters.
database:
  host: localhost
  port: 3306
  name: drupal
  user: root
  password: root
  prefix: ''

# Behat settings.
behat:
  # Behat configuration template.
  source: resources/behat.yml.dist
  # Resulting Behat configuration file after performing token replacement.
  destination: behat.yml.dist
  # Following tokens will be automatically replaced when running "project:setup-behat".
  tokens:
    !base_url: http://localhost
    !drupal_root: build

bin:
  drush: ./vendor/bin/drush
```

Configuration is processed by the [Robo Config](https://github.com/nuvoleweb/robo-config) project, check its `README.md`
for more information on how to properly override configuration parameters.
