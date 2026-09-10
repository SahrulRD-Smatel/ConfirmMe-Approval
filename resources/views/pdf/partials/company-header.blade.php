{{-- 
    Standardized Company Letterhead / Kop Surat
    Used across all PDF templates for consistent branding.
    
    Layout: Logo | Formulir Kerja | Metadata Table
    Matches the official company form format (Formulir Kerja).

    Optional variables:
    - $accentColor (default: #7F1D1D)
    - $docNumber (document number)
    - $docDate (document date string)
    - $docRevision (revision number, default: '01')
    - $docPage (page info, default: auto)
--}}
@php
    $headerAccent = $accentColor ?? '#7F1D1D';
    $revision = $docRevision ?? '01';
    $docDateFormatted = $docDate ?? now()->format('d F Y');
@endphp

<div class="company-header">
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            {{-- Logo --}}
            <td class="header-logo-cell">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="header-logo">
                @endif
            </td>

            {{-- Center: Formulir Kerja --}}
            <td class="header-center-cell">
                <div class="header-form-title">Formulir Kerja</div>
            </td>

            {{-- Right: Metadata Table --}}
            <td class="header-meta-cell">
                <table class="header-meta-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="meta-label">No. Dokumen</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ $docNumber ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Status Revisi</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ $revision }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal Berlaku</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">{{ $docDateFormatted }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Halaman</td>
                        <td class="meta-sep">:</td>
                        <td class="meta-value">01 dari 01</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="header-line" style="border-bottom: 3px solid {{ $headerAccent }};"></div>
</div>

<style>
    .company-header {
        margin-bottom: 15px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-logo-cell {
        width: 80px;
        vertical-align: middle;
        padding-right: 5px;
    }

    .header-logo {
        height: 70px;
        width: auto;
    }

    .header-center-cell {
        vertical-align: middle;
        text-align: center;
    }

    .header-form-title {
        font-size: 20px;
        font-weight: bold;
        color: #1a1a1a;
        letter-spacing: 1px;
    }

    .header-meta-cell {
        width: 210px;
        vertical-align: middle;
    }

    .header-meta-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #333;
    }

    .header-meta-table td {
        padding: 2px 5px;
        font-size: 8px;
        border: 1px solid #333;
        line-height: 1.3;
    }

    .meta-label {
        font-weight: bold;
        color: #333;
        white-space: nowrap;
        width: 85px;
    }

    .meta-sep {
        width: 8px;
        text-align: center;
    }

    .meta-value {
        color: #333;
    }

    .header-line {
        margin-top: 8px;
        padding-bottom: 0;
    }
</style>
