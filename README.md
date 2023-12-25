# Typed - Primitive as Typed Object

Library that maps pritmitve types into typed objects.
Can be used to maps request/input into objects of defined classes.
Gives [ErrorCollection](https://github.com/xtompie/result/blob/master/src/ErrorCollection.php) on fail.

## Requiments

PHP >= 8.0

## Installation

Using [composer](https://getcomposer.org/)

```shell
composer require xtompie/typed
```

## Docs

### Basic

```php
<?php

use Xtompie\Typed\Max;
use Xtompie\Typed\Min;
use Xtompie\Typed\NotBlank;
use Xtompie\Typed\Typed;

Class PetPayload
{
    public function __construct(
        #[NotBlank]
        protected string $name,

        #[NotBlank]
        #[Min(0)]
        #[Max(30)]
        protected int $age,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function age(): int
    {
        return $this->age;
    }
}

$pet = Typed::typed(PetPayload::class, $_POST);
```

Maping is done throught class constructor.

When the conditions are met then `$pet` will be an instance of `PetPayload` e.g.

```plain
object(PetPayload)#5 (2) {
  ["name":protected] => string(5) "Cicik"
  ["age":protected] => int(3)
}
```

Else `$pet` will be an instance of [ErrorCollection](https://github.com/xtompie/result/blob/master/src/ErrorCollection.php) e.g.

```plain
object(Xtompie\Result\ErrorCollection)#8 (1) {
  ["collection":protected]=> array(2) {
    [0] => object(Xtompie\Result\Error)#10 (3) {
      ["message":protected] => string(24) "Value must not be blank"
      ["key":protected] => string(9) "not_blank"
      ["space":protected] => string(4) "name"
    }
    [1] => object(Xtompie\Result\Error)#12 (3) {
      ["message":protected] => string(37) "Value should be less than or equal 30"
      ["key":protected] => string(3) "max"
      ["space":protected] => string(3) "age"
    }
  }
}
```

Advantages of use typed objects:

- Better static code analysis e.g. phpstan.
- Request payload in one place.

For maping objects `Typed::object()` have more precise type definition:

```php
<?php

Class Typed
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @param mixed $input
     * @return T|ErrorCollection
     */
    public static function object(string $type, mixed $input): object
    {
        // ...
    }
    // ...
}
```

It is better for phpstan.

### Class

```php
<?php

use Xtompie\Typed\NotBlank;
use Xtompie\Typed\Typed;

class Author
{
    public function __construct(
        #[NotBlank]
        protected string $name,
    ) {
    }
}

class Article
{
    public function __construct(
        protected Author $author,
    ) {
    }
}

$article = Typed::typed(Article::class, ['author' => ['name' => 'John']]);
var_dump($article);
```

Output

```plain
object(Article)#4 (1) {
    ["author":protected] => object(Author)#9 (1) {
         ["name":protected] => string(4) "John"
    }
}
```

### ArrayOf

```php
<?php

use Xtompie\Typed\ArrayOf;
use Xtompie\Typed\NotBlank;
use Xtompie\Typed\Typed;

class Comment
{
    public function __construct(
        #[NotBlank]
        protected string $text,
    ) {
    }
}

class Article
{
    public function __construct(
        #[ArrayOf(Comment::class)]
        protected array $comments,
    ) {
    }
}

$article = Typed::typed(Article::class, ['comments' => [['text' => 'A'], ['text' => 'B']]]);
var_dump($article);
/*
object(Article)#6 (1) {
    ["comments":protected] => array(2) {
        [0] => object(Comment)#12 (1) {
            ["text":protected] => string(1) "A"
        }
        [1] => object(Comment)#13 (1) {
            ["text":protected] => string(1) "B"
        }
    }
}
```

### Source

Primitve field name can have characters that can't be used in method property name.
To solve this `Source` can be used.

```php
<?php

use Xtompie\Typed\Source;
use Xtompie\Typed\Typed;

class ArticleQuery
{
    public function __construct(
        #[Source('id:qt')]
        protected int $idGt,
    ) {
    }
}

$query = Typed::typed(ArticleQuery::class, ['id:qt' => 1234]);
var_dump($query);
/*
object(ArticleQuery)#4 (1) {
    ["idGt":protected] => int(1234)
}

```

### Only

To not allow undefined fields `Only` can be used.

```php
<?php

use Xtompie\Typed\Only;
use Xtompie\Typed\Typed;

#[Only]
class Article
{
    public function __construct(
        protected string $title,
        protected string $body,
    ) {
    }
}

$article = Typed::typed(Article::class, ['title' => 'T', 'body' => 'B', 'desc' => 'D']);
var_dump($article);
```

Output

```plain
object(Xtompie\Result\ErrorCollection)#9 (1) {
    ["collection":protected] => array(1) {
        [0]=>object(Xtompie\Result\Error)#8 (3) {
            ["message":protected] => string(17) "Invalid key: desc"
            ["key":protected] => string(4) "only"
            ["space":protected] => NULL
        }
    }
}
```

### Callback

```php
<?php

use Xtompie\Result\ErrorCollection;
use Xtompie\Typed\Callback;
use Xtompie\Typed\NotBlank;
use Xtompie\Typed\Typed;

#[Callback('typed')]
class Password
{
    public function __construct(
        #[NotBlank]
        protected string $new_password,
        protected string $new_password_confirm,
    ) {
    }

    protected function passwordIdentical(): bool
    {
        return $this->new_password === $this->new_password_confirm;
    }

    public function typed(): static|ErrorCollection
    {
        if (!$this->passwordIdentical()) {
            return ErrorCollection::ofErrorMsg('Passwords must be indentical', 'identical', 'new_password_confirm');
        }
        return $this;
    }
}

$password = Typed::typed(Password::class, ['new_password' => '1234', 'new_password_confirm' => '123']);
var_dump($password);
/*
object(Xtompie\Result\ErrorCollection)#7 (1) {
    ["collection":protected] => array(1) {
        [0] => object(Xtompie\Result\Error)#4 (3) {
            ["message":protected] => string(28) "Passwords must be indentical"
            ["key":protected] => string(9) "identical"
            ["space":protected] => string(20) "new_password_confirm"
        }
    }
}
```

### Others

[Alnum](https://github.com/xtompie/typed/blob/master/src/Alnum.php),
[Alpha](https://github.com/xtompie/typed/blob/master/src/Alpha.php),
[ArrayKeyRegex](https://github.com/xtompie/typed/blob/master/src/ArrayKeyRegex.php),
[ArrayKeyString](https://github.com/xtompie/typed/blob/master/src/ArrayKeyString.php),
[ArrayLengthMax](https://github.com/xtompie/typed/blob/master/src/ArrayLengthMax.php),
[ArrayLengthMin](https://github.com/xtompie/typed/blob/master/src/ArrayLengthMin.php),
[ArrayValueLengthMax](https://github.com/xtompie/typed/blob/master/src/ArrayValueLengthMax.php),
[ArrayValueLengthMin](https://github.com/xtompie/typed/blob/master/src/ArrayValueLengthMin.php),
[ArrayValueString](https://github.com/xtompie/typed/blob/master/src/ArrayValueString.php),
[Choice](https://github.com/xtompie/typed/blob/master/src/Choice.php),
[Date](https://github.com/xtompie/typed/blob/master/src/Date.php),
[Digit](https://github.com/xtompie/typed/blob/master/src/Digit.php),
[Email](https://github.com/xtompie/typed/blob/master/src/Email.php),
[LengthMax](https://github.com/xtompie/typed/blob/master/src/LengthMax.php),
[LengthMin](https://github.com/xtompie/typed/blob/master/src/LengthMin.php),
[Max](https://github.com/xtompie/typed/blob/master/src/Max.php),
[Min](https://github.com/xtompie/typed/blob/master/src/Min.php),
[NotBlank](https://github.com/xtompie/typed/blob/master/src/NotBlank.php),
[Regex](https://github.com/xtompie/typed/blob/master/src/Regex.php),
[Replace](https://github.com/xtompie/typed/blob/master/src/Replace.php),
[ToBool](https://github.com/xtompie/typed/blob/master/src/ToBool.php),
[ToInt](https://github.com/xtompie/typed/blob/master/src/ToInt.php),
[ToString](https://github.com/xtompie/typed/blob/master/src/ToString.php),
[Trim](https://github.com/xtompie/typed/blob/master/src/Trim.php),
[TrimLeft](https://github.com/xtompie/typed/blob/master/src/TrimLeft.php),
[TrimRight](https://github.com/xtompie/typed/blob/master/src/TrimRight.php),

### Limitations

Object property must have type.
Type can't be and union or intersection.
If in primitive incoming data can have many types then use mixed property.
Then some kind of `To*` assert can be used or Callback.
