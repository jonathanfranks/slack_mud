<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\source;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use League\Csv\Reader;

/**
 * Source for MSG files in the Kyrandia source code.
 *
 * Available configuration options:
 * - path: Path to the MSG file. File streams are supported.
 *
 * @codingStandardsIgnoreStart
 *
 * Example with minimal options:
 * @code
 * source:
 *   plugin: kyrandia_msg
 *   path: /tmp/GALKYRAN.MSG
 *   ids: [id]
 * @endcode
 *
 * @MigrateSource(
 *   id = "kyrandia_msg",
 *   source_module = "kyrandia_migrate"
 * )
 */
class MSG extends SourcePluginBase implements ConfigurableInterface {

  /**
   * {@inheritdoc}
   *
   * @throws \InvalidArgumentException
   * @throws \Drupal\migrate\MigrateException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->setConfiguration($configuration);

    // Path is required.
    if (empty($this->configuration['path'])) {
      throw new \InvalidArgumentException('You must declare the "path" to the source LCS file in your source settings.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'path' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    // We must preserve integer keys for column_name mapping.
    $this->configuration = NestedArray::mergeDeepArray([
      $this->defaultConfiguration(),
      $configuration,
    ], TRUE);
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    return $this->configuration['path'];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\migrate\MigrateException
   */
  public function initializeIterator() {
    $contents = file_get_contents($this->configuration['path']);
    $contents = str_replace("\r\n \r\n", "\n\n", $contents);
    $raw_rows = preg_split("/([^||\n].*{)/", $contents, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    // The preg_split gives us content items in multiple rows.
    $rows = [];
    foreach ($raw_rows as $index => $raw_row) {
      $row = [];
      if (strpos($raw_row, '{') == FALSE) {
        // This is the content row. Its name is in the row before.
        $priorRow = array_key_exists($index, $raw_rows) ? $raw_rows[$index - 1] : '';
        $name = str_replace(' {', '', $priorRow);
        $endPos = strpos($raw_row, '}');
        $description = substr($raw_row, 0, $endPos);
        $rawType = substr($raw_row, $endPos);
        $type = str_replace(['} T ', "\n"], '', $rawType);
        $row['name'] = $name;
        $row['description'] = $description;
        $row['type'] = $type;
        $rows[] = $row;
      }
    }
    return new \ArrayIterator($rows);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids = [];
    $ids['name']['type'] = 'string';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    // MSG files have a name, a description, and a type.
    $fields = [
      'name' => 'name',
      'description' => 'description',
      'type' => 'type',
    ];
    return $fields;
  }

  /**
   * Get the generator.
   *
   * @param \Iterator $records
   *   The MSG records.
   *
   * @codingStandardsIgnoreStart
   *
   * @return \Generator
   *   The records generator.
   *
   * @codingStandardsIgnoreEnd
   */
  protected function getGenerator(\Iterator $records) {
    foreach ($records as $record) {
      yield $record;
    }
  }

}
