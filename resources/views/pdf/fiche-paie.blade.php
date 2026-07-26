<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche de paie — Semaine {{ $semaine }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8.5px;
            color: #0F172A;
        }

        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            background: #0F172A;
            padding: 8px 14px;
            margin-bottom: 8px;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .company {
            font-size: 13px;
            font-weight: bold;
            color: #1C9F93;
        }

        .company-sub {
            font-size: 7.5px;
            color: #64748B;
            margin-top: 1px;
        }

        .doc-title {
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        .doc-num {
            font-size: 7.5px;
            color: #94a3b8;
        }

        /* ── INFO BAR ── */
        .info-bar {
            display: table;
            width: 100%;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 5px 12px;
            margin-bottom: 8px;
        }

        .info-cell {
            display: table-cell;
            padding: 0 10px 0 0;
        }

        .info-label {
            font-size: 7px;
            color: #64748B;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .info-value {
            font-size: 8.5px;
            font-weight: bold;
            color: #0F172A;
        }

        /* ── SECTION ── */
        .section-title {
            background: #1C9F93;
            color: #fff;
            font-weight: bold;
            font-size: 7.5px;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
            page-break-after: avoid;
        }

        /* ── TABLE ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        thead th {
            background: #F1F5F9;
            color: #64748B;
            font-size: 7px;
            text-transform: uppercase;
            padding: 3px 5px;
            text-align: center;
            border-bottom: 1px solid #E2E8F0;
        }

        thead th:first-child {
            text-align: left;
        }

        tbody td {
            padding: 3px 5px;
            border-bottom: 1px solid #F8FAFC;
            text-align: center;
            color: #334155;
            font-size: 8px;
        }

        tbody td:first-child {
            text-align: left;
            font-weight: 600;
            color: #0F172A;
        }

        tbody tr:nth-child(even) {
            background: #F8FAFC;
        }

        tbody tr {
            page-break-inside: avoid;
        }

        /* ── SOUS-TOTAL ── */
        .subtotal td {
            background: #E8F5F4;
            color: #1C9F93;
            font-weight: bold;
            font-size: 7.5px;
            padding: 3px 5px;
            text-align: right;
            border-top: 1px solid #1C9F93;
            page-break-before: avoid;
        }

        /* ── TOTAL GÉNÉRAL ── */
        .total-general {
            display: table;
            width: 100%;
            background: #0F172A;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .total-label {
            display: table-cell;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            vertical-align: middle;
        }

        .total-amount {
            display: table-cell;
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            color: #1C9F93;
            vertical-align: middle;
        }

        .total-cur {
            font-size: 7.5px;
            color: #64748B;
            font-weight: normal;
        }

        /* ── SIGNATURES ── */
        .sig-zone {
            display: table;
            width: 100%;
            margin-top: 12px;
            page-break-inside: avoid;
        }

        .sig-cell {
            display: table-cell;
            width: 33%;
            padding: 0 8px;
        }

        .sig-box {
            border-top: 1px solid #CBD5E1;
            padding-top: 5px;
            text-align: center;
        }

        .sig-line {
            height: 20px;
        }

        .sig-lbl {
            font-size: 7px;
            color: #64748B;
            text-transform: uppercase;
        }

        .sig-name {
            font-size: 8px;
            font-weight: bold;
            color: #0F172A;
            margin-top: 2px;
        }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #E2E8F0;
            page-break-inside: avoid;
        }

        /* ── LÉGENDE ── */
        .legende {
            font-size: 7px;
            color: #64748B;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="company">DIMA GROUPE</div>
            <div class="company-sub">Sotrac Mermoz, Lot N°71 — Dakar, Sénégal</div>
        </div>
        <div class="header-right">
            <div class="doc-title">FICHE DE PAIE HEBDOMADAIRE</div>
            <div class="doc-num">Document officiel — Confidentiel</div>
        </div>
    </div>

    {{-- INFO CHANTIER --}}
    <div class="info-bar">
        <div class="info-cell">
            <div class="info-label">Chantier</div>
            <div class="info-value">{{ $chantier->nomChantier }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Adresse</div>
            <div class="info-value">{{ $chantier->adresse }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Semaine</div>
            <div class="info-value">N° {{ $semaine }} / {{ $annee }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Du</div>
            <div class="info-value">{{ $debutSemaine }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Au</div>
            <div class="info-value">{{ $finSemaine }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Ouvriers</div>
            <div class="info-value">{{ $recaps->flatten()->count() }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Généré le</div>
            <div class="info-value">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- TABLEAU PAR POSTE --}}
    @foreach ($recaps as $posteLibelle => $lignes)
        <div class="section-title">{{ $posteLibelle }} ({{ $lignes->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left; width:20%">Ouvrier</th>
                    <th style="width:5%">Sam</th>
                    <th style="width:5%">Dim</th>
                    <th style="width:5%">Lun</th>
                    <th style="width:5%">Mar</th>
                    <th style="width:5%">Mer</th>
                    <th style="width:5%">Jeu</th>
                    <th style="width:5%">Ven</th>
                    <th style="width:5%">J.P</th>
                    <th style="width:5%">H.S</th>
                    <th style="width:10%">Sal. base</th>
                    <th style="width:10%">Sal. H.S</th>
                    <th style="width:10%; text-align:right">TOTAL (F)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lignes as $recap)
                    @php
                        // Semaine Sam→Ven
                        $samedi = \Carbon\Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->subDays(2);
                        $vendredi = $samedi->copy()->addDays(6);

                        $pointagesOuvrier = \App\Models\Pointage::where('ouvrier_id', $recap->ouvrier_id)
                            ->where('chantier_id', $recap->chantier_id)
                            ->whereBetween('date', [$samedi, $vendredi])
                            ->get()
                            ->keyBy(fn($p) => \Carbon\Carbon::parse($p->date)->toDateString());

                        // 7 jours : Sam Dim Lun Mar Mer Jeu Ven
                        $joursDates = collect(range(0, 6))->map(fn($i) => $samedi->copy()->addDays($i));

                        $statutMap = [
                            'present' => 'P',
                            'absent' => 'A',
                            'maladie' => 'M',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $recap->ouvrier->nomComplet }}</td>
                        @foreach ($joursDates as $jourDate)
                            @php
                                $p = $pointagesOuvrier->get($jourDate->toDateString());
                                $s = $p ? $statutMap[$p->statutPointage] ?? '?' : '—';
                                $color = match ($s) {
                                    'P' => 'color:#1C9F93; font-weight:bold;',
                                    'A' => 'color:#94a3b8;',
                                    'M' => 'color:#f59e0b;',
                                    default => 'color:#cbd5e1;',
                                };
                                // H.sup sous la lettre si présent
                                $hSup = $p?->heures_sup ?? 0;
                            @endphp
                            <td style="{{ $color }}">
                                {{ $s }}
                                @if ($hSup > 0)
                                    <br><span style="font-size:6px; color:#f59e0b;">+{{ $hSup }}h</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="font-weight:bold; color:#0F172A;">
                            {{ $recap->jours_presents }}
                        </td>
                        <td>
                            {{ $recap->total_heures_sup > 0 ? $recap->total_heures_sup . 'h' : '—' }}
                        </td>
                        <td>{{ number_format($recap->salaire_base, 0, ',', ' ') }}</td>
                        <td>
                            {{ $recap->salaire_heures_sup > 0 ? number_format($recap->salaire_heures_sup, 0, ',', ' ') : '—' }}
                        </td>
                        <td style="text-align:right; font-weight:bold; color:#0F172A;">
                            {{ number_format($recap->salaire_total, 0, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tr class="subtotal">
                <td colspan="12" style="text-align:right;">
                    Sous-total {{ $posteLibelle }} :
                </td>
                <td style="text-align:right; color:#1C9F93;">
                    {{ number_format($lignes->sum('salaire_total'), 0, ',', ' ') }} F
                </td>
            </tr>
        </table>
    @endforeach

    {{-- TOTAL GÉNÉRAL --}}
    <div class="total-general">
        <div class="total-label">
            TOTAL GÉNÉRAL — Semaine {{ $semaine }}/{{ $annee }}
            · {{ $recaps->flatten()->count() }} ouvriers
        </div>
        <div class="total-amount">
            {{ number_format($totalGeneral, 0, ',', ' ') }}
            <span class="total-cur">FCFA</span>
        </div>
    </div>

    {{-- LÉGENDE --}}
    <div class="legende">
        P = Présent &nbsp;·&nbsp; A = Absent &nbsp;·&nbsp; M = Maladie
        &nbsp;·&nbsp; J.P = Jours présents &nbsp;·&nbsp; H.S = Heures supplémentaires
    </div>

    {{-- SIGNATURES --}}
    <div class="sig-zone">
        <div class="sig-cell">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-lbl">Le Pointeur</div>
                <div class="sig-name">
                    {{ optional($recaps->first()?->first()?->soumisParUser)->nomComplet }}
                </div>
            </div>
        </div>
        <div class="sig-cell">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-lbl">Le Chef de projet</div>
                <div class="sig-name">
                    {{ optional($recaps->first()?->first()?->valideParUser)->nomComplet }}
                </div>
            </div>
        </div>
        <div class="sig-cell">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-lbl">La Direction</div>
                <div class="sig-name">Dima Groupe</div>
            </div>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} Dima Groupe — Document confidentiel — Système de gestion v1.0
    </div>

</body>

</html>
