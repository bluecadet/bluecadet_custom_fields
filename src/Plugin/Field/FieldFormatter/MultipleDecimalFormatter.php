<?php

namespace Drupal\bluecadet_custom_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\NumericFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'number_decimal_multiple' formatter.
 *
 * The 'Default' formatter is different for integer fields on the one hand, and
 * for decimal and float fields on the other hand, in order to be able to use
 * different settings.
 *
 * @FieldFormatter(
 *   id = "number_decimal_multiple",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "float_multiple"
 *   }
 * )
 */
class MultipleDecimalFormatter extends NumericFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'thousand_separator' => '',
      'decimal_separator' => '.',
      'scale' => 2,
      'prefix_suffix' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['decimal_separator'] = [
      '#type' => 'select',
      '#title' => $this->t('Decimal marker'),
      '#options' => ['.' => $this->t('Decimal point'), ',' => $this->t('Comma')],
      '#default_value' => $this->getSetting('decimal_separator'),
      '#weight' => 5,
    ];
    $elements['scale'] = [
      '#type' => 'number',
      '#title' => $this->t('Scale', [], ['context' => 'decimal places']),
      '#min' => 0,
      '#max' => 10,
      '#default_value' => $this->getSetting('scale'),
      '#description' => $this->t('The number of digits to the right of the decimal.'),
      '#weight' => 6,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $settings = $this->getFieldSettings();

    foreach ($items as $delta => $item) {
      $outputs = [];
      for ($i = 0; $i < $settings['count']; $i++) {

        $output = $this->numberFormat($item->{'value' . $i});

        // Account for prefix and suffix.
        if ($this->getSetting('prefix_suffix')) {
          $prefixes = isset($settings['prefix']) ? array_map(['Drupal\Core\Field\FieldFilteredMarkup', 'create'], explode('|', $settings['prefix'])) : [''];
          $suffixes = isset($settings['suffix']) ? array_map(['Drupal\Core\Field\FieldFilteredMarkup', 'create'], explode('|', $settings['suffix'])) : [''];
          $prefix = (count($prefixes) > 1) ? $this->formatPlural($item->value, $prefixes[0], $prefixes[1]) : $prefixes[0];
          $suffix = (count($suffixes) > 1) ? $this->formatPlural($item->value, $suffixes[0], $suffixes[1]) : $suffixes[0];
          $output = $prefix . $output . $suffix;
        }
        // Output the raw value in a content attribute if the text of the HTML
        // element differs from the raw value (for example when a prefix is used).
        if (isset($item->_attributes) && $item->value != $output) {
          $item->_attributes += ['content' => $item->value];
        }
        $outputs[] = $output;
      }
      $elements[$delta] = ['#markup' => implode(", ", $outputs)];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function numberFormat($number) {
    return number_format($number, $this->getSetting('scale'), $this->getSetting('decimal_separator'), $this->getSetting('thousand_separator'));
  }

}
