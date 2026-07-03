<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PhotoFrames;
use App\Models\FrameCategories;
use App\Livewire\Forms\PhotoFrameForm;
use Illuminate\Support\Facades\Storage;
use Flux\Flux;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public PhotoFrameForm $form;
    public bool $isEdit = false;

    #[Computed]
    public function PhotoFrames()
    {
        return PhotoFrames::with('frameCategories')->latest()->paginate(10);
    }

    #[Computed]
    public function Categories()
    {
        return FrameCategories::all();
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

        Flux::modal('create-photoframe')->close();
    }

    public function edit(PhotoFrames $photoframe)
    {
        $this->isEdit = true;
        $this->form->setPhotoFrame($photoframe);
        Flux::modal('create-photoframe')->show();
    }

    #[On('confirm-delete')]
    public function delete($id)
    {
        $frame = PhotoFrames::find($id);
        if ($frame) {
            if ($frame->gambar_frame && Storage::disk('public')->exists($frame->gambar_frame)) {
                Storage::disk('public')->delete($frame->gambar_frame);
            }
            $frame->delete();
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Photo Frames</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Manage Your Photo Frames (Must be PNG)</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-photoframe">
        <flux:button variant="primary" icon="plus" color="primary" wire:click="create">Add Photo Frame</flux:button>
    </flux:modal.trigger>

    <div class="overflow-x-auto">
       <flux:table :paginate="$this->PhotoFrames">
            <flux:table.columns>
                <flux:table.column>Nama Frame</flux:table.column>
                <flux:table.column>Tema</flux:table.column>
                <flux:table.column>Kategori</flux:table.column>
                <flux:table.column>Gambar Frame</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->PhotoFrames as $photoframe)
                    <flux:table.row :key="$photoframe->id">
                        
                        <flux:table.cell class="flex items-center gap-3 font-medium">
                            {{ $photoframe->nama_frame }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $photoframe->tema ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $photoframe->frameCategories->name ?? $photoframe->frameCategories->nama_kategori ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-800 rounded">
                            @if($photoframe->gambar_frame)
                                <img src="{{ asset('storage/' . $photoframe->gambar_frame) }}" alt="Frame" class="h-16 w-auto object-contain">
                            @else
                                No Image
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">{{ $photoframe->created_at->diffForHumans() }}</flux:table.cell>

                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $photoframe->id }})">Edit</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $photoframe->id }} })" wire:confirm="Delete this frame?">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
   
    <flux:modal name="create-photoframe" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $isEdit ? 'Edit Photo Frame' : 'Add Photo Frame' }}</flux:heading>
                <flux:subheading class="mt-2">Manage your Photo Frame (PNG format only).</flux:subheading>
            </div>

            <flux:input wire:model="form.nama_frame" label="Nama Frame" placeholder="Contoh: Summer " />
            <flux:input wire:model="form.tema" label="Tema (Opsional)" placeholder="Contoh: Holiday Addict" />
            
            <flux:select wire:model="form.category_id" label="Kategori Frame">
                <option value="">Pilih Kategori</option>
                @foreach($this->Categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name ?? $category->nama_kategori }}</option>
                @endforeach
            </flux:select>

            <flux:input type="file" wire:model="form.gambar_frame" label="Upload Template Frame (.png)" accept="image/png" />
            <div wire:loading wire:target="form.gambar_frame" class="text-sm text-blue-500">Uploading...</div>

            @if ($form->gambar_frame && !is_string($form->gambar_frame))
                <div class="p-2 bg-gray-200 rounded border">
                    <img src="{{ $form->gambar_frame->temporaryUrl() }}" class="w-full h-auto object-contain">
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:modal>
</div>