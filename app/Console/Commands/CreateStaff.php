<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateStaff extends Command
{
    protected $signature = 'kennisbank:create-staff';

    protected $description = 'Create an IT staff account for the Kennisbank panel';

    public function handle(): int
    {
        $data = [
            'name' => trim((string) $this->ask('Staff member name')),
            'email' => strtolower(trim((string) $this->ask('Work email address'))),
            'password' => $this->secret('Password (at least 12 characters)'),
            'password_confirmation' => $this->secret('Confirm password'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'bail',
                'required',
                'string',
                'confirmed',
                Password::min(12),
                function (string $attribute, string $value, \Closure $fail): void {
                    if (strlen($value) > 72) {
                        $fail('The password must be at most 72 bytes.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User($validator->safe()->only(['name', 'email', 'password']));
        $user->is_it_staff = true;
        $user->save();

        $this->info('IT staff account created.');

        return self::SUCCESS;
    }
}
