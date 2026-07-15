@extends('layouts.frontend')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

<div class="max-w-5xl mx-auto py-24 px-6">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-black text-gray-900 mb-2">📸 Studio Photobox</h1>
        <p class="text-gray-500">Upload foto kamu, sesuaikan posisi, dan klik potong jika perlu.</p>
    </div>
    
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-pink-100 flex flex-col items-center relative">
        
        <div class="absolute top-8 right-8 z-10">
            <button id="cropBtn" onclick="openCropModal()" class="hidden bg-blue-500 text-white px-5 py-2 rounded-full font-bold hover:bg-blue-600 transition shadow-lg flex items-center gap-2">
                ✂️ Potong Foto
            </button>
        </div>

        <div id="canvas-container" class="flex justify-center border-2 border-dashed border-pink-200 p-4 rounded-2xl bg-gray-50 relative">
             <canvas id="photoboxCanvas" width="350" height="500"></canvas>
        </div>
        
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <input type="file" id="photoInput" class="hidden" accept="image/*">
            <label for="photoInput" class="cursor-pointer bg-pink-100 text-pink-600 px-8 py-3 rounded-full font-bold hover:bg-pink-200 transition shadow-sm">
                1. Upload Foto
            </label>
            
            <button onclick="generateResult()" class="bg-pink-500 text-white px-8 py-3 rounded-full font-bold hover:bg-pink-600 transition shadow-lg">
                2. Selesai & Simpan
            </button>
        </div>
    </div>
</div>

<div id="cropStage" class="fixed inset-0 bg-black/90 z-[100] hidden flex-col items-center justify-center p-6 backdrop-blur-sm">
    <h3 class="text-white text-2xl font-bold mb-4">Sesuaikan Foto</h3>
    <div class="w-full max-w-2xl h-[60vh] bg-white rounded-2xl overflow-hidden p-2">
        <img id="cropImage" src="" alt="Crop" class="max-w-full max-h-full block">
    </div>
    <div class="flex gap-4 mt-6">
        <button onclick="closeCropModal()" class="bg-gray-500 text-white px-8 py-3 rounded-full font-bold hover:bg-gray-600 transition">Batal</button>
        <button onclick="applyCrop()" class="bg-pink-500 text-white px-8 py-3 rounded-full font-bold hover:bg-pink-600 transition shadow-lg">✂️ Terapkan Crop</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    // --- 1. INISIALISASI CANVAS ---
    const canvas = new fabric.Canvas('photoboxCanvas', {
        backgroundColor: '#ffffff'
    });
    
    // Load Frame
    const frameUrl = "{{ asset('storage/' . $frame->gambar_frame) }}";
    fabric.Image.fromURL(frameUrl, function(img) {
        let scale = Math.min(canvas.width / img.width, canvas.height / img.height);
        img.scale(scale);
        canvas.setOverlayImage(img, canvas.renderAll.bind(canvas), {
            originX: 'center', originY: 'center',
            left: canvas.width / 2, top: canvas.height / 2
        });
    }, { crossOrigin: 'anonymous' });

    // --- 2. LOGIKA UPLOAD FOTO ---
    document.getElementById('photoInput').addEventListener('change', function(e) {
        let file = e.target.files[0];
        if (!file) return;

        let reader = new FileReader();
        reader.onload = function(f) {
            let data = f.target.result;
            fabric.Image.fromURL(data, function(img) {
                img.scaleToWidth(200);
                img.set({ left: 75, top: 100 });
                canvas.add(img);
                canvas.sendToBack(img); 
                canvas.setActiveObject(img);
            });
        };
        reader.readAsDataURL(file);
        
        // Reset input agar bisa upload foto yang sama jika dihapus
        e.target.value = ''; 
    });

    // --- 3. LOGIKA CROP FOTO ---
    let cropper = null;
    let croppingImage = null;
    const cropBtn = document.getElementById('cropBtn');
    const cropStage = document.getElementById('cropStage');
    const cropImageEl = document.getElementById('cropImage');

    // Tampilkan tombol crop HANYA saat foto diklik
    canvas.on('selection:created', checkSelection);
    canvas.on('selection:updated', checkSelection);
    canvas.on('selection:cleared', function() {
        cropBtn.classList.add('hidden');
    });

    function checkSelection(e) {
        if (e.selected && e.selected.length === 1 && e.selected[0].type === 'image') {
            cropBtn.classList.remove('hidden');
        } else {
            cropBtn.classList.add('hidden');
        }
    }

    // Buka layar Crop
    function openCropModal() {
        let activeObj = canvas.getActiveObject();
        if (!activeObj || activeObj.type !== 'image') return;

        croppingImage = activeObj;
        cropStage.classList.remove('hidden');
        cropStage.classList.add('flex');

        cropImageEl.onload = function() {
            if (cropper) cropper.destroy();
            cropper = new Cropper(cropImageEl, {
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                movable: true,
                zoomable: true,
                rotatable: false,
                scalable: false,
            });
        };
        
        // Ambil sumber gambar asli yang belum terpengaruh rotasi/skala canvas
        cropImageEl.src = activeObj.getSrc();
    }

    // Terapkan hasil Crop kembali ke Canvas
    function applyCrop() {
        if (!cropper || !croppingImage) return;

        let croppedCanvas = cropper.getCroppedCanvas({
            maxWidth: 2048,
            maxHeight: 2048,
        });

        let croppedDataUrl = croppedCanvas.toDataURL('image/png', 1.0);

        // Update gambar di canvas dengan hasil crop
        croppingImage.setSrc(croppedDataUrl, function() {
            // Kembalikan proporsi skala agar tidak distorsi
            croppingImage.set({ scaleX: 1, scaleY: 1 });
            croppingImage.scaleToWidth(200); 
            canvas.renderAll();
        });

        closeCropModal();
    }

    // Tutup layar Crop
    function closeCropModal() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        cropStage.classList.add('hidden');
        cropStage.classList.remove('flex');
        croppingImage = null;
    }

    // --- 4. LOGIKA SIMPAN (POST KE SERVER) ---
    function generateResult() {
        canvas.discardActiveObject();
        canvas.renderAll();

        const finalImage = canvas.toDataURL({ format: 'png', quality: 1 });
        const btn = document.querySelector('button[onclick="generateResult()"]');
        
        btn.innerHTML = "Menyimpan... ⏳";
        btn.disabled = true;

        fetch("{{ route('photobox.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                frame_id: "{{ $frame->id }}",
                image_base64: finalImage
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert('Gagal menyimpan foto.');
                resetButton(btn);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan sistem saat menyimpan foto!");
            resetButton(btn);
        });
    }

    function resetButton(btn) {
        btn.innerHTML = "2. Selesai & Simpan";
        btn.disabled = false;
    }
</script>
@endsection