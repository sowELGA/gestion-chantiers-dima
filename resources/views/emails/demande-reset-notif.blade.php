@extends('emails.layout')

@section('contenu')
    <div class="header">
        <div class="header-icon" style="background: #EF4444;">
            <svg fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                         1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                         0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <div class="header-title">Demande de réinitialisation</div>
            <div class="header-sub">Dima Groupe · Action requise</div>
        </div>
    </div>

    <div class="body">

        <div class="greeting">Bonjour Direction,</div>

        <p class="intro">
            Un utilisateur a signalé un oubli de mot de passe et demande
            une réinitialisation. Veuillez traiter cette demande depuis
            votre tableau de bord.
        </p>

        {{-- Infos utilisateur --}}
        <table width="100%" cellpadding="0" cellspacing="0"
            style="border: 1px solid #E2E8F0; border-radius: 12px;
                  overflow: hidden; margin-bottom: 24px;">
            <tr>
                <td colspan="2"
                    style="background: #0F172A; padding: 10px 20px;
                       font-size: 10px; font-weight: 700; color: #94A3B8;
                       text-transform: uppercase; letter-spacing: 1.5px;">
                    👤 Utilisateur concerné
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Nom complet</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0F172A;">
                        {{ $userDemandeur->prenomUser }} {{ $userDemandeur->nomUser }}
                    </div>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Email</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0F172A;">
                        {{ $userDemandeur->email }}
                    </div>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Rôle</div>
                    <span
                        style="display: inline-block; background: #1C9F93;
                             color: white; padding: 3px 12px; border-radius: 20px;
                             font-size: 12px; font-weight: 600;">
                        {{ match ($userDemandeur->role) {
                            'direction' => '🏢 Direction',
                            'chef_projet' => '👷 Chef de projet',
                            'pointeur' => '📋 Pointeur',
                            default => $userDemandeur->role,
                        } }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 14px 20px;">
                    <div
                        style="font-size: 10px; font-weight: 700; color: #94A3B8;
                            text-transform: uppercase; letter-spacing: 1px;
                            margin-bottom: 4px;">
                        Date de la demande</div>
                    <div style="font-size: 14px; font-weight: 600; color: #0F172A;">
                        {{ $demande->created_at->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Bouton --}}
        <a href="{{ route('direction.users.index') }}" class="btn" style="background: #EF4444;">
            Traiter la demande →
        </a>

        <div class="info-box">
            ℹ️ Connectez-vous à votre espace Direction → Gestion des utilisateurs
            pour réinitialiser le mot de passe de cet utilisateur.
        </div>

    </div>
@endsection
