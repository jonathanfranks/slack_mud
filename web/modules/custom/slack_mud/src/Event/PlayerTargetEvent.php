<?php

namespace Drupal\slack_mud\Event;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Base event that targets a player.
 *
 * @package Drupal\slack_mud\Event
 */
class PlayerTargetEvent extends Event {

  /**
   * The player performing the action.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $actingPlayer;

  /**
   * The player being looked at.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $targetPlayer;

  /**
   * The response of the action, in this case, the player's description.
   *
   * @var string
   */
  protected $response;

  /**
   * LookAtPlayer constructor.
   *
   * @param \Drupal\node\NodeInterface $acting_player
   *   The player performing the action.
   * @param \Drupal\node\NodeInterface $target_player
   *   The player being looked at.
   */
  public function __construct(NodeInterface $acting_player, NodeInterface $target_player) {
    $this->actingPlayer = $acting_player;
    $this->targetPlayer = $target_player;
  }

  /**
   * Gets the acting player node.
   *
   * @return \Drupal\node\NodeInterface
   *   The acting player node.
   */
  public function getActingPlayer() {
    return $this->actingPlayer;
  }

  /**
   * Gets the target player node.
   *
   * @return \Drupal\node\NodeInterface
   *   The target player node.
   */
  public function getTargetPlayer() {
    return $this->targetPlayer;
  }

  /**
   * Gets the current response text.
   *
   * @return string
   *   The current response text.
   */
  public function getResponse(): string {
    return $this->response;
  }

  /**
   * Sets new response text.
   *
   * @param string $response
   *   New response text.
   */
  public function setResponse(string $response) {
    $this->response = $response;
  }

}
