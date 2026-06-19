<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\PhotoFrames;
new class extends Component
{
     use WithPagination;
    #[Computed]
    public function PhotoFrames()

    {
        return PhotoFrames::latest()->paginate(1);
    }   
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Photo Frames</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Manage Your Photo Frames</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-photoframe">
        <flux:button variant="primary" icon="plus" color="primary">Add Photo Frame</flux:button>
    </flux:modal.trigger>

    <div class="overflow-x-auto">
       <flux:table :paginate="$this->PhotoFrames">
            <flux:table.columns>
                <flux:table.column>Nama Frame</flux:table.column>
                <flux:table.column>Tema</flux:table.column>
                <flux:table.column>Category_id</flux:table.column>
                <flux:table.column>Gambar Frame</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->PhotoFrames as $photoframe)
                    <flux:table.row :key="$photoframe->id">
                        
                        <flux:table.cell class="flex items-center gap-3">
                            {{ $photoframe->nama_frame }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $photoframe->tema ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $photoframe->category_id ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $photoframe->gambar_frame ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $photoframe->actions ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">{{ $photoframe->created_at->diffForHumans() }}</flux:table.cell>

                        <flux:table.cell>


                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $photoframe->id }})">Edit</flux:menu.item>

                                    <flux:menu.separator />

                                    {{-- <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', id: $photoframe->id)">Delete</flux:menu.item> --}}
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', {id: {{ $photoframe->id }}})">Delete</flux:menu.item>
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