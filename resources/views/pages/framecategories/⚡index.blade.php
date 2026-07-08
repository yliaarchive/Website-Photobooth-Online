<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\FrameCategories;
use App\Livewire\Forms\FrameCategoriesForm;

new class extends Component
{
    use WithPagination;
    
    #[Computed]
    public function FrameCategories()
    {
        return FrameCategories::latest()->paginate(10);
    }

     public function edit($id){
        $this->dispatch('edit-framecategories', id: $id);
    }
};
?>

<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-100 p-4 sm:p-8 -m-4 sm:-m-8 rounded-xl">
    
    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <flux:heading size="xl" class="font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-rose-500">
                    Frame Categories
                </flux:heading>
                <flux:subheading size="lg" class="text-pink-600/70">
                    Manage your frame categories
                </flux:subheading>
            </div>

            <flux:modal.trigger name="create-framecategories">
                <flux:button icon="plus" class="bg-gradient-to-r from-pink-500 to-rose-400 hover:from-pink-600 hover:to-rose-500 text-white border-none shadow-md shadow-pink-200 transition-all">
                    Add Frame Category
                </flux:button>
            </flux:modal.trigger>
        </div>

        <flux:separator variant="subtle" class="bg-pink-100" />

        <livewire:framecategories.create />
        <livewire:framecategories.edit />
        <x-flash-message />

        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-pink-100 p-2 sm:p-4">
            <div class="overflow-x-auto">
                <flux:table :paginate="$this->FrameCategories">
                    <flux:table.columns>
                        <flux:table.column>No</flux:table.column>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Description</flux:table.column>
                        <flux:table.column>Created At</flux:table.column>
                        <flux:table.column>Action</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->FrameCategories as $framecategories)
                            <flux:table.row :key="$framecategories->id" class="hover:bg-pink-50/50 transition-colors">
                                
                                <flux:table.cell class="text-zinc-500">
                                    {{ $loop->iteration + $this->FrameCategories->firstItem() - 1}}
                                </flux:table.cell>

                                <flux:table.cell class="flex items-center gap-3 font-medium text-zinc-800">
                                    {{ $framecategories->name }}
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                                    {{ $framecategories->description ?? '-' }}
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap text-zinc-500">
                                    {{ $framecategories->created_at->diffForHumans() }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" class="hover:bg-pink-100 hover:text-pink-600"></flux:button>

                                        <flux:menu>
                                            <flux:menu.item icon="pencil" wire:click="edit({{ $framecategories->id }})" class="hover:bg-pink-50 hover:text-pink-600">Edit</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', {id: {{ $framecategories->id }}})">Delete</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:table.cell>
                                
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>

    </div>
</div>