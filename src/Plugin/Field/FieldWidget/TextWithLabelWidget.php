<?php

namespace Drupal\bluecadet_custom_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;

/**
 * Plugin implementation of the 'entity_reference_autocomplete_vis' widget.
 *
 * @FieldWidget(
 *   id = "text_with_label_widget",
 *   label = @Translation("Text With Label Widget"),
 *   description = @Translation("A"),
 *   field_types = {
 *     "text_with_label"
 *   }
 * )
 */
class TextWithLabelWidget extends StringTextareaWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'label_title' => 'Label',
      'size' => 60,
      'placeholder' => '',
      'value_title' => 'Value',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element['label_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label Title'),
      '#default_value' => $this->getSetting('label_title'),
      '#description' => $this->t('The text that will display in the form as the title for the "label" field.'),
    ];
    $element['size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of label textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];
    $element['value_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value Title'),
      '#default_value' => $this->getSetting('value_title'),
      '#description' => $this->t('The text that will display in the form as the title for the "long text" field.'),
    ];

    $element += parent::settingsForm($form, $form_state);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary[] = $this->t('Textfield size: @size', ['@size' => $this->getSetting('size')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['label'] = [
      '#type' => 'textfield',
      '#title' => $this->getSetting('label_title'),
      '#default_value' => $items[$delta]->label ?? NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#attributes' => ['class' => ['js-text-full', 'text-full']],
      '#weight' => 1,
    ];

    // Set WYSIWYG enabled text area.
    $element['value']['#type'] = 'text_format';
    $element['value']['#title'] = $this->getSetting('value_title');
    $element['value']['#title_display'] = "before";
    $element['value']['#format'] = $items[$delta]->format;
    $element['value']['#placeholder'] = "";
    $element['value']['#weight'] = 2;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$intval) {
      $val = $intval['value']['value'];
      $format = $intval['value']['format'];
      $intval['value'] = $val;
      $intval['format'] = $format;
    }
    return $values;
  }

}
