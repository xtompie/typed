# Typed - Primitive as Typed Object

Library that maps pritmitve types into typed objects.
Can be used to maps request/input into objects of defined classes.
Gives `\Xtompie\Result\ErrorColleciton` on fail.

## Requiments

PHP >= 8.0

## Installation

Using [composer](https://getcomposer.org/)

```
composer require xtompie/typed
```

## Docs

```php
use Xtompie\Typed\ArrayOf;
use Xtompie\Typed\Compose;
use Xtompie\Typed\Only;
use Xtompie\Typed\Typed;

#[Only]
class Author
{
    public function __construct(
        protected string $name,
    ) {
    }
}

#[Only]
class Category
{
    public function __construct(
        protected string $title,
    ) {
    }
}

#[Only]
#[Compose('compose')]
Class Article
{
    public function __construct(
        protected string $title,
        protected Author $author,
        #[ArrayOf(Category::class)]
        protected array $categories,
    ) {
    }

    public function compose(): static|ErrorCollection
    {
        $this->title = trim($this->title);
        if (strlen($this->title) === 0) {
            return ErrorCollection::of('Title required', 'required', 'title');
        }
        return $this;
    }
}

$article = Typed::object(Article::class, [
    'title' => 'A1',
    'author' => ['name' => 'John Doe'],
    'categories' => [
        ['title' => 'C1'],
        ['title' => 'C2'],
    ],
]);

if ($article instanceof ErrorCollection) {
    var_dump($article); // => errors
    exit;
}

var_dump(get_class($article)); // => Article
```

Maping is done throught class constructor.

Asserts:

`Only` - Primitive data cant have more fields then class.
`Compose` - Run additional `callback(): static|ErrorCollection`
`ArrayOf` - Maps each element of array into typed object.
