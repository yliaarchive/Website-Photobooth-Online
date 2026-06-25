<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\UserPhotos;
use App\Livewire\Forms\UserPhotosForm;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public UserPhotosForm $form;
    public bool $isEdit = false;

    #[Computed]
    public function UserPhotos()
    {
        return UserPhotos::with('user')->latest()->paginate(10);
    }

    public function create()
    {
        $this->isEdit = false;
        $this->form->reset();
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        Flux::modal('user-photos-modal')->close();
    }

    public function edit(UserPhotos $userPhoto)
    {
        $this->isEdit = true;
        $this->form->setUserPhoto($userPhoto);
        Flux::modal('user-photos-modal')->show();
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
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Upload your photos here</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="user-photos-modal">
        <flux:button variant="primary" icon="plus" color="primary" wire:click="create">Add your photos</flux:button>
    </flux:modal.trigger>

    <div class="overflow-x-auto">
       <flux:table :paginate="$this->UserPhotos">
            <flux:table.columns>
                <flux:table.column>No</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Photo</flux:table.column>
                <flux:table.column>Upload Time</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column></flux:table.column>
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
                                <img src="{{ asset('storage/' . $userphoto->file_photo) }}" alt="Photo" class="w-16 h-16 object-cover rounded shadow-sm">
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
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $userphoto->id }})">Edit</flux:menu.item>

                                    <flux:menu.separator />

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

    <flux:modal name="user-photos-modal" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $isEdit ? 'Update Photo' : 'Upload New Photo' }}</flux:heading>
                <flux:subheading>Choose a picture from your device.</flux:subheading>
            </div>

            <flux:input type="file" wire:model="form.file_photo" label="Photo File" />

            <div wire:loading wire:target="form.file_photo" class="text-sm text-blue-500">Uploading...</div>

            @if ($form->file_photo && !is_string($form->file_photo))
                <img src="{{ $form->file_photo->temporaryUrl() }}" class="w-full h-48 object-cover rounded-lg border">
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>