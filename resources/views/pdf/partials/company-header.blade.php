{{-- 
    Standardized Company Letterhead / Kop Surat
    Used across all PDF templates for consistent branding.
    
    Required variables: None (all hardcoded for consistency)
    Optional variables: $accentColor (default: #CC0000)
--}}
@php
    $headerAccent = $accentColor ?? '#CC0000';
@endphp

<div class="company-header">
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-logo-cell">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="header-logo">
                @endif
            </td>
            <td class="header-info-cell">
                <div class="header-company-name">PT. Info Solusindo Data Utama</div>
                <div class="header-address">
                    Gedung Menara Hijau | Lantai 9 Suite 903B |<br>
                    Jl. MT. Haryono, Kav. 33 | Pancoran, Jakarta Selatan 12770, Indonesia<br>
                    Ph. +6221 7986 172 | Fax. +6221 7986 143
                </div>
            </td>
        </tr>
    </table>
    <div class="header-line" style="border-bottom: 3px solid {{ $headerAccent }};"></div>
</div>

<style>
    .company-header {
        margin-bottom: 20px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-logo-cell {
        width: 90px;
        vertical-align: middle;
        padding-right: 5px;
    }

    .header-logo {
        height: 85px;
        width: auto;
    }

    .header-info-cell {
        vertical-align: middle;
    }

    .header-company-name {
        font-size: 22px;
        font-weight: bold;
        color: #1a1a1a;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .header-address {
        font-size: 9px;
        color: #555;
        line-height: 1.5;
    }

    .header-line {
        margin-top: 10px;
        padding-bottom: 0;
    }
</style>
