<x-layouts::app.sidebar :title="$title ?? null">
    
    <div class="bg-grid-neon"></div>
    <div class="neon-stars"></div>

    <flux:main>
        {{ $slot }}
    </flux:main>
    
</x-layouts::app.sidebar>
