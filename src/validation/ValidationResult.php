<?php

namespace ButA2SaeS3\validation;

class ValidationResult
{
    private ?array $validationMessages;
    private mixed $value;

    private function __construct()
    {
        $this->validationMessages = array();
        $this->value = null;
    }

    public function isValid(): bool
    {
        return empty($this->validationMessages) && !empty($this->value);
    }

    public function hasMessages(): bool
    {
        return !empty($this->validationMessages);
    }

    public function messages(): array
    {
        return $this->validationMessages;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function setMessages(array $validationMessages): array
    {
        return $this->validationMessages = $validationMessages;
    }

    public static function fail(array $validationMessages)
    {
        $res = new ValidationResult();
        $res->value = null;
        $res->setMessages($validationMessages);
        return $res;
    }

    public static function ok(mixed $value)
    {
        $res = new ValidationResult();
        $res->value = $value;
        return $res;
    }

    public static function empty()
    {
        return new ValidationResult();
    }

    public function addMessage(string $key, string $value)
    {
        $this->validationMessages[$key] = $value;
    }
}
