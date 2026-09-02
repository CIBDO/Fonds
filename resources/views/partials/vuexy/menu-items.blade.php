@php
    $dashboardUrl = Auth::user()->hasRole('admin') ? route('dashboard.admin')
        : (Auth::user()->hasRole('tresorier') ? route('dashboard.tresorier')
        : (Auth::user()->hasRole('superviseur') ? route('superviseur.dashboard')
        : route('dashboard.acct')));

    $isDashboardActive = request()->routeIs('dashboard.*') || request()->routeIs('superviseur.dashboard');

    $fondsActive = request()->routeIs('demandes-fonds.*');
    $pcsActive = request()->routeIs('pcs.*') && !request()->routeIs('pcs.etats-consolides.*');
    $autresDemandesActive = request()->routeIs('pcs.autres-demandes.*');
    $trieActive = request()->routeIs('trie.*');
    $adminActive = request()->routeIs('users.*') || request()->routeIs('postes.*');
@endphp

{{-- Tableau de bord --}}
<li class="menu-item {{ $isDashboardActive ? 'active' : '' }}">
    <a href="{{ $dashboardUrl }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-dashboard"></i>
        <div>Tableau de Bord</div>
    </a>
</li>

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

{{-- Messagerie --}}
<li class="menu-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
    <a href="{{ route('messages.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-mail"></i>
        <div>Messagerie</div>
    </a>
</li>

{{-- Administration --}}
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
