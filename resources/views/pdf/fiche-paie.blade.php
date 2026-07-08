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
            font-size: 11px;
            color: #0F172A;
            background: #ffffff;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            padding: 16px 20px;
            background: #0F172A;
            margin-bottom: 16px;
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

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1C9F93;
            letter-spacing: 1px;
        }

        .company-sub {
            font-size: 9px;
            color: #64748B;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
        }

        .doc-sub {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ── INFO CHANTIER ── */
        .info-bar {
            display: table;
            width: 100%;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 16px;
        }

        .info-cell {
            display: table-cell;
            padding: 0 16px 0 0;
        }

        .info-label {
            font-size: 8px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 11px;
            font-weight: bold;
            color: #0F172A;
        }

        /* ── SECTION POSTE ── */
        .section-title {
            background: #1C9F93;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            padding: 6px 12px;
            border-radius: 4px 4px 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 12px;
        }

        /* ── TABLE ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        thead th {
            background: #F1F5F9;
            color: #64748B;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 6px 10px;
            text-align: center;
            border-bottom: 1px solid #E2E8F0;
        }

        thead th:first-child {
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: #F8FAFC;
        }

        tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #F1F5F9;
            text-align: center;
            color: #334155;
        }

        tbody td:first-child {
            text-align: left;
            font-weight: 600;
            color: #0F172A;
        }

        tbody td:last-child {
            font-weight: bold;
            color: #0F172A;
        }

        /* ── SOUS-TOTAL ── */
        .subtotal-row td {
            background: #E8F5F4;
            color: #1C9F93;
            font-weight: bold;
            padding: 5px 10px;
            border-top: 1px solid #1C9F93;
            text-align: right;
            font-size: 10px;
        }

        .subtotal-row td:first-child {
            text-align: right;
        }

        /* ── TOTAL GÉNÉRAL ── */
        .total-general {
            display: table;
            width: 100%;
            background: #0F172A;
            border-radius: 6px;
            padding: 12px 16px;
            margin-top: 16px;
        }

        .total-label {
            display: table-cell;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
            vertical-align: middle;
        }

        .total-amount {
            display: table-cell;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #1C9F93;
            vertical-align: middle;
        }

        .total-currency {
            font-size: 10px;
            color: #64748B;
            font-weight: normal;
        }

        /* ── SIGNATURE ── */
        .signature-zone {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .sig-cell {
            display: table-cell;
            width: 33%;
            padding: 0 10px;
        }

        .sig-box {
            border-top: 1px solid #CBD5E1;
            padding-top: 8px;
            text-align: center;
        }

        .sig-label {
            font-size: 9px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sig-name {
            font-size: 10px;
            font-weight: bold;
            color: #0F172A;
            margin-top: 4px;
        }

        .sig-line {
            height: 32px;
            margin-bottom: 4px;
        }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #E2E8F0;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">DIMA GROUPE</div>
            <div class="company-sub">Sotrac Mermoz, Lot N°71, Résidence OMC — Dakar</div>
        </div>
        <div class="header-right">
            <div class="doc-title">FICHE DE PAIE HEBDOMADAIRE</div>
            <div class="doc-sub">Document officiel — Confidentiel</div>
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
            <div class="info-label">Période</div>
            <div class="info-value">{{ $debutSemaine }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Au</div>
            <div class="info-value">{{ $finSemaine }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Généré le</div>
            <div class="info-value">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- TABLEAU PAR POSTE --}}
    @foreach ($recaps as $posteLibelle => $lignes)
        <div class="section-title">{{ $posteLibelle }}</div>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left; width:28%">Ouvrier</th>
                    <th>Lun</th>
                    <th>Mar</th>
                    <th>Mer</th>
                    <th>Jeu</th>
                    <th>Ven</th>
                    <th>Sam</th>
                    <th>Dim</th>
                    <th>Jours</th>
                    <th>H. sup</th>
                    <th>Sal. base</th>
                    <th>Sal. H.sup</th>
                    <th style="text-align:right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lignes as $recap)
                    @php
                        // Charger les pointages de la semaine pour cet ouvrier
                        $debut = \Carbon\Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
                        $fin = \Carbon\Carbon::now()->setISODate($annee, $semaine)->endOfWeek();

                        $pointagesOuvrier = \App\Models\Pointage::where('ouvrier_id', $recap->ouvrier_id)
                            ->where('chantier_id', $recap->chantier_id)
                            ->whereBetween('date', [$debut, $fin])
                            ->get()
                            ->keyBy(fn($p) => \Carbon\Carbon::parse($p->date)->dayOfWeek);

                        // dayOfWeek: 1=Lun, 2=Mar, 3=Mer, 4=Jeu, 5=Ven, 6=Sam, 0=Dim
                        $jours = [1, 2, 3, 4, 5, 6, 0];
                        $statutMap = [
                            'present' => 'P',
                            'absent' => 'A',
                            'maladie' => 'M',
                        ];
                    @endphp
                    <tr>
                        <td>{{ $recap->ouvrier->nomComplet }}</td>
                        @foreach ($jours as $jour)
                            @php
                                $p = $pointagesOuvrier->get($jour);
                                $s = $p ? $statutMap[$p->statutPointage] ?? '?' : '—';
                                $color = match ($s) {
                                    'P' => 'color:#1C9F93; font-weight:bold;',
                                    'A' => 'color:#94a3b8;',
                                    'M' => 'color:#f59e0b;',
                                    default => 'color:#cbd5e1;',
                                };
                            @endphp
                            <td style="{{ $color }}">{{ $s }}</td>
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
                        <td style="text-align:right; color:#0F172A; font-weight:bold;">
                            {{ number_format($recap->salaire_total, 0, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tr class="subtotal-row">
                <td colspan="12" style="text-align:right;">
                    Sous-total {{ $posteLibelle }} :
                </td>
                <td style="text-align:right; color:#1C9F93;">
                    {{ number_format($lignes->sum('salaire_total'), 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </table>
    @endforeach

    {{-- TOTAL GÉNÉRAL --}}
    <div class="total-general">
        <div class="total-label">TOTAL GÉNÉRAL — Semaine {{ $semaine }}</div>
        <div class="total-amount">
            {{ number_format($totalGeneral, 0, ',', ' ') }}
            <span class="total-currency">FCFA</span>
        </div>
    </div>

    {{-- SIGNATURES --}}
    <div class="signature-zone">
        <div class="sig-cell">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Le Pointeur</div>
                <div class="sig-name">{{ optional($recaps->first()?->first()?->soumisParUser)->nomComplet }}</div>
            </div>
        </div>
        <div class="sig-cell">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">Le Chef de projet</div>
                <div class="sig-name">{{ optional($recaps->first()?->first()?->valideParUser)->nomComplet }}</div>
            </div>
        </div>
        <div class="sig-cell">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-label">La Direction</div>
                <div class="sig-name">Dima Groupe</div>
            </div>
        </div>
    </div>

    {{-- LÉGENDE --}}
    <div class="footer">
        Légende : P = Présent · A = Absent · M = Maladie
        &nbsp;&nbsp;|&nbsp;&nbsp;
        © {{ date('Y') }} Dima Groupe — Document confidentiel —
        Système de gestion des chantiers v1.0
    </div>

</body>

</html>
