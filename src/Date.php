<?php

declare(strict_types=1);

namespace Xtompie\Typed;

use Attribute;
use DateTime;
use Xtompie\Result\ErrorCollection;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Date implements Assert
{
    public function __construct(
        protected string $format,
        protected ?string $msg = null,
        protected string $key = 'date',
    ) {
    }

    public function assert(mixed $input, string $type): mixed
    {
        $t = DateTime::createFromFormat($this->format, (string)$input);
        if ($t === false ||  $t->format($this->format) !== $input) {
            $hr = [
                'Y' => 'YYYY',
                'm' => 'MM',
                'd' => 'DD',
                'H' => 'HH',
                'i' => 'MM',
                's' => 'SS',
            ];
            $formatHr = str_replace(array_keys($hr), array_values($hr), $this->format);
            return ErrorCollection::ofErrorMsg(
                message: $this->msg ?: "Value must be a valid date in $formatHr format",
                key:'date'
            );
        }

        return $input;
    }

}
