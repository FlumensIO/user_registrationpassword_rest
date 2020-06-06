<?php

namespace Drupal\user_registrationpassword_rest\Plugin\rest\resource;

use Drupal\user\UserInterface;
use Drupal\user\Plugin\rest\resource\UserRegistrationResource;

/**
 * Represents user registration with password as a resource.
 *
 * @RestResource(
 *   id = "user_registrationpassword_rest",
 *   label = @Translation("User registration with password"),
 *   serialization_class = "Drupal\user\Entity\User",
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/user/register-with-password",
 *   },
 * )
 */
class UserRegistrationPasswordResource extends UserRegistrationResource
{
  public function post(UserInterface $account = null)
  {
    $response = parent::post($account);

    // keep it blocked
    $account->block();
    $account->save();

    return $response;
  }

  protected function sendEmailNotifications(UserInterface $account)
  {
    $params['account'] = $account;
    \Drupal::service('plugin.manager.mail')->mail(
      'user_registrationpassword',
      'register_confirmation_with_pass',
      $account->getEmail(),
      $account->getPreferredLangcode(),
      $params
    );
  }
}
