<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 20px; }
        .struk { max-width: 300px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 2px 0; color: #555; }
        .divider { border-top: 1px dashed #333; margin: 10px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .total { font-weight: bold; font-size: 14px; margin-top: 5px; }
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
            <h2>KLINIK SEHAT</h2>
            <p>Praktik Dokter Umum</p>
            <p>{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            <p>{{ now()->format('H:i') }} WIB</p>
        </div>
        <div class="divider"></div>

        @if($type == 'doctor')
            <p><strong>INVOICE: {{ $payment->invoice_number }}</strong></p>
            <div class="divider"></div>
            <p>Nama: {{ $payment->patient->name }}</p>
            <p>No. RM: {{ $payment->patient->medical_record_number }}</p>
            <div class="divider"></div>
            <div class="row"><span>Biaya Konsultasi</span><span>Rp {{ number_format($payment->consultation_fee, 0, ',', '.') }}</span></div>
            @if($payment->action_fee > 0)
            <div class="row"><span>Biaya Tindakan</span><span>Rp {{ number_format($payment->action_fee, 0, ',', '.') }}</span></div>
            @endif
            <div class="divider"></div>
            <div class="row total"><span>TOTAL</span><span>Rp {{ number_format($payment->total, 0, ',', '.') }}</span></div>
            <p>Metode: {{ strtoupper($payment->payment_method) }}</p>
            <p>Status: LUNAS</p>
        @else
            <p><strong>RECEIPT: {{ $payment->receipt_number }}</strong></p>
            <div class="divider"></div>
            <p>Nama: {{ $payment->patient->name }}</p>
            <p>No. RM: {{ $payment->patient->medical_record_number }}</p>
            <div class="divider"></div>
            <p><strong>Obat:</strong></p>
            <p>{{ $payment->prescription->medicine->name ?? '-' }} x{{ $payment->prescription->qty ?? 0 }}</p>
            <div class="divider"></div>
            <div class="row total"><span>TOTAL</span><span>Rp {{ number_format($payment->total, 0, ',', '.') }}</span></div>
            <p>Metode: {{ strtoupper($payment->payment_method) }}</p>
            <p>Status: LUNAS</p>
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