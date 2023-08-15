<?php

namespace Drupal\bluecadet_custom_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\NumericItemBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'float_multiple' field type.
 *
 * @FieldType(
 *   id = "float_multiple",
 *   label = @Translation("Mulitple Number (floats)"),
 *   description = @Translation("This field stores a number in the database in a floating point format."),
 *   category = @Translation("Number"),
 *   default_widget = "number_multiple",
 *   default_formatter = "number_decimal_multiple"
 * )
 */
class MultipleFloats extends NumericItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $settings = $field_definition->getSettings();

    $count = $settings['count'];

    for ($i = 0; $i < $count; $i++) {
      $properties['value' . $i] = DataDefinition::create('float')
        ->setLabel(t('Float'))
        ->setRequired(TRUE);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [];

    $settings = $field_definition->getSettings();
    $count = $settings['count'];

    for ($i = 0; $i < $count; $i++) {
      $columns['value' . $i] = [
        'type' => 'float',
      ];
    }

    return [
      'columns' => $columns,
    ];
  }


  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'count' => 1,
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = parent::storageSettingsForm($form, $form_state, $has_data);

    $element['count'] = [
      '#type' => 'number',
      '#title' => $this->t('Count'),
      '#step' => 1,
      '#max' => 10,
      '#default_value' => $this->getSetting('count'),
      '#description' => $this->t('The number of number fields for this field. Max is 10.'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::fieldSettingsForm($form, $form_state);
    // $settings = $this->getSettings();

    $element['min']['#step'] = 'any';
    $element['max']['#step'] = 'any';


    // $element = $new + $element;
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $settings = $this->getSettings();
    // ksm($settings, $this, $this->getValue());
    $count = $settings['count'];

    for ($i = 0; $i < $count; $i++) {
      if (empty($this->{'value' . $i}) && (string) $this->{'value' . $i} !== '0') {
        // return TRUE;
      }
    }

    return FALSE;
  }











  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    // $settings = $field_definition->getSettings();
    // $precision = rand(10, 32);
    // $scale = rand(0, 2);
    // $max = is_numeric($settings['max']) ? $settings['max'] : pow(10, ($precision - $scale)) - 1;
    // $min = is_numeric($settings['min']) ? $settings['min'] : -pow(10, ($precision - $scale)) + 1;
    // // @see "Example #1 Calculate a random floating-point number" in
    // // http://php.net/manual/function.mt-getrandmax.php
    // $random_decimal = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    // $values['value'] = self::truncateDecimal($random_decimal, $scale);
    // return $values;
  }

}
