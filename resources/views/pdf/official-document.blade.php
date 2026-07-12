<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <title>{{ $officialDocument->title }}</title>

    <style>
        @page {
            margin: 35px 42px;
        }

        body {
            margin: 0;
            color: #101828;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .document-border {
            min-height: 960px;
            padding: 26px;
            border: 3px solid #073b88;
        }

        .header-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .republic {
            color: #073b88;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
        }

        .motto {
            margin-top: 4px;
            font-size: 10px;
            text-align: center;
        }

        .logo-rca {
    width: 95px;
    height: auto;
    display: block;
}

        .title-block {
            margin-top: 42px;
            text-align: center;
        }

        .title-block h1 {
            margin: 0 0 8px;
            color: #073b88;
            font-size: 25px;
            text-transform: uppercase;
        }

        .official-number {
            color: #d21034;
            font-weight: bold;
        }

        .content {
            margin-top: 38px;
            font-size: 14px;
            text-align: justify;
        }

        .identity-table {
            width: 100%;
            margin-top: 26px;
            border-collapse: collapse;
        }

        .identity-table th,
        .identity-table td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #cfd8e6;
        }

        .identity-table th {
            width: 34%;
            color: #073b88;
            background: #edf3ff;
        }

        .certification {
            margin-top: 32px;
            padding: 18px;
            background: #f7f9fc;
            border-left: 5px solid #ffcd00;
        }

        .signature-section {
            margin-top: 50px;
        }

        .signature-cell {
            width: 55%;
            vertical-align: top;
        }

        .qr-cell {
            width: 45%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box {
            min-height: 120px;
            padding-top: 15px;
            text-align: center;
        }

        .signature-name {
            margin-top: 55px;
            font-weight: bold;
        }

        .stamp {
            width: 90px;
            height: 90px;
            margin: 10px auto 0;
            display: table;
            color: #073b88;
            border: 3px solid #073b88;
            border-radius: 50%;
            text-align: center;
        }

        .stamp span {
            display: table-cell;
            vertical-align: middle;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .qr-image {
            width: 145px;
            height: 145px;
        }

        .verification-text {
            margin-top: 7px;
            font-size: 8px;
            word-break: break-all;
        }

        .security {
            margin-top: 32px;
            padding-top: 15px;
            color: #667085;
            font-size: 8px;
            border-top: 1px solid #d5dce8;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -18px;
            left: 0;
            color: #667085;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="document-border">

    <table class="header-table">
    <tr>
        <td style="width: 25%; vertical-align: top;">
            @if($logoDataUri)
                <img
                    src="{{ $logoDataUri }}"
                    class="logo-rca"
                    alt="Logo RCA"
                >
            @endif
        </td>

        <td style="width: 50%; vertical-align: top;">
            <div class="republic">
                RÉPUBLIQUE CENTRAFRICAINE
            </div>

            <div class="motto">
                Unité — Dignité — Travail
            </div>
        </td>

        <td style="
            width: 25%;
            vertical-align: top;
            text-align: right;
        ">
            <strong>PNAE-RCA</strong><br>
            Administration<br>
            électronique
        </td>
    </tr>
</table>

    <div class="title-block">
        <h1>{{ $officialDocument->title }}</h1>

        <div class="official-number">
            N° {{ $officialDocument->official_number }}
        </div>
    </div>

    <div class="content">
        <p>
            La Plateforme Nationale d’Administration Électronique
            certifie que le présent document a été délivré à :
        </p>

        <table class="identity-table">
            <tr>
                <th>Nom du bénéficiaire</th>
                <td>{{ $application->user->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Adresse électronique</th>
                <td>{{ $application->user->email ?? '-' }}</td>
            </tr>

            <tr>
                <th>Démarche administrative</th>
                <td>{{ $application->procedure->title ?? '-' }}</td>
            </tr>

            <tr>
                <th>Ministère compétent</th>
                <td>
                    {{ $application->procedure->ministry->name ?? '-' }}
                </td>
            </tr>

            <tr>
                <th>Référence du dossier</th>
                <td>{{ $application->reference }}</td>
            </tr>

            <tr>
                <th>Date de délivrance</th>
                <td>
                    {{ $officialDocument->issued_at->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>

        <div class="certification">
            Après vérification du dossier, des pièces justificatives et,
            lorsque nécessaire, du paiement des frais administratifs,
            le présent document est déclaré conforme et valable.
        </div>
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-box">
                        <strong>
                            Autorité administrative compétente
                        </strong>

                        <div class="stamp">
                            <span>
                                Cachet officiel<br>
                                PNAE-RCA
                            </span>
                        </div>

                        <div class="signature-name">
                            Signature électronique
                        </div>
                    </div>
                </td>

                <td class="qr-cell">
                    <strong>Vérification d’authenticité</strong>

                    <div>
                        <img
                            src="{{ $qrCodeDataUri }}"
                            class="qr-image"
                            alt="QR Code"
                        >
                    </div>

                    <div class="verification-text">
                        {{ $verificationUrl }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="security">
        Empreinte SHA-256 enregistrée dans la plateforme nationale.<br>
        Code de signature :
        {{ $officialDocument->signature_code }}
    </div>

</div>

<div class="footer">
    Document généré électroniquement par la PNAE-RCA —
    {{ $officialDocument->official_number }}
</div>

</body>
</html>