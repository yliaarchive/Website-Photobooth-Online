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

<div class="max-w-7xl mx-auto space-y-8 p-4 md:p-6">
    
    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 md:p-8 shadow-sm border border-pink-100 dark:border-zinc-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-rose-400">
                🏷️ Frame Categories
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 font-medium text-lg">
                Manage your frame categories ✨
            </p>
        </div>

        <flux:modal.trigger name="create-framecategories">
            <flux:button 
                icon="plus" 
                class="rounded-full bg-gradient-to-r from-pink-400 to-fuchsia-400 hover:from-pink-500 hover:to-fuchsia-500 text-white shadow-lg shadow-pink-200 dark:shadow-none px-8 py-3 transition-all duration-300 hover:scale-105 font-bold border-none">
                Add Frame Category
            </flux:button>
        </flux:modal.trigger>
    </div>

    <livewire:framecategories.create />
    <livewire:framecategories.edit />
    <x-flash-message />

    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-pink-50 dark:border-zinc-800 overflow-hidden p-2">
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
                        <flux:table.row :key="$framecategories->id" class="hover:bg-pink-50/50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                            
                            <flux:table.cell class="text-zinc-400 font-medium">
                                {{ $loop->iteration + $this->FrameCategories->firstItem() - 1}}
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-gradient-to-r from-pink-100 to-rose-100 text-pink-700 dark:from-pink-900/40 dark:to-rose-900/40 dark:text-pink-300 border border-pink-200/60 dark:border-pink-800 shadow-sm">
                                    {{ $framecategories->name }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($framecategories->description)
                                    <div class="inline-block px-4 py-2 bg-slate-50/80 dark:bg-zinc-800/80 rounded-xl border border-slate-100 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 text-sm max-w-xs truncate hover:whitespace-normal hover:break-words transition-all duration-300">
                                        {{ $framecategories->description }}
                                    </div>
                                @else
                                    <span class="text-zinc-400 italic text-sm px-2">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap text-zinc-500 text-sm">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $framecategories->created_at->diffForHumans() }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" class="hover:bg-pink-100 dark:hover:bg-zinc-700 rounded-full"></flux:button>

                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:click="edit({{ $framecategories->id }})">Edit</flux:menu.item>
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