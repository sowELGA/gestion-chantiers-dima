@extends('emails.layout')

@section('contenu')
    <div class="header">
        <div class="header-icon" style="background: #D97706;">
            <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                             a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <div>
            <div class="header-title">Mot de passe réinitialisé</div>
            <div class="header-sub">Dima Groupe · Gestion des chantiers</div>
        </div>
    </div>

    <div class="body">

        <div class="greeting">
            Bonjour {{ $user->prenomUser }} {{ $user->nomUser }},
        </div>

        <p class="intro">
            Suite à votre demande, la direction Dima Groupe a réinitialisé votre
            mot de passe. Voici vos nouveaux identifiants de connexion.
        </p>

        {{-- Identifiants --}}
        <table width="100%" cellpadding="0" cellspacing="0"
            style="border: 1px solid #E2E8F0; border-radius: 12px;
                  overflow: hidden; margin-bottom: 24px;">
            <tr>
                <td colspan="2"
                    style="background: #0F172A; padding: 10px 20px;
                       font-size: 10px; font-weight: 700; color: #94A3B8;
                       text-transform: uppercase; letter-spacing: 1.5px;">
                    🔑 Vos nouveaux identifiants
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Adresse email
                    </div>
                    <div style="font-size: 14px; font-weight: 600; color: #0F172A;">
                        {{ $user->email }}
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Nouveau mot de passe temporaire
                    </div>
                    <div
                        style="font-size: 22px; font-weight: 800; color: #D97706;
                            font-family: 'Courier New', monospace;
                            letter-spacing: 3px;">
                        {{ $nouveauMdp }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Bouton --}}
        <a href="{{ $lien }}" class="btn btn-warning">
            Se connecter avec le nouveau mot de passe →
        </a>

        {{-- Lien texte --}}
        <div class="lien-texte">
            🔗 Lien direct : <a href="{{ $lien }}">{{ $lien }}</a>
        </div>

        {{-- Avertissement --}}
        <div class="warning">
            <div>⚠️</div>
            <div class="warning-text">
                <strong>Important :</strong> Ce mot de passe est temporaire.
                Vous serez invité(e) à le modifier à votre prochaine connexion.
            </div>
        </div>

        <div class="info-box">
            ✅ Si vous n'avez pas fait cette demande, contactez immédiatement
            la direction Dima Groupe.
        </div>

    </div>
@endsection
