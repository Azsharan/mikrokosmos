<?php

namespace App\Livewire\Admin;

use App\Models\ShopUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ShopUsers extends Datatable
{
    protected ?string $recordName = 'Shop User';

    protected function title(): string
    {
        return __('Shop Users');
    }

    protected function description(): ?string
    {
        return __('Manage customers that can log into the shop');
    }

    protected function query(): Builder
    {
        return ShopUser::query()->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Customer'),
                'priority' => 1,
                'format' => fn (ShopUser $shopUser) => sprintf(
                    '<div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                        <p class="text-xs text-neutral-500">%s</p>
                    </div>',
                    e($shopUser->name),
                    e($shopUser->address ?? '')
                ),
                'html' => true,
            ],
            [
                'label' => __('Email'),
                'type' => 'text',
                'field' => 'email',
                'priority' => 1,
            ],
            [
                'label' => __('Phone'),
                'type' => 'text',
                'field' => 'phone',
                'default' => __('Not specified'),
                'priority' => 2,
            ],
            [
                'label' => __('Status'),
                'type' => 'checkbox',
                'field' => 'is_active',
                'checked_label' => __('Active'),
                'unchecked_label' => __('Inactive'),
                'priority' => 1,
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
                'placeholder' => __('Customer name'),
            ],
            'email' => [
                'type' => 'email',
                'label' => __('Email'),
                'placeholder' => 'customer@example.com',
            ],
            'password' => [
                'type' => 'password',
                'label' => __('Password'),
                'placeholder' => '••••••••',
            ],
            'phone' => [
                'type' => 'text',
                'label' => __('Phone'),
            ],
            'address' => [
                'type' => 'textarea',
                'label' => __('Address'),
                'default' => '',
            ],
            'is_active' => [
                'type' => 'checkbox',
                'label' => __('Active'),
                'default' => true,
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
                Rule::unique('shop_users', 'email')->ignore($recordId),
            ],
            'formData.password' => [
                $recordId ? 'nullable' : 'required',
                'string',
                'min:6',
            ],
            'formData.phone' => ['nullable', 'string', 'max:255'],
            'formData.address' => ['nullable', 'string'],
            'formData.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.name' => __('Name'),
            'formData.email' => __('Email'),
            'formData.password' => __('Password'),
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
