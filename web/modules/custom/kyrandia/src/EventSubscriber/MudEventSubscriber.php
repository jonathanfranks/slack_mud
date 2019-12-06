<?php

namespace Drupal\kyrandia\EventSubscriber;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
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
      $displayName = $targetPlayer->field_display_name->value;
      $isFemale = $kyrandiaProfile->field_kyrandia_is_female->value;
      $genderDescription = $isFemale ? $level->field_female_description->value : $level->field_male_description->value;
      $desc = sprintf($genderDescription, $displayName);

      // Use the inventory_other command to get the other player's items.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('inventory_other');
      $descInv = ' ' . $plugin->perform('', $targetPlayer) . '.';

      $desc .= $descInv;
      $event->setResponse(strip_tags($desc));
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
