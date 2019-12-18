<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Drupal\kyrandia\Plugin\MudCommand\Kneel;
use Drupal\kyrandia\Plugin\MudCommand\KyrandiaCommandPluginBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Defines application features from the specific context.
 */
class KyrandiaContext implements Context, SnippetAcceptingContext {

  /**
   * The game handler service.
   *
   * @var \Drupal\kyrandia\Service\KyrandiaGameHandlerServiceInterface
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
    $gameHandler = \Drupal::getContainer()->get('kyrandia.game_handler');
    $this->gameHandler = $gameHandler;
  }

  /**
   * @When :player should not have the spell :spell
   */
  public function shouldNotHaveTheSpell($player, $spell) {
    $playerNode = $this->getPlayerByName($player);
    $profile = $this->gameHandler->getKyrandiaProfile($playerNode);
    if ($this->gameHandler->playerHasSpell($playerNode, $spell)) {
      throw new \Exception(sprintf('%s has the spell %s.', $player, $spell));
    }
  }

  /**
   * @Then :player should have :gold gold
   */
  public function shouldHaveGold($player, $gold) {
    $playerNode = $this->getPlayerByName($player);
    $profile = $this->gameHandler->getKyrandiaProfile($playerNode);
    if ($profile->field_kyrandia_gold->value != $gold) {
      throw new \Exception(sprintf('%s should have %s gold, but has %s.', $player, $gold, $profile->field_kyrandia_gold->value));
    }
  }

  /**
   * @Then :player should have the spell :spell
   */
  public function shouldHaveTheSpell($player, $spell) {
    $playerNode = $this->getPlayerByName($player);
    $profile = $this->gameHandler->getKyrandiaProfile($playerNode);
    if (!$this->gameHandler->playerHasSpell($playerNode, $spell)) {
      throw new \Exception(sprintf('%s does not have the spell %s.', $player, $spell));
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
   * Checks if specified player has specified spell.
   *
   * @param \Drupal\node\NodeInterface $player
   *   The player.
   * @param string $spellName
   *   The spell name.
   *
   * @return \Drupal\node\NodeInterface
   *   The spell if the player has the spell in their spellbook or null.
   */
  protected function playerHasSpell(NodeInterface $player, $spellName) {
    $profile = $this->gameHandler->getKyrandiaProfile($player);
    foreach ($profile->field_kyrandia_spellbook as $spell) {
      if ($spell->entity->getName() == $spellName) {
        return $spell->entity;
      }
    }
    return FALSE;
  }

  /**
   * @Given :player should be level :level
   */
  public function shouldBeLevel($player, $level) {
    $playerNode = $this->getPlayerByName($player);
    $profile = $this->gameHandler->getKyrandiaProfile($playerNode);
    $actualLevel = $profile->field_kyrandia_level->entity->getName();
    if ($actualLevel != $level) {
      throw new \Exception(sprintf('Player %s is not level %s, but is level %s.', $player, $level, $actualLevel));
    }
  }

  /**
   * @Given the dragon is in :location
   */
  public function moveDragonTo($location) {
    $locationNode = $this->gameHandler->getLocationByName($location);
    if (!$locationNode) {
      throw new \Exception(sprintf('Location %s does not exist.', $location));
    }
    $this->gameHandler->moveDragon($locationNode);
  }

  /**
   * @Given :player is married to :spouse
   */
  public function isMarriedTo($player, $spouse) {
    $playerNode = $this->getPlayerByName($player);
    $spousePlayerNode = $this->getPlayerByName($spouse);
    if (!$playerNode) {
      throw new \Exception(sprintf('No player called %s.', $player));
    }
    if (!$spousePlayerNode) {
      throw new \Exception(sprintf('No player called %s.', $spouse));
    }
    $profile = $this->gameHandler->getKyrandiaProfile($playerNode);
    $profile->field_kyrandia_married_to = $spousePlayerNode;
    $profile->save();
  }

  /**
   * @Then :player should be married to :spouse
   */
  public function assertMarriedTo($player, $spouse) {
    $playerNode = $this->getPlayerByName($player);
    $spousePlayerNode = $this->getPlayerByName($spouse);
    if (!$playerNode) {
      throw new \Exception(sprintf('No player called %s.', $player));
    }
    if (!$spousePlayerNode) {
      throw new \Exception(sprintf('No player called %s.', $spouse));
    }
    $profile = $this->gameHandler->getKyrandiaProfile($playerNode);
    if ($profile->field_kyrandia_married_to->target_id != $spousePlayerNode->id()) {
      throw new \Exception(sprintf('Player %s is not married to %s.', $player, $spouse));
    }
  }

  /**
   * @Given the current temple chant count is set to :count
   */
  public function setCurrentTempleChantCountIs($count) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'game')
      ->condition('title', 'kyrandia');
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $game = Node::load($id);
      $this->gameHandler->saveInstanceSetting($game, 'currentTempleChantCount', $count);
    }
    else {
      throw new \Exception(sprintf('No game called %s.', 'kyrandia'));
    }
  }

  /**
   * @Then the current temple chant count should be :count
   */
  public function getCurrentTempleChantCountIs($count) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'game')
      ->condition('title', 'kyrandia');
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $game = Node::load($id);
      $current = $this->gameHandler->getInstanceSetting($game, 'currentTempleChantCount', 0);
      if ($count != $current) {
        throw new \Exception(sprintf('The temple chant count is supposed to be %s but is currently %s.', $count, $current));
      }
    }
    else {
      throw new \Exception(sprintf('No game called %s.', 'kyrandia'));
    }
  }

  /**
   * @Given the opensesame is set to :count
   */
  public function setOpenSesame($count) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'game')
      ->condition('title', 'kyrandia');
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $game = Node::load($id);
      $this->gameHandler->saveInstanceSetting($game, 'opensesame', $count);
    }
    else {
      throw new \Exception(sprintf('No game called %s.', 'kyrandia'));
    }
  }

  /**
   * @Then the opensesame should be :count
   */
  public function getOpenSesame($count) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'game')
      ->condition('title', 'kyrandia');
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $game = Node::load($id);
      $current = $this->gameHandler->getInstanceSetting($game, 'opensesame', 0);
      if ($count != $current) {
        throw new \Exception(sprintf('The opensesame is supposed to be %s but is currently %s.', $count, $current));
      }
    }
    else {
      throw new \Exception(sprintf('No game called %s.', 'kyrandia'));
    }
  }

  /**
   * @Given the Kyrandia random number will generate :number
   */
  public function forceRandomNumber($number) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'game')
      ->condition('title', 'kyrandia');
    $ids = $query->execute();
    if ($ids) {
      $id = reset($ids);
      $game = Node::load($id);
      $this->gameHandler->saveInstanceSetting($game, 'forceRandomNumber', $number);
    }
    else {
      throw new \Exception(sprintf('No game called %s.', 'kyrandia'));
    }
  }

  /**
   * @AfterScenario
   */
  public function cleanRandom() {
    // Sets the random number generator to NULL, which prevents the numbers from
    // being forced.
    $this->forceRandomNumber(NULL);
  }

}
