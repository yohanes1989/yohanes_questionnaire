<?php

namespace Drupal\yohanes_questionnaire\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Questionnaire submission entities.
 */
class QuestionnaireSubmissionViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
