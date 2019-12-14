<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\node\Entity\Node;
use Drupal\slack_mud\Event\CommandEvent;

/**
 * Defines application features from the specific context.
 */
class D8SlackContext implements Context, SnippetAcceptingContext {

  /**
   * The results from the latest command that was executed.
   *
   * @var array
   */
  protected $results;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @When :player performs :command
   */
  public function performs($player, $command) {
    $playerNode = $this->getPlayerByName($player);
    $eventDispatcher = \Drupal::getContainer()->get('event_dispatcher');
    $mudEvent = new CommandEvent($playerNode, $command);
    $mudEvent = $eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
    $results = $mudEvent->getResponse();
    $this->results = $results;
  }

  /**
   * @Then :player should see :expected
   */
  public function shouldSee($player, $expected) {
    $playerNode = $this->getPlayerByName($player);
    if (!$this->results) {
      throw new \Exception('No result from last command.');
    }
    if (!$playerNode) {
      throw new \Exception('No result from last command.');
    }
    if (!array_key_exists($playerNode->id(), $this->results)) {
      throw new \Exception('No results for specified player.');
    }

    $found = FALSE;
    // There are problems specifying regular expressions like newlines in the
    // steps, so let's just convert them to text.
    foreach ($this->results[$playerNode->id()] as $result) {
      $modifiedResult = strtr($result, ["\n" => "\\n", "\r" => "\\r"]);
      if ($modifiedResult == $expected) {
        $found = TRUE;
        break;
      }
    }

    if (!$found) {
      throw new \Exception('Expected message not found for specified player.');
    }
  }

  /**
   * @Then :player should not have any messages
   */
  public function shouldNotHaveAnyMessages($player) {
    $playerNode = $this->getPlayerByName($player);
    if (array_key_exists($playerNode->id(), $this->results)) {
      throw new \Exception(sprintf('Results for found for %s.', $player));
    }
  }

  /**
   * Returns a player node from the player name.
   *
   * @param string $playerName
   *   The name of the player to load.
   *
   * @return \Drupal\node\NodeInterface
   *   The player node.
   */
  protected function getPlayerByName($playerName) {
    $playerNode = NULL;
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'player')
      ->condition('title', $playerName);
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $playerNode = Node::load($id);
    }
    return $playerNode;
  }

}
