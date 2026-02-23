<!DOCTYPE html>
<html>
<head>
    <title>Rekam Medis - {{ $patient->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        
        /* KOP SURAT */
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #262C5B; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        
        /* INFO PASIEN */
        .patient-info { width: 100%; margin-bottom: 20px; background-color: #f9f9f9; padding: 10px; border-radius: 5px; }
        .patient-info td { padding: 3px 5px; vertical-align: top; }
        .label { font-weight: bold; width: 120px; }
        
        /* TABEL KUNJUNGAN */
        .visit-container { margin-bottom: 25px; border: 1px solid #ddd; page-break-inside: avoid; }
        .visit-header { background-color: #262C5B; color: #fff; padding: 8px; font-weight: bold; font-size: 13px; }
        .visit-table { width: 100%; border-collapse: collapse; }
        .visit-table th { text-align: left; background-color: #eee; padding: 6px; border-bottom: 1px solid #ddd; width: 25%; }
        .visit-table td { padding: 6px; border-bottom: 1px solid #ddd; }
        
        /* FOOTER */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; text-align: right; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="header" style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; min-height: 120px; position: relative;">
        {{-- Logo di sisi kiri --}}
        <div style="position: absolute; left: 0; top: 0; width: 15%;">
            <img src="{{ public_path('images/logo-tanpa-teks.png') }}" style="width: 100%; height: auto;">
        </div>

        {{-- Informasi Klinik (Digeser ke kanan agar tidak tertutup logo) --}}
        <div style="margin-left: 15%; text-align: center;">
            <h1 style="margin: 0; padding: 0; font-size: 20px; text-transform: uppercase;">
                {{ $clinic_name }}
            </h1>
            <p style="margin: 5px 0; font-size: 12px;">
                {{ $clinic_address }} | Telp: 0812-3456-7890
            </p>
            <p style="margin: 0; font-size: 14px; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-top: 10px;">
                Laporan Rekam Medis Pasien
            </p>
        </div>
    </div>

    {{-- INFO PASIEN --}}
    <table class="patient-info">
        <tr>
            <td class="label">Nama Pasien</td>
            <td>: {{ $patient->name }}</td>
            <td class="label">Nomor Rekam Medis</td>
            <td>: <strong>{{ $patient->no_rm }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td>: {{ $patient->nik }}</td>
            <td class="label">Nomor BPJS</td>
            <td>: {{ $patient->patientProfile->bpjs_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pekerjaan</td>
            <td>: {{ $patient->patientProfile->occupation ?? '-' }}</td>
            <td class="label">Pendidikan</td>
            <td colspan="3">: {{ $patient->patientProfile->education ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tgl Lahir / Usia</td>
            <td>: {{ \Carbon\Carbon::parse($patient->dob)->translatedFormat('d F Y') }} ({{ \Carbon\Carbon::parse($patient->dob)->age }} Th)</td>
            <td class="label">Alamat</td>
            <td colspan="3">: {{ $patient->address }}</td>
        </tr>
        <tr>
            <td class="label">Gol. Darah / Alergi</td>
            <td>: {{ $patient->patientProfile->blood_type ?? '-' }} / {{ $patient->patientProfile->allergies ?? 'Tidak ada' }}</td>
            <td class="label">Status Kawin</td>
            <td colspan="3">: {{ $patient->patientProfile->marital_status_label ?? '-' }}</td>
        </tr>
    </table>

    {{-- LOOPING KUNJUNGAN --}}
    @foreach($visits as $visit)
        @php $mr = $visit->medicalRecord; @endphp
        <div class="visit-container">
            <div class="visit-header">
                Kunjungan: {{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('l, d F Y') }}, Pembayaran: {{ strtoupper($visit->payment_type) }}
                <span style="float: right; font-weight: normal; font-size: 11px;">
                    Dokter: {{ $visit->dokter->user->name ?? '-' }}
                </span>
            </div>
            <table class="visit-table">
                <tr>
                    <th>Keluhan Utama</th>
                    <td>{{ $visit->complaint }}</td>
                </tr>
                <tr>
                    <th>Tanda Vital</th>
                    <td>
                        TD: {{ $mr->blood_pressure ?? '-' }} mmHg | 
                        Suhu: {{ $mr->temperature ?? '-' }} °C | 
                        BB: {{ $mr->weight ?? '-' }} kg
                    </td>
                </tr>
                <tr>
                    <th>Diagnosa (ICD-10)</th>
                    <td><strong>{{ $mr->diagnosis_icd10 ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <th>Tindakan</th>
                    <td>{{ $mr->treatment_notes ?? '-' }}</td>
                </tr>
                @if($visit->consent)
                <tr>
                    <th>Persetujuan</th>
                    <td>{{ $visit->consent }}</td>
                </tr>
                @endif
                <tr>
                    <th>Resep Obat</th>
                    <td>
                        @if(!empty($mr->medications))
                            @foreach($mr->medications as $med)
                                &bull; {{ $med['name'] }} ({{ $med['dosage'] }})<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @if($mr->follow_up_plan)
                <tr>
                    <th>Rencana Lanjutan</th>
                    <td>{{ $mr->follow_up_plan }}</td>
                </tr>
                @endif
                @if($mr->refferal)
                <tr>
                    <th>Rujukan</th>
                    <td>{{ $mr->refferal }}</td>
                </tr>
                @endif
            </table>
        </div>
    @endforeach

    <div class="footer">
        Dicetak pada: {{ $date_printed }} | Dokumen ini sah dan digenerate oleh sistem komputer.
    </div>

</body>
</html>