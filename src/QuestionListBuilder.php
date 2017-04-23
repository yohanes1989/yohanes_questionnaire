<?php

namespace Drupal\yohanes_questionnaire;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Question entities.
 *
 * @ingroup yohanes_questionnaire
 */
class QuestionListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    //$header['id'] = $this->t('Question ID');
    $header['question'] = $this->t('Question');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\yohanes_questionnaire\Entity\Question */
    $row['question'] = $this->l(
      $entity->label(),
      new Url(
        'entity.question.edit_form', array(
          'question' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
