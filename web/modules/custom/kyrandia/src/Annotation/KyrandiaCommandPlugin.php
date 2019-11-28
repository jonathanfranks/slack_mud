<?php

namespace Drupal\kyrandia\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Kyrandia command plugin annotation object.
 *
 * Plugin Namespace: Plugin\KyrandiaCommand
 *
 * @package Drupal\kyrandia\Annotation
 *
 * @Annotation
 */
class KyrandiaCommandPlugin extends Plugin {

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

}
