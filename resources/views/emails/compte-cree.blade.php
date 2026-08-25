@extends('emails.layout')

@section('contenu')
    {{-- Header --}}
    <div class="header">
        <div class="header-icon">
            <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <div class="header-title">Bienvenue !</div>
            <div class="header-sub">Dima Groupe · Gestion des chantiers</div>
        </div>
    </div>

    {{-- Body --}}
    <div class="body">

        <div class="greeting">
            Bonjour {{ $user->prenomUser }} {{ $user->nomUser }},
        </div>

        <p class="intro">
            La direction Dima Groupe vient de créer votre accès au système de gestion
            et de suivi des chantiers. Voici vos identifiants de connexion personnels.
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
                    🔐 Vos identifiants de connexion
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 20px; width: 40%;">
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
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Mot de passe temporaire
                    </div>
                    <div
                        style="font-size: 22px; font-weight: 800; color: #1C9F93;
                            font-family: 'Courier New', monospace;
                            letter-spacing: 3px;">
                        {{ $motDePasse }}
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 6px;">
                        Votre rôle
                    </div>
                    <span
                        style="display: inline-block; background: #1C9F93;
                             color: white; padding: 4px 14px; border-radius: 20px;
                             font-size: 12px; font-weight: 600;">
                        {{ match ($user->role) {
                            'direction' => '🏢 Direction',
                            'chef_projet' => '👷 Chef de projet',
                            'pointeur' => '📋 Pointeur',
                            default => $user->role,
                        } }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- Étapes --}}
        <div style="margin-bottom: 24px;">
            <div style="font-size: 13px; font-weight: 700; color: #0F172A;
                    margin-bottom: 12px;">
                📌 Comment accéder au système :
            </div>
            @foreach (['Cliquez sur le bouton ci-dessous pour accéder au système.', 'Saisissez votre email et votre mot de passe temporaire.', 'Vous serez invité(e) à définir un nouveau mot de passe personnel.', 'Accédez à votre tableau de bord selon votre rôle.'] as $i => $etape)
                <div style="display: flex; gap: 10px; margin-bottom: 8px;
                        align-items: flex-start;">
                    <div
                        style="width: 20px; height: 20px; background: #1C9F93;
                            color: white; border-radius: 50%; font-size: 10px;
                            font-weight: 700; display: flex; align-items: center;
                            justify-content: center; flex-shrink: 0; margin-top: 1px;">
                        {{ $i + 1 }}
                    </div>
                    <div style="font-size: 13px; color: #475569; line-height: 1.5;">
                        {{ $etape }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bouton --}}
        <a href="{{ $lien }}" class="btn">
            Accéder au système →
        </a>

        {{-- Lien texte --}}
        <div class="lien-texte">
            🔗 Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
            <a href="{{ $lien }}">{{ $lien }}</a>
        </div>

        {{-- Avertissement --}}
        <div class="warning">
            <div>⚠️</div>
            <div class="warning-text">
                <strong>Important :</strong> Ce mot de passe est temporaire.
                Vous serez obligé(e) de le modifier à votre première connexion.
                Ne partagez jamais vos identifiants.
            </div>
        </div>

    </div>
@endsection
