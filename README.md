# user_registrationpassword_rest

Drupal 8+ user registration with password over REST module. 

* You probably want to give `Access POST on User registration with password resource` permission to `ANONYMOUS USER`.

* `POST /user/register-with-password?_format=json`
```
{
    "name": [{"value":"username"}],
    "pass": [{"value":"my password"}],
    "mail": [{"value":"email@email.com"}]
}
```
