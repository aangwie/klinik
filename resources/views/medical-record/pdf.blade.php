<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis - {{ $patient->name }}</title>
    @php
        $appName = \App\Models\Setting::getAppName();
        $appLogoBase64 = \App\Models\Setting::getAppLogoBase64();
    @endphp
    @if($appLogoBase64)
    <link rel="icon" href="{{ $appLogoBase64 }}" type="image/png">
    @endif
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #059669; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; color: #059669; }
        .header p { margin: 3px 0; color: #666; }
        .header img { max-width: 50px; max-height: 50px; margin-bottom: 5px; }
        .patient-info { margin-bottom: 20px; padding: 10px; background: #f9fafb; }
        .patient-info table { width: 100%; }
        .patient-info td { padding: 3px 10px; }
        .visit { margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 10px; border-radius: 5px; }
        .visit-header { background: #f0fdf4; padding: 5px 10px; margin: -10px -10px 10px -10px; border-bottom: 1px solid #e5e7eb; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 5px; text-align: left; }
        .label { color: #666; width: 120px; }
        .footer { text-align: center; margin-top: 30px; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        @if($appLogoBase64)
        <img src="{{ $appLogoBase64 }}" alt="Logo">
        @endif
        <h1>{{ strtoupper($appName) }}</h1>
        <p>Praktik Dokter Umum</p>
        <p>REKAM MEDIS PASIEN</p>
    </div>

    <div class="patient-info">
        <table>
            <tr><td class="label">No. Rekam Medis</td><td><strong>{{ $patient->medical_record_number }}</strong></td></tr>
            <tr><td class="label">Nama Pasien</td><td><strong>{{ $patient->name }}</strong></td></tr>
            <tr><td class="label">NIK</td><td>{{ $patient->nik }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td>{{ $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
            <tr><td class="label">Tanggal Lahir</td><td>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : '-' }}</td></tr>
            <tr><td class="label">Alamat</td><td>{{ $patient->address ?? '-' }}</td></tr>
            <tr><td class="label">No. HP</td><td>{{ $patient->phone ?? '-' }}</td></tr>
        </table>
    </div>

    <h3 style="color: #059669;">Riwayat Kunjungan</h3>

    @forelse($examinations as $exam)
    <div class="visit">
        <div class="visit-header">
            {{ \Carbon\Carbon::parse($exam->created_at)->isoFormat('dddd, D MMMM Y') }} - Dokter: {{ $exam->doctor->name ?? '-' }}
        </div>
        <table>
            <tr><td class="label">Keluhan</td><td>{{ $exam->complaint }}</td></tr>
            <tr><td class="label">Diagnosa</td><td>{{ $exam->diagnosis }}</td></tr>
            @if($exam->blood_pressure || $exam->weight || $exam->temperature)
            <tr>
                <td class="label">Tanda Vital</td>
                <td>TD: {{ $exam->blood_pressure ?? '-' }} | BB: {{ $exam->weight ?? '-' }} kg | TB: {{ $exam->height ?? '-' }} cm | Suhu: {{ $exam->temperature ?? '-' }}°C | Nadi: {{ $exam->pulse ?? '-' }} bpm</td>
            </tr>
            @endif
            @if($exam->actions)
            <tr><td class="label">Tindakan</td><td>{{ $exam->actions }}</td></tr>
            @endif
            @if($exam->prescriptions->count() > 0)
            <tr>
                <td class="label">Resep Obat</td>
                <td>
                    @foreach($exam->prescriptions as $pres)
                        {{ $pres->medicine->name ?? '-' }} x{{ $pres->qty }} ({{ $pres->instruction }})<br>
                    @endforeach
                </td>
            </tr>
            @endif
            @if($exam->notes)
            <tr><td class="label">Catatan</td><td>{{ $exam->notes }}</td></tr>
            @endif
        </table>
    </div>
    @empty
    <p style="text-align:center; color:#999;">Belum ada riwayat kunjungan</p>
    @endforelse

    <div class="footer">
        <p>Dicetak pada: {{ now()->isoFormat('dddd, D MMMM Y H:mm') }} WIB</p>
        <p>Dokumen ini adalah informasi medis yang bersifat rahasia</p>
    </div>
</body>
</html>