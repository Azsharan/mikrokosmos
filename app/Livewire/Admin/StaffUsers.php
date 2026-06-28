<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffUsers extends Datatable
{
    protected ?string $recordName = 'Staff User';

    protected function title(): string
    {
        return __('Staff Users');
    }

    protected function description(): ?string
    {
        return __('Manage staff accounts that can access the admin dashboard');
    }

    protected function query(): Builder
    {
        return User::query()->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Staff Member'),
                'priority' => 1,
                'format' => fn (User $user) => sprintf(
                    '<div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                        <p class="text-xs text-neutral-500">%s</p>
                    </div>',
                    e($user->name),
                    e($user->email)
                ),
                'html' => true,
            ],
            [
                'label' => __('Role'),
                'field' => 'role',
                'format' => fn (User $user) => $user->role === UserRole::SuperAdmin
                    ? '<span class="font-semibold text-amber-600 dark:text-amber-400">' . __('Super Admin') . '</span>'
                    : '<span class="text-neutral-500">' . __('Staff') . '</span>',
                'html' => true,
                'priority' => 1,
            ],
            [
                'label' => __('Email Verified'),
                'type' => 'checkbox',
                'field' => 'email_verified_at',
                'checked_label' => __('Verified'),
                'unchecked_label' => __('Pending'),
                'priority' => 1,
            ],
            [
                'label' => __('Two Factor'),
                'type' => 'checkbox',
                'field' => 'two_factor_confirmed_at',
                'checked_label' => __('Enabled'),
                'unchecked_label' => __('Disabled'),
                'priority' => 2,
            ],
            [
                'label' => __('Created'),
                'type' => 'datetime',
                'field' => 'created_at',
                'format_string' => 'Y-m-d H:i',
                'align' => 'right',
                'priority' => 3,
            ],
        ];
    }

    protected function formFields(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => __('Name'),
                'placeholder' => __('Staff name'),
            ],
            'email' => [
                'type' => 'email',
                'label' => __('Email'),
                'placeholder' => 'admin@example.com',
            ],
            'password' => [
                'type' => 'password',
                'label' => __('Password'),
                'placeholder' => '••••••••',
                'default' => '',
            ],
            'role' => [
                'type' => 'select',
                'label' => __('Role'),
                'options' => [
                    UserRole::Staff->value => __('Staff'),
                    UserRole::SuperAdmin->value => __('Super Admin'),
                ],
                'default' => UserRole::Staff->value,
            ],
        ];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($recordId),
            ],
            'formData.password' => [
                $recordId ? 'nullable' : 'required',
                'string',
                'min:6',
            ],
            'formData.role' => ['required', Rule::enum(UserRole::class)],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.name' => __('Name'),
            'formData.email' => __('Email'),
            'formData.password' => __('Password'),
            'formData.role' => __('Role'),
        ];
    }

    protected function createRecord(array $data): Model
    {
        $record = $this->newModelInstance();
        $record->fill($this->prepareData($data));
        $record->save();

        return $record;
    }

    protected function updateRecord(Model $record, array $data): Model
    {
        $record->fill($this->prepareData($data, $record));
        $record->save();

        return $record;
    }

    protected function prepareData(array $data, ?Model $record = null): array
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif ($record) {
            unset($data['password']);
        }

        return $data;
    }
}
