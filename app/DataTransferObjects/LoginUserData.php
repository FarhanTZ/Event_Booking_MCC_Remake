<?php

namespace App\DataTransferObjects;

use App\Http\Requests\Auth\LoginRequest;

readonly class LoginUserData
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
        public ?string $redirectSlot = null,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: strtolower(trim($request->validated('email'))),
            password: $request->validated('password'),
            remember: $request->boolean('remember'),
            redirectSlot: $request->filled('redirect_slot') ? $request->input('redirect_slot') : null,
        );
    }
}
