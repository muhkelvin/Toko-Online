@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran')

@section('content')
    <div class="container mx-auto max-w-2xl">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-3xl font-playfair">Upload Bukti Pembayaran</h1>
                <p class="text-base-content/70">Untuk Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>

                {{-- Informasi Pembayaran --}}
                <div role="alert" class="alert alert-info mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="font-bold">Total Pembayaran</h3>
                        <div class="text-lg font-mono">Rp{{ number_format($order->payment->amount, 0, ',', '.') }}</div>
                    </div>
                </div>

                <form action="{{ route('payment.upload.process', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-6 mt-6">
                    @csrf
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">File Bukti Pembayaran</span>
                        </label>
                        {{-- Area Upload File Kustom --}}
                        <div class="relative w-full h-48 border-2 border-dashed border-base-300 rounded-lg flex justify-center items-center hover:border-primary transition-colors" id="drop-zone">
                            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                            {{-- Tampilan Awal --}}
                            <div class="text-center text-base-content/60" id="upload-prompt">
                                <svg class="w-12 h-12 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M3 17.25V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25z" />
                                </svg>
                                <p class="mt-2">Klik atau seret file ke sini</p>
                                <p class="text-xs mt-1">PNG, JPG (Maks. 5MB)</p>
                            </div>

                            {{-- Tampilan Preview Gambar --}}
                            <div class="hidden flex-col items-center p-4" id="image-preview">
                                <img src="" alt="Preview Bukti Pembayaran" class="max-h-32 rounded-md object-contain" id="preview-img">
                                <p class="text-sm mt-2" id="file-name"></p>
                            </div>
                        </div>
                        @error('payment_proof')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="card-actions justify-end items-center gap-4">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-ghost">Batal</a>
                        <button type="submit" class="btn btn-primary">Kirim Bukti Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('payment_proof');
            const uploadPrompt = document.getElementById('upload-prompt');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const fileNameEl = document.getElementById('file-name');

            // Fungsi untuk menampilkan preview
            function handleFile(file) {
                if (!file) return;

                // Validasi tipe file
                if (!file.type.startsWith('image/')) {
                    alert('Hanya file gambar yang diizinkan (JPG, PNG).');
                    return;
                }

                // Validasi ukuran file (max 5MB)
                const fileSize = file.size / 1024 / 1024; // dalam MB
                if (fileSize > 5) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    fileNameEl.textContent = `${file.name} (${fileSize.toFixed(2)} MB)`;
                    uploadPrompt.classList.add('hidden');
                    imagePreview.classList.remove('hidden');
                    imagePreview.classList.add('flex');
                }
                reader.readAsDataURL(file);
            }

            // Event listener untuk input file
            fileInput.addEventListener('change', () => {
                handleFile(fileInput.files[0]);
            });

            // Event listener untuk drag and drop
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-primary');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-primary');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-primary');
                const droppedFiles = e.dataTransfer.files;
                if (droppedFiles.length) {
                    fileInput.files = droppedFiles; // Assign dropped files to the input
                    handleFile(droppedFiles[0]);
                }
            });
        </script>
@endsection
