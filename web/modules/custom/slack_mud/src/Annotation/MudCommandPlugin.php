<?php

namespace Drupal\slack_mud\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a MUD command plugin annotation object.
 *
 * Plugin Namespace: Plugin\MudCommand
 *
 * @package Drupal\kyrandia\Annotation
 *
 * @Annotation
 */
class MudCommandPlugin extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

  /**
   * An array of synonyms that can also be used to call this command plugin.
   *
   * @var string[]
   */
  public $synonyms = [];

}
