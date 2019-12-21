<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\Service\MudGameHandlerServiceInterface;

/**
 * Defines application features from the specific context.
 */
class D8SlackContext implements Context, SnippetAcceptingContext {

  /**
   * The game handler service.
   *
   * @var \Drupal\slack_mud\Service\MudGameHandlerServiceInterface
   */
  protected $gameHandler;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
    /** @var MudGameHandlerServiceInterface $gameHandler */
    $gameHandler = \Drupal::getContainer()->get('slack_mud.game_handler');
    $this->gameHandler = $gameHandler;
  }

  /**
   * The results from the latest command that was executed.
   *
   * @var array
   */
  protected $results;

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
   * @Then :player should be in :location
   */
  public function shouldBeIn($player, $expected) {
    $playerNode = $this->getPlayerByName($player);
    if (!$playerNode) {
      throw new \Exception(sprintf('Player %s not found.', $player));
    }
    $actualLocation = $playerNode->field_location->entity->getTitle();
    if ($actualLocation != $expected) {
      throw new \Exception(sprintf('Player %s should be in %s but is in %s.', $player, $expected, $actualLocation));
    }
  }

  /**
   * @Then :player should see
   */
  public function shouldSeePyString($player, PyStringNode $string) {
    $modifiedResult = $this->fixString($string->getRaw());
    $this->shouldSee($player, $modifiedResult);
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
      throw new \Exception(sprintf('No player named %s exists.', $player));
    }
    if (!array_key_exists($playerNode->id(), $this->results)) {
      throw new \Exception('No results for specified player.');
    }

    $found = FALSE;
    // There are problems specifying regular expressions like newlines in the
    // steps, so let's just convert them to text.
    foreach ($this->results[$playerNode->id()] as $result) {
      $modifiedResult = $this->fixString($result);
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
   * @Then :player should not see :expected
   */
  public function shouldNotSee($player, $expected) {
    $playerNode = $this->getPlayerByName($player);
    if (!$playerNode) {
      throw new \Exception(sprintf('No player named %s exists.', $player));
    }

    $found = FALSE;
    // If there aren't any results for that player, then they don't have the
    // expected message.
    if (array_key_exists($playerNode->id(), $this->results)) {
      // There are problems specifying regular expressions like newlines in the
      // steps, so let's just convert them to text.
      foreach ($this->results[$playerNode->id()] as $result) {
        $modifiedResult = $this->fixString($result);
        if ($modifiedResult == $expected) {
          $found = TRUE;
          break;
        }
      }
    }
    if ($found) {
      throw new \Exception('Expected message was found for specified player but should not have been.');
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

  /**
   * @Then :item item should be in :location
   */
  public function assertItemInLocation($item, $location) {
    $locationNode = $this->gameHandler->getLocationByName($location);
    if (!$this->gameHandler->locationHasItem($locationNode, $item, FALSE)) {
      throw new \Exception(sprintf('Location %s does not have %s.', $location, $item));
    }
  }

  /**
   * Removes all instances of given item from given location.
   *
   * @Given :location has no :item
   */
  public function removeItemFromLocation($location, $item) {
    $locationNode = $this->gameHandler->getLocationByName($location);
    do {
      $itemNode = $this->gameHandler->locationHasItem($locationNode, $item, TRUE);
    } while ($itemNode);
  }

  /**
   * @Then :item item should not be in :location
   */
  public function assertItemNotInLocation($item, $location) {
    $locationNode = $this->gameHandler->getLocationByName($location);
    if ($this->gameHandler->locationHasItem($locationNode, $item, FALSE)) {
      throw new \Exception(sprintf('Location %s has %s but should not.', $location, $item));
    }
  }

  /**
   * @Then :player should not have :item in inventory
   */
  public function shouldNotHaveInInventory($player, $item) {
    $playerNode = $this->getPlayerByName($player);
    if ($this->playerHasItem($playerNode, $item)) {
      throw new \Exception(sprintf('Player %s has %s in inventory', $player, $item));
    }
  }

  /**
   * @Then :player should have :item in inventory
   */
  public function shouldHaveInInventory($player, $item) {
    $playerNode = $this->getPlayerByName($player);
    if (!$this->playerHasItem($playerNode, $item)) {
      throw new \Exception(sprintf('Player %s does not have %s in inventory', $player, $item));
    }
  }

  /**
   * Checks if the player has the specified item.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player whose inventory we are checking.
   * @param string $targetItemName
   *   The item we're checking for.
   * @param bool $removeItem
   *   If TRUE, remove the item from the player's inventory when found.
   *
   * @return \Drupal\node\NodeInterface|bool
   *   FALSE if the player doesn't the item, otherwise the item in
   *   the player's inventory field.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function playerHasItem(NodeInterface $player, $targetItemName, $removeItem = FALSE) {
    foreach ($player->field_inventory as $delta => $item) {
      $itemName = strtolower(trim($item->entity->getTitle()));
      if (strpos($itemName, $targetItemName) === 0) {
        if ($removeItem) {
          unset($player->field_inventory[$delta]);
          $player->save();
        }
        return $item->entity;
      }
    }
    return FALSE;
  }

  /**
   * @param $result
   *
   * @return string
   */
  protected function fixString($result): string {
    $modifiedResult = strtr($result, [
      "\n" => "\\n",
      "\r" => "\\r",
      "\f" => "\\f",
    ]);
    return $modifiedResult;
  }

}
