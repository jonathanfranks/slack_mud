<?php

namespace Drupal\slack_mud\EventSubscriber;

use Drupal\slack_mud\Event\CommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MudEventSubscriber.
 */
class MudEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommandEvent::COMMAND_EVENT] = [
      'onCommand',
      600,
    ];
    return $events;
  }

  /**
   * Subscriber for MudEvent CommandEvent event.
   *
   * @param \Drupal\slack_mud\Event\CommandEvent $event
   *   The command event.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function onCommand(CommandEvent $event) {
    // If the event is set for stopPropagation, don't process it here.
    // It's already been handled in another place.
    if (!$event->isStopPropagation()) {
      $results = $event->getResponse();
      $actingPlayer = $event->getActingPlayer();
      if ($actingPlayer) {
        // @TODO What about command plugins?
        $removeWords = [
          ' at ',
          ' to ',
          ' from ',
          ' with ',
        ];
        $rawCommand = $event->getCommandString();
        $command = trim(str_replace($removeWords, " ", $rawCommand));
        // Let's assume everything breaks nicely into individual words.
        $commandWords = explode(' ', $command);
        $verb = $commandWords[0];

        /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
        $pluginManager = \Drupal::service('plugin.manager.mud_command');
        /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
        $plugin = NULL;
        // Game plugins take precedence, so check a game command first.
        if (!$plugin) {
          // There wasn't a plugin for this directly. Use the game plugin ID to
          // determine what plugin prefix to use.
          $game = $actingPlayer->field_game->entity;
          if ($game) {
            $pluginPrefix = $game->field_plugin_identifier->value;
            if ($pluginManager->hasDefinition($pluginPrefix . '_' . $verb)) {
              $plugin = $pluginManager->createInstance($pluginPrefix . '_' . $verb);
            }
          }
        }
        // Didn't find a plugin for the game. Try a generic one.
        if (!$plugin) {
          if ($pluginManager->hasDefinition($verb)) {
            $plugin = $pluginManager->createInstance($verb);
          }
        }
        if (!$plugin) {
          // See if the game has a generic handler.
          $pluginId = $pluginPrefix . '_generic_command_handler';
          if ($pluginManager->hasDefinition($pluginId)) {
            $plugin = $pluginManager->createInstance($pluginId);
          }
        }
        // Now that we've had another chance to load a plugin, see if we can
        // perform the action.
        if ($plugin) {
          $plugin->perform($command, $actingPlayer, $results);
        }
      }
      if (!$results) {
        // Nothing processed a result. Treat it as an invalid command.
        $results[$actingPlayer->id()][] = t("You can't do that here.");
      }
      if ($results) {
        $event->setResponse($results);
      }
    }
  }

}
