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
    $header['name'] = $this->t('Name');
    $header['result'] = $this->t('Result');
    $header['submitted_by'] = $this->t('Submitted by');
    $header['submitted_at'] = $this->t('Submitted at');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmission */
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.questionnaire_submission.edit_form', array(
          'questionnaire_submission' => $entity->id(),
        )
      )
    );
      $row['result'] = $entity->getResult() + 0;
    $row['submitted_by'] = $this->l(
        $entity->getOwner()->getAccountName(),
        new Url(
            'entity.user.canonical', array(
                'user' => $entity->getOwner()->id(),
            )
        )
    );
    $row['submitted_at'] = \Drupal::service('date.formatter')->format($entity->getCreatedTime());
    return $row + parent::buildRow($entity);
  }

}
