<?php

/**
 * @file
 * Contains questionnaire_submission.page.inc.
 *
 * Page callback for Questionnaire submission entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Questionnaire submission templates.
 *
 * Default template: questionnaire_submission.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function yohanes_questionnaire_preprocess_questionnaire_submission(array &$variables) {
  // Fetch QuestionnaireSubmission Entity Object.
  $questionnaire_submission = $variables['elements']['#questionnaire_submission'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
