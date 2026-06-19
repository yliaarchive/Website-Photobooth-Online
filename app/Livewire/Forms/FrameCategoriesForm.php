<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\FrameCategories;
use Illuminate\Validation\Rule;

class FrameCategoriesForm extends Form
{
    public string $name = '';
    public string $description = '';
    public ?FrameCategories $framecategories = null;

      public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('frame_categories', 'name')->ignore($this->framecategories?->id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function store()
    {
        $this->validate();
        FrameCategories::create($this->only(['name', 'description']));
        $this->reset();
    }

    public function setFrameCategories(FrameCategories $framecategories): void
    {
        $this->framecategories = $framecategories;
        $this->name = $framecategories->name;
        $this->description = $framecategories->description ?? '';
    }

    public function update()
    {
        $this->validate();
        $this->framecategories->update($this->only(['name', 'description']));
    }
}
