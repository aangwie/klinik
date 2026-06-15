<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ \App\Models\Setting::getAppName() }}</title>
    @php
        $appName = \App\Models\Setting::getAppName();
        $appLogoBase64 = \App\Models\Setting::getAppLogoBase64();
    @endphp
    @if($appLogoBase64)
    <link rel="icon" href="{{ $appLogoBase64 }}" type="image/png">
    @endif
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 20px; }
        .struk { max-width: 300px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 2px 0; color: #555; }
        .divider { border-top: 1px dashed #333; margin: 10px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .total { font-weight: bold; font-size: 14px; margin-top: 5px; }
        .medicine-item { margin: 2px 0; padding-left: 8px; font-size: 11px; display: flex; justify-content: space-between; }
        .label-obat { font-size: 11px; margin: 6px 0 2px 0; padding: 4px 6px; background: #f0fdf4; border-left: 3px solid #10b981; }
        .footer { text-align: center; margin-top: 15px; font-size: 11px; color: #777; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="header">
            @if($appLogoBase64)
            <img src="{{ $appLogoBase64 }}" alt="Logo" style="max-width:60px; max-height:60px; margin-bottom:5px;">
            @endif
            <h2>{{ strtoupper($appName) }}</h2>
            <p>Praktik Dokter Umum</p>
            <p>{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            <p>{{ now()->format('H:i') }} WIB</p>
        </div>
        <div class="divider"></div>
        <p>Nama: {{ $payment->patient->name }}</p>
        <p>No. RM: {{ $payment->patient->medical_record_number }}</p>
        <div class="divider"></div>

        @php
            $grandTotal = $payment->total + ($payment->medicine_total ?? 0);
        @endphp

        <p><strong>INVOICE: {{ $payment->invoice_number }}</strong></p>
        <div class="divider"></div>
        <p class="row" style="font-weight:600;font-size:13px;">RINCIAN BIAYA</p>
        <div class="divider" style="border-top-style:solid;border-top-width:1px;"></div>
        <div class="row"><span>Biaya Konsultasi</span><span>Rp {{ number_format($payment->consultation_fee, 0, ',', '.') }}</span></div>
        @if($payment->action_fee > 0)
        <div class="row"><span>Biaya Tindakan</span><span>Rp {{ number_format($payment->action_fee, 0, ',', '.') }}</span></div>
        @endif
        <div class="row" style="border-top:1px dashed #ddd;padding-top:4px;">
            <span>Subtotal Jasa Dokter</span>
            <span>Rp {{ number_format($payment->total, 0, ',', '.') }}</span>
        </div>
        @if(isset($payment->medicine_details) && count($payment->medicine_details) > 0)
        <div class="divider"></div>
        <p><strong>Obat:</strong></p>
        @foreach($payment->medicine_details as $md)
        <div class="medicine-item"><span>{{ $md->name }} x{{ $md->qty }}</span><span>Rp {{ number_format($md->subtotal, 0, ',', '.') }}</span></div>
        @endforeach
        <div class="row" style="border-top:1px dashed #ddd;padding-top:4px;">
            <span>Subtotal Obat</span>
            <span>Rp {{ number_format($payment->medicine_total, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="divider"></div>
        <div class="row total"><span>TOTAL KESELURUHAN</span><span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span></div>
        <p>Metode: {{ strtoupper($payment->payment_method) }}</p>
        <p>Status: LUNAS</p>

        @if(isset($payment->medicine_details) && count($payment->medicine_details) > 0)
        <div class="divider"></div>
        <p style="font-weight:600;font-size:13px;">INFORMASI OBAT</p>
        <p style="font-size:10px;color:#888;">(Serahkan ke apoteker)</p>
        <div class="divider" style="border-top-style:solid;border-top-width:1px;"></div>
        @foreach($payment->medicine_details as $md)
        <div class="label-obat">
            <strong>{{ $md->name }}</strong> - {{ $md->qty }} {{ $md->qty > 1 ? 'tablet/bungkus' : 'tablet/bungkus' }}
        </div>
        <div style="font-size:10px;padding-left:8px;margin-bottom:8px;">
            Aturan Pakai: <strong>{{ $md->instruction ?? 'Sesuai petunjuk dokter' }}</strong>
        </div>
        @endforeach
        @endif

        <div class="divider"></div>
        <div class="footer">
            <p>Terima Kasih</p>
            <p>Semoga Lekas Sembuh</p>
        </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()" style="padding:10px 30px; background:#059669; color:white; border:none; border-radius:5px; cursor:pointer;">Cetak Struk</button>
        <button onclick="window.close()" style="padding:10px 30px; background:#6b7280; color:white; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Tutup</button>
    </div>
</body>
</html>