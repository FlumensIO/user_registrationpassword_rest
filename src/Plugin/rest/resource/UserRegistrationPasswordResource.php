<?php

namespace Drupal\user_registrationpassword_rest\Plugin\rest\resource;

use Drupal\user\UserInterface;
use Drupal\user\Plugin\rest\resource\UserRegistrationResource;

use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Represents user registration with password as a resource.
 *
 * @RestResource(
 *   id = "user_registrationpassword_rest",
 *   label = @Translation("User registration with password"),
 *   serialization_class = "Drupal\user\Entity\User",
 *   uri_paths = {
 *     "create" = "/user/register-with-password",
 *   },
 * )
 */
class UserRegistrationPasswordResource extends UserRegistrationResource
{
  public function post(UserInterface $account = null)
  {
    if (!isset($account)){
     return $this->resendVerificationEmail();
    }

    $response = parent::post($account);

    // keep it blocked
    $account->block();
    $account->save();

    return $response;
  }

  protected function resendVerificationEmail() {
    \Drupal::logger('user_registrationpassword_rest')->notice("Verification email reset request.");
      
    $request = \Drupal::request();
    $mailParam = $request->query->get('email');
    if (!$mailParam){
      throw new BadRequestHttpException("If you're trying to request a verification email resend then please provide an email url param");
    }
    
    $mail = str_replace(" ", "+", $mailParam);
    $user = user_load_by_mail($mail);
    if (!$user){
      throw new BadRequestHttpException("No such user was found for {$mail}");
    }

    if ($user->isActive()){
      throw new BadRequestHttpException("This account is already activated");
    }

    $this->sendEmailNotifications($user);

    \Drupal::logger('user_registrationpassword_rest')->notice("Verification email reset request done.");
    
    $response = ['message' => "Verification mail was sent to {$mail}"];
    return new ResourceResponse($response);
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
