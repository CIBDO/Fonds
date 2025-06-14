<div class="dgtcp-institutional-banner">
    <div class="row align-items-center">
        <div class="col-md-1 text-center">
            <div class="mini-logo">
                <div class="mini-circle outer-green">
                    <div class="mini-circle middle-gold">
                        <div class="mini-circle inner-red">
                            <div class="mini-center">
                                <span class="key-mini">🔑</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="institution-info">
                <h6 class="institution-name">RÉPUBLIQUE DU BÉNIN</h6>
                <h5 class="ministry-name">MINISTÈRE DE L'ÉCONOMIE ET DES FINANCES</h5>
                <h4 class="dgtcp-name">DIRECTION GÉNÉRALE DU TRÉSOR ET DE LA COMPTABILITÉ PUBLIQUE</h4>
            </div>
        </div>
        <div class="col-md-3 text-end">
            <div class="official-stamp">
                <div class="stamp-text">OFFICIEL</div>
                <div class="stamp-year">{{ date('Y') }}</div>
            </div>
        </div>
    </div>
</div>

<style>
.dgtcp-institutional-banner {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(245, 158, 11, 0.05) 100%);
    border: 2px solid var(--dgtcp-green);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(34, 197, 94, 0.1);
}

.mini-logo {
    position: relative;
    width: 60px;
    height: 60px;
}

.mini-circle {
    position: absolute;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mini-logo .outer-green {
    width: 60px;
    height: 60px;
    background: var(--dgtcp-green);
    top: 0;
    left: 0;
}

.mini-logo .middle-gold {
    width: 48px;
    height: 48px;
    background: var(--dgtcp-gold);
    top: 6px;
    left: 6px;
}

.mini-logo .inner-red {
    width: 36px;
    height: 36px;
    background: var(--dgtcp-red);
    top: 6px;
    left: 6px;
}

.mini-center {
    width: 24px;
    height: 24px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 6px;
    left: 6px;
    position: relative;
}

.key-mini {
    font-size: 10px;
    color: var(--dgtcp-gold);
    transform: rotate(45deg);
}

.institution-info {
    text-align: left;
}

.institution-name {
    color: var(--dgtcp-green);
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.25rem;
}

.ministry-name {
    color: var(--dgtcp-gold);
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.dgtcp-name {
    color: var(--dgtcp-red);
    font-weight: 800;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0;
}

.official-stamp {
    background: var(--primary-gradient);
    color: white;
    padding: 1rem;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 3px 10px rgba(34, 197, 94, 0.3);
    transform: rotate(-15deg);
}

.stamp-text {
    font-size: 0.7rem;
    font-weight: 800;
    text-align: center;
    line-height: 1;
}

.stamp-year {
    font-size: 0.6rem;
    font-weight: 600;
    margin-top: 0.2rem;
}

.dgtcp-institutional-banner:hover {
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.15);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .dgtcp-institutional-banner {
        padding: 1rem;
    }

    .institution-name {
        font-size: 0.7rem;
    }

    .ministry-name {
        font-size: 0.8rem;
    }

    .dgtcp-name {
        font-size: 0.9rem;
    }

    .official-stamp {
        width: 60px;
        height: 60px;
        padding: 0.5rem;
    }

    .stamp-text {
        font-size: 0.6rem;
    }

    .stamp-year {
        font-size: 0.5rem;
    }

    .mini-logo {
        width: 40px;
        height: 40px;
    }

    .mini-logo .outer-green {
        width: 40px;
        height: 40px;
    }

    .mini-logo .middle-gold {
        width: 32px;
        height: 32px;
        top: 4px;
        left: 4px;
    }

    .mini-logo .inner-red {
        width: 24px;
        height: 24px;
        top: 4px;
        left: 4px;
    }

    .mini-center {
        width: 16px;
        height: 16px;
        top: 4px;
        left: 4px;
    }

    .key-mini {
        font-size: 8px;
    }
}
</style>
