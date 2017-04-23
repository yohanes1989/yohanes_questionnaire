<?php

namespace Drupal\yohanes_questionnaire;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Question entity.
 *
 * @see \Drupal\yohanes_questionnaire\Entity\Question.
 */
class QuestionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
      $isOwner = $entity->getOwnerId() == $account->id();

    /** @var \Drupal\yohanes_questionnaire\Entity\QuestionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished question entities');
        }

          return AccessResult::allowed();
      case 'update':
          $permissions = ['edit question entities'];
          if($isOwner){
              $permissions[] = 'edit own question entities';
          }

        return AccessResult::allowedIfHasPermissions($account, $permissions, 'OR');
      case 'delete':
          $permissions = ['delete question entities'];
          if($isOwner){
              $permissions[] = 'delete own question entities';
          }

          return AccessResult::allowedIfHasPermissions($account, $permissions, 'OR');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add question entities');
  }

}
