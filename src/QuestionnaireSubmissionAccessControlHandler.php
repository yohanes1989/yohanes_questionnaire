<?php

namespace Drupal\yohanes_questionnaire;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Questionnaire submission entity.
 *
 * @see \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmission.
 */
class QuestionnaireSubmissionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmissionInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view questionnaire submission entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit questionnaire submission entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete questionnaire submission entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add questionnaire submission entities');
  }

}
