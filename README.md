# Slack MUD for Drupal 8

This project integrates Drupal and Slack to play text games in Slack.

## Installation

To set up and use the project, download [Composer](https://getcomposer.org/). This project has been set up to work with [Docksal](https://docksal.io).

```
fin project start
fin composer install
fin init
```

# Kyrandia

To play the Kyrandia game, you'll need to enable the modules and import the Kyrandia game data.

```
fin drush en -y kyrandia_migrate
fin drush mim kyrandia_game
fin drush mim --all
```

There is currently an error in the migration that needs the game node to be explicitly imported first. The migration dependencies don't pick this up and it'll error if you don't do it this way.

Enabling kyrandia_migrate will enable all of the Kyrandia and SlackMUD modules needed to play the game.

Once the import is complete, you can run the tests.

```
cd tests
fin behat
```

To create a Slack application and connect it to this site, follow the instructions in the presentation at [https://mid.camp/6280](https://mid.camp/6280).
