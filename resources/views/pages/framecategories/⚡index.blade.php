<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\FrameCategories;

new class extends Component
{
    use WithPagination;
    #[Computed]
    public function FrameCategories()
    {
        return FrameCategories::latest()->paginate(10);
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Frame Categories</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Manage your frame categories</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-framecategory">
        <flux:button variant="primary" icon="plus" color="primary">Add Frame Category</flux:button>
    </flux:modal.trigger>

    <div class="overflow-x-auto">
       <flux:table :paginate="$this->FrameCategories">
            <flux:table.columns>
                <flux:table.column >No</flux:table.column>
                <flux:table.column >Name</flux:table.column>
                <flux:table.column>Description</flux:table.column>
                <flux:table.column >Created At</flux:table.column>
                <flux:table.column >Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->FrameCategories as $framecategories)
                    <flux:table.row :key="$framecategories->id">

                        <flux:table.cell>
                            {{ $loop->iteration + $this->FrameCategories->firstItem() - 1}}
                        </flux:table.cell>

                        <flux:table.cell class="flex items-center gap-3">
                            {{ $framecategories->name }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $framecategories->description ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">{{ $framecategories->created_at->diffForHumans() }}</flux:table.cell>

                        <flux:table.cell>


                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $framecategories->id }})">Edit</flux:menu.item>

                                    <flux:menu.separator />

                                    {{-- <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', id: $framecategory->id)">Delete</flux:menu.item> --}}
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