<?php

namespace Drupal\entity_reference_with_label\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * @FieldType(
 *   id = "entity_reference_with_label",
 *   label = @Translation("Entity reference with description"),
 *   description = @Translation("An entity field containing an entity reference
 *   with a description."),
 *   category = @Translation("Reference with description"),
 *   default_widget = "entity_reference_select_with_label",
 *   default_formatter = "entity_reference_with_label_view",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class EntityReferenceWithLabel extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $properties['label'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Label'))
      ->setRequired(TRUE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['label'] = [
      'type' => 'varchar',
      'length' => 255,
    ];
    return $schema;
  }

}
