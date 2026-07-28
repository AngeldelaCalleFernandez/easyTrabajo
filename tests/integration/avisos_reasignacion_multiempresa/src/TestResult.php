<?php

declare(strict_types=1);

final class TestResult
{
    private string $id;
    private bool $passed;
    private string $message;

    public function __construct(string $id, bool $passed, string $message)
    {
        $this->id = $id;
        $this->passed = $passed;
        $this->message = $message;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function passed(): bool
    {
        return $this->passed;
    }

    public function message(): string
    {
        return $this->message;
    }
}
