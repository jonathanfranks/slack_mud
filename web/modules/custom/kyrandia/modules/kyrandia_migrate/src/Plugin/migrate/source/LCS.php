<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\source;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use League\Csv\Reader;

/**
 * Source for LCS files in the Kyrandia source code.
 *
 * Available configuration options:
 * - path: Path to the LCS file. File streams are supported.
 *
 * @codingStandardsIgnoreStart
 *
 * Example with minimal options:
 * @code
 * source:
 *   plugin: kyrandia_lcs
 *   path: /tmp/GALKYRAN.LCS
 *   ids: [id]
 * @endcode
 *
 * @MigrateSource(
 *   id = "kyrandia_lcs",
 *   source_module = "kyrandia_migrate"
 * )
 */
class LCS extends SourcePluginBase implements ConfigurableInterface {

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
    $raw_rows = explode("\n\n", $contents);
    $rows = [];
    foreach ($raw_rows as $raw_row) {
      $row = [];
      if (trim($raw_row)) {
        $split_row = explode("\n", $raw_row);
        foreach ($split_row as $split_columns) {
          $split_item = explode(": ", $split_columns);
          if (count($split_item) == 1) {
            // Only one element, this is probably the exits.
            if (strpos($split_item[0], "N:") === 0) {
              $split_exits = explode(" ", $split_columns);
              $split_exit_array = [];
              foreach ($split_exits as $exit_item) {
                $split_exit_item = explode(":", $exit_item);
                if (count($split_exit_item) > 1 && $split_exit_item[1] != -1) {
                  $split_exit_array[] = [
                    'direction' => $split_exit_item[0],
                    'location' => $split_exit_item[1],
                  ];
                }
              }
              if ($split_exit_array) {
                $row['exits'] = $split_exit_array;
              }
            }
          }
          elseif (count($split_item) > 1) {
            $row[$split_item[0]] = $split_item[1];
          }
        }
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
    foreach ($this->configuration['keys'] as $value) {
      $ids[$value]['type'] = 'string';
    }
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    // If fields are not defined, use the header record.
    if (empty($this->configuration['fields'])) {
      $header = $this->getReader()->getHeader();
      return array_combine($header, $header);
    }
    $fields = [];
    foreach ($this->configuration['fields'] as $field) {
      $fields[$field['name']] = isset($field['label']) ? $field['label'] : $field['name'];
    }
    return $fields;
  }

  /**
   * Get the generator.
   *
   * @param \Iterator $records
   *   The LCS records.
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
