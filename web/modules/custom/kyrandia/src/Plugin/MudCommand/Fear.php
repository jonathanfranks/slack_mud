<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Fear command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_fear",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Fear extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // "Fear no evil" advances to level 5 at the dead wooded glade.
    // Players say a command at the temple to get to level 3.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if ($commandText == 'fear no evil' && $loc->getTitle() == 'Location 16') {
      // Player is at the temple.
      $level = $profile->field_kyrandia_level->entity;
      if ($level->getName() == '4') {
        $this->advanceLevel($profile, 5);

        $result = "As you boldly defy the evil, the Goddess Tashanna rewards you for your courage with more knowledge and power. You are now level 5!";
      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
