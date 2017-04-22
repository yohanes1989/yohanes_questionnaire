<?php

namespace Drupal\yohanes_questionnaire;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Questionnaire submission entities.
 *
 * @ingroup yohanes_questionnaire
 */
class QuestionnaireSubmissionListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Questionnaire submission ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmission */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.questionnaire_submission.edit_form', array(
          'questionnaire_submission' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
