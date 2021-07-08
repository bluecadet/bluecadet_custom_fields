<?php

namespace Drupal\bluecadet_custom_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;

/**
 * Plugin implementation of the 'TextWithLabel' formatter.
 *
 * @FieldFormatter(
 *   id = "text_with_label_formatter",
 *   label = @Translation("Text with Label formatter"),
 *   description = @Translation(""),
 *   field_types = {
 *     "text_with_label"
 *   }
 * )
 */
class TextWithLabelFormatter extends TextDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {

      $elements[$delta]['label'] = [
        '#type' => 'inline_template',
        '#template' => '{{ value|nl2br }}',
        '#context' => ['value' => $item->label],
      ];

      $elements[$delta]['value'] = [
        '#type' => 'processed_text',
        '#text' => $item->value,
        '#format' => $item->format,
        '#langcode' => $item->getLangcode(),
      ];
    }

    return $elements;
  }

}
