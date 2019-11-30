<?php

namespace Drupal\kyrandia\EventSubscriber;

use Drupal\Component\Uuid\Com;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\Event\LookAtPlayerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MudEventSubscriber.
 */
class MudEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LookAtPlayerEvent::LOOK_AT_PLAYER_EVENT] = [
      'onLookAtPlayer',
      600,
    ];
    $events[CommandEvent::COMMAND_EVENT] = [
      'onCommand',
      600,
    ];
    return $events;
  }

  /**
   * Subscriber for the MudEvent LookAtPlayer event.
   *
   * @param \Drupal\slack_mud\Event\LookAtPlayerEvent $event
   *   The LookAtPlayer event.
   */
  public function onLookAtPlayer(LookAtPlayerEvent $event) {
    $targetPlayer = $event->getTargetPlayer();
    $kyrandiaProfile = $this->getKyrandiaProfile($targetPlayer);
    if ($kyrandiaProfile) {
      $level = $kyrandiaProfile->field_kyrandia_level->entity;
      $desc = $level->field_male_description->value;
      $event->setResponse(strip_tags($desc));
    }
  }

  /**
   * Subscriber for MudEvent CommandEvent event.
   *
   * @param \Drupal\slack_mud\Event\CommandEvent $event
   *   The command event.
   */
  public function onCommand(CommandEvent $event) {
    $result = NULL;
    $actingPlayer = $event->getActingPlayer();
    $kyrandiaProfile = $this->getKyrandiaProfile($actingPlayer);
    if ($kyrandiaProfile) {
      // @TODO What about command plugins?
      $removeWords = [
        ' at ',
        ' to ',
        ' from ',
        ' with ',
      ];
      $rawCommand = $event->getCommandString();
      $command = str_replace($removeWords, " ", $rawCommand);
      // Let's assume everything breaks nicely into individual words.
      $commandWords = explode(' ', $command);
      $verb = $commandWords[0];

      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance($verb);
      if ($plugin) {
        $result = $plugin->perform($command, $actingPlayer);
      }
    }
    if ($result) {
      $event->setResponse($result);
    }
  }

  /**
   * Gets the Kyrandia profile node for the given player node.
   *
   * @param \Drupal\node\NodeInterface $targetPlayer
   *   The player.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The player's Kyrandia profile node.
   */
  protected function getKyrandiaProfile(NodeInterface $targetPlayer) {
    $kyrandiaProfile = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'kyrandia_profile')
      ->condition('field_player.target_id', $targetPlayer->id());
    $kyrandiaProfileNids = $query->execute();
    if ($kyrandiaProfileNids) {
      $kyrandiaProfileNid = reset($kyrandiaProfileNids);
      $kyrandiaProfile = Node::load($kyrandiaProfileNid);
    }
    return $kyrandiaProfile;
  }

}
