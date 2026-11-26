<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR - {{ $barang->kode_barang }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6 font-sans antialiased">
    {{-- Print Controls --}}
    <div class="no-print mb-6 flex items-center gap-3">
        <a href="{{ route('barangs.show', $barang) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-xs transition">
            &larr; Kembali ke Detail
        </a>
        <button type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
            Cetak Label QR
        </button>
    </div>

    {{-- Printable Asset Label Card --}}
    <div class="bg-white border-2 border-slate-800 rounded-2xl p-6 w-full max-w-sm shadow-sm text-center">
        <div class="flex items-center justify-center gap-3 pb-4 border-b border-slate-200">
            <img src="{{ asset($settings->resolvedLogoPath()) }}"
                 alt="Logo {{ $settings->nama_sekolah }}"
                 class="w-11 h-11 object-contain shrink-0">
            <div class="text-left">
                <p class="text-xs font-extrabold uppercase tracking-wide text-slate-900 leading-tight">
                    {{ $settings->nama_sekolah }}
                </p>
                <p class="text-[11px] font-semibold text-indigo-700">
                    {{ $settings->nama_aplikasi }}
                </p>
            </div>
        </div>

        <div class="my-5 flex justify-center">
            <div id="print-qr-box" class="w-48 h-48 p-2 border border-slate-200 rounded-xl bg-white flex items-center justify-center"></div>
        </div>

        <div class="pt-4 border-t border-slate-200 space-y-1">
            <p class="text-lg font-mono font-extrabold text-slate-900 tracking-wider">
                {{ $barang->kode_barang }}
            </p>
            <p class="text-sm font-bold text-slate-800">
                {{ $barang->nama_barang }}
            </p>
            <p class="text-[11px] text-slate-500">
                Kondisi: {{ $barang->kondisi }} &bull; Pindai untuk detail aset
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        (function () {
            const box = document.getElementById('print-qr-box');
            if (typeof qrcode === 'function' && box) {
                const qr = qrcode(0, 'M');
                qr.addData(@json($qrUrl));
                qr.make();
                box.innerHTML = qr.createSvgTag({ cellSize: 5, margin: 0, scalable: true });
                const svg = box.querySelector('svg');
                if (svg) svg.classList.add('w-full', 'h-full');
            }
        })();
    </script>
</body>
</html>
