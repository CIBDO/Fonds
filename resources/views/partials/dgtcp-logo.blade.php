<div class="dgtcp-logo-container">
    <div class="dgtcp-logo">
        <div class="logo-circle outer-green">
            <div class="logo-circle middle-gold">
                <div class="logo-circle inner-red">
                    <div class="logo-center">
                        <div class="country-outline"></div>
                        <div class="key-symbol">🔑</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="logo-text-top">TRÉSOR PUBLIC</div>
        <div class="logo-text-bottom">DGTCP</div>
    </div>
</div>

<style>
.dgtcp-logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1rem 0;
}

.dgtcp-logo {
    position: relative;
    width: 120px;
    height: 120px;
    font-family: 'Arial', sans-serif;
}

.logo-circle {
    position: absolute;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.outer-green {
    width: 120px;
    height: 120px;
    background: var(--dgtcp-green);
    top: 0;
    left: 0;
}

.middle-gold {
    width: 100px;
    height: 100px;
    background: var(--dgtcp-gold);
    top: 10px;
    left: 10px;
}

.inner-red {
    width: 80px;
    height: 80px;
    background: var(--dgtcp-red);
    top: 10px;
    left: 10px;
}

.logo-center {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 10px;
    left: 10px;
}

.country-outline {
    position: absolute;
    width: 40px;
    height: 30px;
    border: 2px solid #333;
    border-radius: 20% 40% 60% 20%;
    top: 8px;
    left: 10px;
}

.key-symbol {
    position: absolute;
    font-size: 16px;
    color: var(--dgtcp-gold);
    top: 20px;
    right: 8px;
    transform: rotate(45deg);
}

.logo-text-top {
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 11px;
    font-weight: bold;
    color: var(--dgtcp-black);
    letter-spacing: 1px;
    text-align: center;
    width: 140px;
}

.logo-text-bottom {
    position: absolute;
    bottom: -25px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 16px;
    font-weight: bold;
    color: var(--dgtcp-black);
    letter-spacing: 2px;
}

.dgtcp-logo:hover {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

@media (max-width: 768px) {
    .dgtcp-logo {
        width: 80px;
        height: 80px;
    }

    .outer-green {
        width: 80px;
        height: 80px;
    }

    .middle-gold {
        width: 66px;
        height: 66px;
        top: 7px;
        left: 7px;
    }

    .inner-red {
        width: 52px;
        height: 52px;
        top: 7px;
        left: 7px;
    }

    .logo-center {
        width: 38px;
        height: 38px;
        top: 7px;
        left: 7px;
    }

    .country-outline {
        width: 26px;
        height: 20px;
        top: 5px;
        left: 6px;
    }

    .key-symbol {
        font-size: 10px;
        top: 13px;
        right: 5px;
    }

    .logo-text-top {
        font-size: 8px;
        top: -20px;
        width: 100px;
    }

    .logo-text-bottom {
        font-size: 12px;
        bottom: -20px;
    }
}
</style>
