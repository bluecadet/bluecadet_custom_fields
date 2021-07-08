<?php

namespace Drupal\bluecadet_custom_fields\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\text\Plugin\Field\FieldType\TextLongItem;

/**
 * Defines the 'string' field type with label.
 *
 * @FieldType(
 *   id = "text_with_label",
 *   label = @Translation("Text (formatted, long) with Label"),
 *   description = @Translation("This field stores a long text with a text format nd label."),
 *   category = @Translation("Text"),
 *   default_widget = "text_with_label_widget",
 *   default_formatter = "text_with_label_formatter"
 * )
 */
class TextWithLabel extends TextLongItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'label_case_sensitive' => FALSE,
      'label_max_length' => 255,
      'label_is_ascii' => FALSE,
      'label_is_required' => TRUE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'label' => [
          'type' => $field_definition->getSetting('label_is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
          'length' => (int) $field_definition->getSetting('label_max_length'),
          'binary' => $field_definition->getSetting('case_sensitive'),
        ],
        'value' => [
          'type' => 'text',
          'size' => 'big',
        ],
        'format' => [
          'type' => 'varchar_ascii',
          'length' => 255,
        ],
      ],
      'indexes' => [
        'format' => ['format'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // This is called very early by the user entity roles field. Prevent
    // early t() calls by using the TranslatableMarkup.
    $properties['label'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Label'))
      ->setSetting('case_sensitive', $field_definition->getSetting('label_case_sensitive'))
      ->setRequired($field_definition->getSetting('label_is_required'));

    $properties += parent::propertyDefinitions($field_definition);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    if ($max_length = $this->getSetting('label_max_length')) {
      $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
      $constraints[] = $constraint_manager->create('ComplexData', [
        'label' => [
          'Length' => [
            'max' => $max_length,
            'maxMessage' => $this->t('%name: may not be longer than @max characters.', [
              '%name' => $this->getFieldDefinition()->getLabel(),
              '@max' => $max_length,
            ]),
          ],
        ],
      ]);
    }

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values = parent::generateSampleValue($field_definition);

    $random = new Random();
    $values['label'] = $random->word(mt_rand(1, $field_definition->getSetting('label_max_length')));

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];

    $element['label_max_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Label Maximum length'),
      '#default_value' => $this->getSetting('label_max_length'),
      '#required' => TRUE,
      '#description' => $this->t('The maximum length of the label in characters.'),
      '#min' => 1,
      '#disabled' => $has_data,
    ];

    $element['label_is_required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Label is required'),
      '#default_value' => $this->getSetting('label_is_required'),
      '#required' => TRUE,
      '#description' => $this->t('If the label is required for the field to be considered empty.'),
      '#min' => 1,
      '#disabled' => $has_data,
    ];

    return $element;
  }

}
