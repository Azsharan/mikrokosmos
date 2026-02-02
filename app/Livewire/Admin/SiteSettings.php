<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Datatable
{
    protected ?string $recordName = 'Site Setting';

    protected function title(): string
    {
        return __('Site Settings');
    }

    protected function description(): ?string
    {
        return __('Configure global storefront links and preferences');
    }

    protected function query(): Builder
    {
        return SiteSetting::query()->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Instagram URL'),
                'field' => 'instagram_url',
                'type' => 'text',
                'default' => __('No configurado'),
            ],
            [
                'label' => __('Instagram enabled'),
                'type' => 'badge',
                'field' => 'instagram_enabled',
                'options' => [
                    true => ['label' => __('Enabled'), 'class' => 'bg-emerald-100 text-emerald-700'],
                    false => ['label' => __('Disabled'), 'class' => 'bg-rose-100 text-rose-700'],
                ],
            ],
        ];
    }

    protected function formFields(): array
    {
        return [
            'instagram_url' => [
                'type' => 'text',
                'label' => __('Instagram URL'),
                'placeholder' => 'https://instagram.com/your-account',
            ],
            'instagram_enabled' => [
                'type' => 'checkbox',
                'label' => __('Show Instagram link'),
                'default' => true,
            ],
        ];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [
            'formData.instagram_url' => ['nullable', 'url', 'max:255'],
            'formData.instagram_enabled' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.instagram_url' => __('Instagram URL'),
            'formData.instagram_enabled' => __('Instagram enabled'),
        ];
    }

    protected function createRecord(array $data): Model
    {
        $record = parent::createRecord($data);
        cache()->forget('site_settings_active');

        return $record;
    }

    protected function updateRecord(Model $record, array $data): Model
    {
        $updated = parent::updateRecord($record, $data);
        cache()->forget('site_settings_active');

        return $updated;
    }

    protected function deleteRecordInstance(Model $record): void
    {
        parent::deleteRecordInstance($record);
        cache()->forget('site_settings_active');
    }
}
