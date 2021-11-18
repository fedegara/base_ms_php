<?php

namespace App\Domain\Inheritances;

use Cratia\Rest\Actions\ActionError;

class I18nActionError extends ActionError
{
    private $i18n_error;

    public function __construct(string $i18n_error, int $code, string $type, ?string $description)
    {
        parent::__construct($code, $type, $description);
        $this->i18n_error = $i18n_error;
    }

    public function jsonSerialize()
    {
        $return = parent::jsonSerialize();
        $return['i18n'] = $this->i18n_error;
        return $return;
    }
}
