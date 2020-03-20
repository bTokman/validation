<p align="center"><h1 align="center">:panda_face:  PHP VALIDATION TRAIT :panda_face:</h1></p>
<p align="center">
    <a href="https://packagist.org/packages/b-tokman/validation" target="_blank">
        <img src="https://badgen.net/packagist/lang/b-tokman/validation" alt="Programming language">
    </a>
    <a href="https://scrutinizer-ci.com/g/bTokman/validation/?branch=master" target="_blank">
        <img src="https://scrutinizer-ci.com/g/bTokman/validation/badges/quality-score.png?b=master" alt="Scrutinizer code quality">
    </a>
    <a href="https://travis-ci.org/github/bTokman/validation" target="_blank">
        <img src="https://badgen.net/travis/bTokman/validation" alt="Travis build">
    </a>
</p>

This is simple PHP standalone Trait, that can be used in any object. The validation process based on the [Symfony Validation Component](https://symfony.com/doc/current/components/validator.html)

# :rocket:  Installation
This library requeired [PHP](https://www.php.net) version `7.2` or highter. And [composer](https://getcomposer.org/) - package manger for PHP.

```sh
  $ composer require b-tokman/validation 
```

# :bulb: Usage

After the installation you'll be able to use the `bTokman\validation\ValidationTrait` trait in your app.

**You cannot override a trait's property in the class where the trait is used.**
However, you can override a trait's property in a class that extends the class where the trait is used.

- Start using trait in your base class 
- Declare validation rules in your extended class. [List of availible Rules](https://symfony.com/doc/current/validation.html#constraints)
- On a new instance of your class just call method `validate`. 

**The validation result is** 
- `array` of errors `[[fieldName] => [errorMessage1, errorMessag2, ...]` .
- `null` if the validation was passed.



```php
class BaseObject
{
    use bTokman\validation\ValidationTrait;
}

......

class ValidationObject extends BaseObject
{
    public $validationRules = [
        'password' => [NotBlank::class, [Length::class, ['min' => 8]]],
    ];

    public $password;
}

......

$object = new ValidationObject();
   
$result = $object->validate();


```

```php

class ValidationObject
{
    use ValidationTrait;

    public function __construct()
    {
        $this->validationRules = [
            'password' => [NotBlank::class, [Length::class, ['min' => 8]]],
        ];
    }
    
    public $password;
}

$object = new ValidationObject();
   
$result = $object->validate();

```
