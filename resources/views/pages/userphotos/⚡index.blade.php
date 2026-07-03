<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\UserPhotos;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function UserPhotos()
    {
        return UserPhotos::with('user')->latest()->paginate(10);
    }

    #[On('confirm-delete')]
    public function delete($id)
    {
        $photo = UserPhotos::find($id);
        if ($photo) {
            if (Storage::disk('public')->exists($photo->file_photo)) {
                Storage::disk('public')->delete($photo->file_photo);
            }
            $photo->delete();
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">User Photos</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Your Gallery foto here.</flux:subheading>
    <flux:separator variant="subtle" />

    <div class="overflow-x-auto mt-4">
       <flux:table :paginate="$this->UserPhotos">
            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Photo</flux:table.column>
                <flux:table.column>Upload Time</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->UserPhotos as $userphoto)
                    <flux:table.row :key="$userphoto->id">

                         <flux:table.cell>
                            {{ $loop->iteration + $this->UserPhotos->firstItem() - 1}}
                        </flux:table.cell>
                        
                        <flux:table.cell class="flex items-center gap-3">
                            {{ $userphoto->user->name ?? 'Unknown User' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            @if($userphoto->file_photo)
                                <img src="{{ asset('storage/' . $userphoto->file_photo) }}" alt="Photo" class="w-16 h-16 object-cover rounded shadow-sm border border-gray-200">
                            @else
                                -
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $userphoto->upload_time ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">
                            {{ $userphoto->created_at->diffForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $userphoto->id }} })" wire:confirm="Are you sure you want to delete this photo?">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>