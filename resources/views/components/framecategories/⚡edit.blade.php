<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\FrameCategories;
use App\Livewire\Forms\FrameCategoriesForm;


new class extends Component
{
    public FrameCategoriesForm $form;

    #[On('edit-framecategories')]
    public function editFrameCategories($id){
        $framecategories = FrameCategories::find($id);
        $this->form->setFrameCategories($framecategories);
        Flux::modal('edit-framecategories')->show();

    }

    public function updateFrameCategories() {
        $this->form->update();
        Flux::modal('edit-framecategories')->close();
        session()->flash('success', 'Frame category updated successfully');
        $this->redirectRoute('framecategories.index', navigate: true);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->form->reset();
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $framecategories = FrameCategories::find($id);
        $this->form->setFrameCategories($framecategories);
        Flux::modal('delete-framecategories')->show();
    }

    public function deleteFrameCategories() {
        $this->form->framecategories->delete();
        Flux::modal('delete-framecategories')->close();
        session()->flash('success', 'Frame categories deleted successfully');
        $this->redirectRoute('framecategories.index', navigate: true);
    }
};
?>

<div>
    <flux:modal 
        name="edit-framecategories" 
        class="md:w-150" 
        x-on:close="$wire.resetForm()" 
    >
        <form class="space-y-8" wire:submit.prevent="updateFrameCategories">
            {{-- header --}}
            <div class="space-y-2">
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                    Edit Categories
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400">
                    Edit your categories details below
                </flux:text>
            </div>

            {{-- form field --}}
            <div class="space-y-6">
                <flux:input
                    label="Name"
                    placeholder="Enter category name"
                    wire:model="form.name"
                    wire:dirty.class.text-red-500
                />

                <flux:textarea
                    label="Description"
                    placeholder="Enter category description"
                    wire:model="form.description"
                    wire:dirty.class.text-red-500
                />
            </div>

            <div 
                wire:show ="$dirty"
                class="text-red-500 dark:text-red-400"
            >
                you have unsaved changes
            </div>
    
            {{-- footer --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="primary" type="submit">Update</flux:button>
            </div>
                

        </form>
    </flux:modal>

    {{-- delete modal --}}

    <flux:modal 
        name="delete-framecategories" 
        class="md:w-150" 
        x-on:close="$wire.resetForm()" 
    >
        <form class="space-y-8" wire:submit.prevent="deleteFrameCategories">
            {{-- header --}}
            <div class="space-y-2">
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">
                    Delete Categories
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400">
                    this action cannot be undone
                </flux:text>
            </div>

            {{-- footer --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="outline" color="neutral">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" color="danger" type="submit">Delete</flux:button>
            </div>
                

        </form>
    </flux:modal>

</div>