@php
    $isAccd = Auth::user()->hasRole('accd');
    $dashboardUrl = Auth::user()->hasRole('admin') ? route('dashboard.admin')
        : (Auth::user()->hasRole('tresorier') ? route('dashboard.tresorier')
        : (Auth::user()->hasRole('superviseur') ? route('superviseur.dashboard')
        : ($isAccd ? route('dashboard.accd')
        : route('dashboard.acct'))));

    $isDashboardActive = request()->routeIs('dashboard.*') || request()->routeIs('superviseur.dashboard');

    $fondsActive = request()->routeIs('demandes-fonds.*');
    $pcsActive = request()->routeIs('pcs.*') && !request()->routeIs('pcs.etats-consolides.*');
    $autresDemandesActive = request()->routeIs('pcs.autres-demandes.*');
    $trieActive = request()->routeIs('trie.*');
    $fnlActive = request()->routeIs('fnl.*');
    $adminActive = request()->routeIs('users.*') || request()->routeIs('postes.*');
    $etatsConsolidesActive = request()->routeIs('pcs.etats-consolides.*');
@endphp

{{-- Tableau de bord (TOUS LES RÔLES) --}}
<li class="menu-item {{ $isDashboardActive ? 'active' : '' }}">
    <a href="{{ $dashboardUrl }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-dashboard"></i>
        <div>Tableau de Bord</div>
    </a>
</li>

@if ($isAccd)
    {{-- ========================================================= --}}
    {{--  MENU SPÉCIFIQUE ACCD (juste après le dashboard)        --}}
    {{--  1. Paiements FNL     2. Autres Demandes   3. États Consolidés --}}
    {{-- ========================================================= --}}

    {{-- 1. Paiement FNL (1er menu ACCD) - lien direct, PAS de toggle pour éviter l'erreur Menu._getItem --}}
    <li class="menu-item {{ $fnlActive ? 'active' : '' }}">
        <a href="{{ route('fnl.paiements.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-home"></i>
            <div>Paiements FNL</div>
        </a>
    </li>

    {{-- 2. Autres Demandes (menu autonome pour ACCD - consultation) --}}
    <li class="menu-item {{ $autresDemandesActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-file-description"></i>
            <div>Autres Demandes</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.index') || request()->routeIs('pcs.autres-demandes.show') ? 'active' : '' }}">
                <a href="{{ route('pcs.autres-demandes.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-list"></i>
                    <div>Liste des Demandes</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.statistiques') ? 'active' : '' }}">
                <a href="{{ route('pcs.autres-demandes.statistiques') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-chart-bar"></i>
                    <div>Statistiques</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.etat-consolide.autres-demandes') || request()->routeIs('pcs.autres-demandes.etat-consolide.filtre') || request()->routeIs('pcs.autres-demandes.etat-consolide.poste-emetteur') || request()->routeIs('pcs.autres-demandes.filtre-etat') || request()->routeIs('pcs.autres-demandes.apercu') ? 'active' : '' }}">
                <a href="{{ route('pcs.autres-demandes.etat-consolide.autres-demandes') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-chart-pie"></i>
                    <div>États Consolidés</div>
                </a>
            </li>
        </ul>
    </li>

    {{-- 3. États Consolidés (menu ACCD : comprend PCS + Destockages + TRIE) --}}
    <li class="menu-item {{ $etatsConsolidesActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-chart-line"></i>
            <div>États Consolidés</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('pcs.etats-consolides.index') || request()->routeIs('pcs.etats-consolides.apercu') ? 'active' : '' }}">
                <a href="{{ route('pcs.etats-consolides.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-file-invoice"></i>
                    <div>États PC Consolidés</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('pcs.destockages.etats') || request()->routeIs('pcs.destockages.pdf.etat-consolide') ? 'active' : '' }}">
                <a href="{{ route('pcs.destockages.etats') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-receipt"></i>
                    <div>États Règlements PCS</div>
                </a>
            </li>
            @if (Auth::user()->hasAnyRole(['acct', 'admin', 'accd', 'superviseur']))
                <li class="menu-item {{ request()->routeIs('demandes-fonds.situation-mensuelle') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.situation-mensuelle') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-coins"></i>
                        <div>États Mensuels Demandes Fonds</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('demandes-fonds.consolide') || request()->routeIs('demandes-fonds.consolide-detaille') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.consolide') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-chart-area"></i>
                        <div>États Consolidés Demandes Fonds</div>
                    </a>
                </li>
            @endif
            <li class="menu-item {{ request()->routeIs('trie.etats.*') ? 'active' : '' }}">
                <a href="{{ route('trie.etats.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-building-bank"></i>
                    <div>États TRIE</div>
                </a>
            </li>
        </ul>
    </li>

@else
    {{-- ========================================================= --}}
    {{--  MENU DES AUTRES RÔLES (admin, tresorier, acct, superviseur) --}}
    {{-- ========================================================= --}}

    {{-- Demande de fonds --}}
    @if (Auth::user()->hasAnyRole(['tresorier', 'admin', 'acct', 'superviseur']))
    <li class="menu-item {{ $fondsActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-coins"></i>
            <div>Demande de fonds</div>
        </a>
        <ul class="menu-sub">
            @if (Auth::user()->hasAnyRole(['tresorier', 'admin']))
                <li class="menu-item {{ request()->routeIs('demandes-fonds.create') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.create') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-circle-plus"></i>
                        <div>Nouvelle Demande</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('demandes-fonds.index') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.index') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-list"></i>
                        <div>Liste des Demandes</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('demandes-fonds.situation') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.situation') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-chart-bar"></i>
                        <div>Situation</div>
                    </a>
                </li>
            @endif
            @if (Auth::user()->hasAnyRole(['acct', 'admin']))
                <li class="menu-item {{ request()->routeIs('demandes-fonds.envois') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.envois') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-send"></i>
                        <div>Envoi de Fonds</div>
                    </a>
                </li>
            @endif
            @if (Auth::user()->hasAnyRole(['admin', 'acct', 'superviseur']))
                <li class="menu-item {{ request()->routeIs('demandes-fonds.situation-mensuelle') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.situation-mensuelle') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-printer"></i>
                        <div>Situation Mensuelle</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('demandes-fonds.etat-detaille-avant-envoi') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.etat-detaille-avant-envoi') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-file-spreadsheet"></i>
                        <div>Situation Détaillée</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('demandes-fonds.consolide') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.consolide') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-chart-area"></i>
                        <div>État / Poste</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('demandes-fonds.consolide-detaille') ? 'active' : '' }}">
                    <a href="{{ route('demandes-fonds.consolide-detaille') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div>État / Personnel</div>
                    </a>
                </li>
            @endif
        </ul>
    </li>
    @endif

    {{-- Déclaration PC --}}
    @php
        $showPcsPoste = (Auth::user()->peut_saisir_pcs || Auth::user()->poste_id) && !Auth::user()->peut_valider_pcs && !Auth::user()->hasRole('acct') && !Auth::user()->hasRole('admin');
        $showPcsAcct = Auth::user()->peut_valider_pcs || Auth::user()->hasRole('acct') || Auth::user()->hasRole('admin');
        $pcsMenuActive = $showPcsPoste
            ? request()->routeIs('pcs.declarations.*')
            : $pcsActive;
    @endphp
    @if ($showPcsPoste || $showPcsAcct)
    <li class="menu-item {{ $pcsMenuActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-file-invoice"></i>
            <div>Déclaration PC</div>
        </a>
        <ul class="menu-sub">
            @if ($showPcsPoste)
                @if (Auth::user()->peut_saisir_pcs || Auth::user()->poste_id)
                    <li class="menu-item {{ request()->routeIs('pcs.declarations.create') ? 'active' : '' }}">
                        <a href="{{ route('pcs.declarations.create') }}" class="menu-link"><div>Nouvelle Déclaration PC</div></a>
                    </li>
                @endif
                <li class="menu-item {{ request()->routeIs('pcs.declarations.index') ? 'active' : '' }}">
                    <a href="{{ route('pcs.declarations.index') }}" class="menu-link"><div>Mes Déclarations</div></a>
                </li>
            @endif
            @if ($showPcsAcct)
                <li class="menu-item {{ request()->routeIs('pcs.declarations.index') || request()->routeIs('pcs.declarations.show') ? 'active' : '' }}">
                    <a href="{{ route('pcs.declarations.index') }}" class="menu-link"><div>Déclarations PC</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.index') ? 'active' : '' }}">
                    <a href="{{ route('pcs.autres-demandes.index') }}" class="menu-link"><div>Autres Demandes</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.statistiques') ? 'active' : '' }}">
                    <a href="{{ route('pcs.autres-demandes.statistiques') }}" class="menu-link"><div>Statistiques PCS</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('pcs.destockages.create') ? 'active' : '' }}">
                    <a href="{{ route('pcs.destockages.create') }}" class="menu-link"><div>Nouveau Règlement</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('pcs.destockages.index') ? 'active' : '' }}">
                    <a href="{{ route('pcs.destockages.index') }}" class="menu-link"><div>Règlements</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('pcs.destockages.etats') ? 'active' : '' }}">
                    <a href="{{ route('pcs.destockages.etats') }}" class="menu-link"><div>États Règlements</div></a>
                </li>
                @if (Auth::user()->hasRole('admin'))
                    <li class="menu-item {{ request()->routeIs('pcs.bureaux.*') ? 'active' : '' }}">
                        <a href="{{ route('pcs.bureaux.index') }}" class="menu-link"><div>Bureaux de Douanes</div></a>
                    </li>
                @endif
            @endif
        </ul>
    </li>
    @endif

    {{-- Autres Demandes (postes) --}}
    @if ($showPcsPoste)
    <li class="menu-item {{ $autresDemandesActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-file-description"></i>
            <div>Autres Demandes</div>
        </a>
        <ul class="menu-sub">
            @if (Auth::user()->peut_saisir_pcs || Auth::user()->poste_id)
                <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.create') ? 'active' : '' }}">
                    <a href="{{ route('pcs.autres-demandes.create') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-circle-plus"></i>
                        <div>Nouvelle Demande</div>
                    </a>
                </li>
            @endif
            <li class="menu-item {{ request()->routeIs('pcs.autres-demandes.index') || request()->routeIs('pcs.autres-demandes.show') ? 'active' : '' }}">
                <a href="{{ route('pcs.autres-demandes.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-list"></i>
                    <div>Mes Demandes</div>
                </a>
            </li>
        </ul>
    </li>
    @endif

    {{-- Fonds de garantie (TRIE) --}}
    <li class="menu-item {{ $trieActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-building-bank"></i>
            <div>Fonds Triés</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('trie.bureaux.*') ? 'active' : '' }}">
                <a href="{{ route('trie.bureaux.index') }}" class="menu-link"><div>Bureaux TRIE</div></a>
            </li>
            @if (Auth::user()->poste_id && !Auth::user()->hasRole('acct'))
                <li class="menu-item {{ request()->routeIs('trie.cotisations.create') ? 'active' : '' }}">
                    <a href="{{ route('trie.cotisations.create') }}" class="menu-link"><div>Nouvelle Cotisation</div></a>
                </li>
            @endif
            <li class="menu-item {{ request()->routeIs('trie.cotisations.*') ? 'active' : '' }}">
                <a href="{{ route('trie.cotisations.index') }}" class="menu-link"><div>Cotisations</div></a>
            </li>
        </ul>
    </li>

    {{-- Paiement FNL --}}
    @if (Auth::user()->hasAnyRole(['admin']) || (Auth::user()->hasRole('tresorier') && Auth::user()->poste_id))
    <li class="menu-item {{ $fnlActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-home"></i>
            <div>Paiement FNL</div>
        </a>
        <ul class="menu-sub">
            @if (Auth::user()->hasRole('tresorier') && Auth::user()->poste_id)
                <li class="menu-item {{ request()->routeIs('fnl.paiements.create') ? 'active' : '' }}">
                    <a href="{{ route('fnl.paiements.create') }}" class="menu-link"><div>Nouveau paiement</div></a>
                </li>
            @endif
            <li class="menu-item {{ request()->routeIs('fnl.paiements.index') || request()->routeIs('fnl.paiements.show') ? 'active' : '' }}">
                <a href="{{ route('fnl.paiements.index') }}" class="menu-link"><div>Paiements</div></a>
            </li>
        </ul>
    </li>
    @endif

    {{-- États consolidés (ACCT / Admin) --}}
    @if (Auth::user()->hasAnyRole(['acct', 'admin']))
    <li class="menu-item {{ $etatsConsolidesActive ? 'active' : '' }}">
        <a href="{{ route('pcs.etats-consolides.index') }}" class="menu-link">
            <i class="menu-icon icon-base ti tabler-chart-line"></i>
            <div>États Consolidés</div>
        </a>
    </li>
    @endif

@endif {{-- FIN du @else (rôles autres que ACCD) --}}

{{-- Messagerie --}}
{{-- <li class="menu-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
    <a href="{{ route('messages.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-mail"></i>
        <div>Messagerie</div>
    </a>
</li> --}}

{{-- Administration (ADMIN SEUL - pour tous les rôles si admin) --}}
@if (Auth::user()->hasRole('admin'))
<li class="menu-item {{ $adminActive ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-settings"></i>
        <div>Administration</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
            <a href="{{ route('users.create') }}" class="menu-link"><div>Créer un Compte</div></a>
        </li>
        <li class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link"><div>Utilisateurs</div></a>
        </li>
        <li class="menu-item {{ request()->routeIs('postes.index') ? 'active' : '' }}">
            <a href="{{ route('postes.index') }}" class="menu-link"><div>Postes</div></a>
        </li>
    </ul>
</li>
@endif
