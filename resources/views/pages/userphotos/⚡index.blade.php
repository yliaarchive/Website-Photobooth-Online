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
        $query = UserPhotos::with('user')->latest();

        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        return $query->paginate(10);
    }

    #[On('confirm-delete')]
    public function delete($id)
    {
        $photo = UserPhotos::find($id);
        
        if ($photo) {
            if (auth()->user()->role !== 'admin' && $photo->user_id !== auth()->id()) {
                abort(403, 'Anda tidak memiliki izin untuk menghapus foto ini.');
            }

            if ($photo->file_photo && Storage::disk('public')->exists($photo->file_photo)) {
                Storage::disk('public')->delete($photo->file_photo);
            }
            $photo->delete();
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-8 p-4 md:p-6">
    
    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 md:p-8 shadow-sm border border-pink-100 dark:border-zinc-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-rose-400">
                📸 User Photos
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 font-medium text-lg">
                Gallery of beautiful moments captured here ✨
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-pink-50 dark:border-zinc-800 overflow-hidden p-2">
        <div class="overflow-x-auto">
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
                        <flux:table.row :key="$userphoto->id" class="hover:bg-pink-50/50 dark:hover:bg-zinc-800/50 transition-colors duration-200">

                            <flux:table.cell class="text-zinc-400 font-medium">
                                {{ $loop->iteration + $this->UserPhotos->firstItem() - 1}}
                            </flux:table.cell>
                            
                            <flux:table.cell class="font-bold text-zinc-800 dark:text-zinc-200 text-base">
                                {{ $userphoto->user->name ?? 'Unknown User' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($userphoto->file_photo)
                                    <div class="bg-slate-50 dark:bg-zinc-800 p-1.5 rounded-xl inline-block border border-slate-200 dark:border-zinc-700 shadow-sm relative group overflow-hidden">
                                        <img src="{{ asset('storage/' . $userphoto->file_photo) }}" alt="Photo" 
                                             class="w-16 h-16 object-cover rounded-lg transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                @else
                                    <span class="text-zinc-400 italic text-sm">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="text-zinc-500 dark:text-zinc-400 text-sm font-medium">
                                @if($userphoto->upload_time)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-600 dark:bg-zinc-800 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700">
                                        {{ $userphoto->upload_time }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 italic text-sm">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap text-zinc-500 text-sm">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $userphoto->created_at->diffForHumans() }}
                                </span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" class="hover:bg-pink-100 dark:hover:bg-zinc-700 rounded-full"></flux:button>

                                    <flux:menu>
                                        <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $userphoto->id }} })" wire:confirm="Are you sure you want to delete this cute photo?">
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
</div>