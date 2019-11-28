<?php

namespace Drupal\kyrandia\Plugin\KyrandiaCommand;

use Drupal\kyrandia\KyrandiaCommandPluginInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Level command plugin implementation.
 *
 * @KyrandiaCommandPlugin(
 *   id = "level",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\KyrandiaCommand
 */
class Level extends KyrandiaCommandPluginBase implements KyrandiaCommandPluginInterface {

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players can kneel at the willow tree at location 0 to go from level 1 to
    // level 2.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      $result = t('You are currently level :level, :title.',
        [
          ':level' => $profile->field_kyrandia_level->entity->getName(),
          ':title' => $profile->field_kyrandia_level->entity->description->value,
        ]);
    }
    return $result;
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
    // @TODO: Service-ize this.
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
