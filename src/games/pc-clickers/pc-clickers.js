class PCClicker {
    constructor() {
        this.pc = 0;
        this.pcPerClick = 0.1;
        this.pcPerSecond = 0;
        this.multiplier = 1;
        this.goldenChance = 0.005;
        this.totalClicks = 0;

        this.upgrades = {
            // Hardware
            mouse: { cost: 15, baseIncrease: 0.1, perClick: true, owned: 0, multiplier: 1.2 },
            keyboard: { cost: 100, baseIncrease: 0.5, perClick: false, owned: 0, multiplier: 1.2 },
            screen: { cost: 500, baseIncrease: 2, perClick: false, owned: 0, multiplier: 1.2 },
            gpu: { cost: 2000, baseIncrease: 5, perClick: false, owned: 0, multiplier: 1.2 },
            ram: { cost: 5000, baseIncrease: 8, perClick: false, owned: 0, multiplier: 1.2 },
            cpu: { cost: 10000, baseIncrease: 12, perClick: false, owned: 0, multiplier: 1.2 },
            watercooling: { cost: 25000, baseIncrease: 20, perClick: false, owned: 0, multiplier: 1.2 },
            // Software
            antivirus: { cost: 100000, baseIncrease: 5, perClick: true, owned: 0, multiplier: 1.3 },
            system: { cost: 250000, baseIncrease: 5, perClick: false, owned: 0, multiplier: 1.3 },
            miner: { cost: 500000, baseIncrease: 15, perClick: false, owned: 0, multiplier: 1.3 },
            ai: { cost: 1000000, baseIncrease: 30, perClick: false, owned: 0, multiplier: 1.3 },
            // Network
            wifi: { cost: 2500000, baseIncrease: 10, perClick: false, owned: 0, multiplier: 1.35 },
            server: { cost: 10000000, baseIncrease: 40, perClick: false, owned: 0, multiplier: 1.35 },
            datacenter: { cost: 50000000, baseIncrease: 80, perClick: false, owned: 0, multiplier: 1.35 }
        };

        this.achievements = {
            'first-pc': { required: 1, checked: false },
            'clicker': { required: 100, checked: false },
            'collector': { required: 5, checked: false },
            'millionaire': { required: 1000000, checked: false }
        };

        // --- Variables principales ---
        this.pcCount = 0;
        this.pcPerClick = 1;
        this.clickCount = 0;
       /* this.achievementsLevel = {
            "first-pc": false,
            "beginner-clicker": false,
            "hardware-fan": false,
            "click-master": false,
            "golden-hunter": false,
            "software-expert": false,
            "millionaire": false,
            "network-king": false,
            "legendary": false
        };*/

        // DOM Elements
        this.pcCountEl = document.getElementById('pc-count');
        this.pcPerSecondEl = document.getElementById('pc-per-second');
        this.multiplierEl = document.getElementById('multiplier');
        this.clickablePc = document.querySelector('.clickable-pc');
        this.menuBtns = document.querySelectorAll('.menu-btn');
        this.gameSection = document.querySelector('.game-section');
        this.achievementsSection = document.querySelector('.achievements-section');
        this.achievementEls = document.querySelectorAll('.achievement');
        this.saveBtn = document.getElementById('save');
        this.resetBtn = document.getElementById('reset');
        this.goldenPcArea = document.getElementById('golden-pc-area');

        // Event Listeners
        this.clickablePc.onclick = (e) => this.click(e);
        
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.onclick = () => this.switchCategory(btn.dataset.category);
        });

        document.querySelectorAll('.menu-btn').forEach(btn => {
            btn.onclick = () => this.switchSection(btn.dataset.menu);
        });

        document.querySelectorAll('.shop-item').forEach(item => {
            item.onclick = () => this.buyUpgrade(item.dataset.item);
        });

        document.getElementById('save').onclick = () => this.save();
        document.getElementById('reset').onclick = () => this.reset();

        // Start
        this.load();
        setInterval(() => this.tick(), 1000);
        setInterval(() => this.save(), 30000);
    }

    click(e) {
        const gain = Math.round(this.pcPerClick * this.multiplier * 100) / 100;
        this.pc += gain;
        this.totalClicks++;

        const effect = document.createElement('div');
        effect.className = 'click-effect';
        effect.textContent = `+${gain}`;
        effect.style.left = e.offsetX + 'px';
        effect.style.top = e.offsetY + 'px';
        this.clickablePc.appendChild(effect);
        setTimeout(() => effect.remove(), 1000);

        this.checkAchievements();
        this.updateDisplay();
    }

    buyUpgrade(type) {
        const upgrade = this.upgrades[type];
        if (!upgrade || this.pc < upgrade.cost) return;

        this.pc -= upgrade.cost;
        upgrade.owned++;

        if (upgrade.perClick) {
            this.pcPerClick = Math.round((this.pcPerClick + upgrade.baseIncrease) * 100) / 100;
        } else {
            this.pcPerSecond = Math.round((this.pcPerSecond + upgrade.baseIncrease) * 100) / 100;
        }

        upgrade.cost = Math.ceil(upgrade.cost * upgrade.multiplier);
        this.checkAchievements();
        this.updateDisplay();
    }

    checkAchievements() {
        let totalUpgrades = Object.values(this.upgrades).reduce((sum, upg) => sum + upg.owned, 0);

        if (!this.achievements['first-pc'].checked && this.pc >= 1) {
            this.unlockAchievement('first-pc');
        }
        if (!this.achievements['clicker'].checked && this.totalClicks >= 100) {
            this.unlockAchievement('clicker');
        }
        if (!this.achievements['collector'].checked && totalUpgrades >= 5) {
            this.unlockAchievement('collector');
        }
        if (!this.achievements['millionaire'].checked && this.pc >= 1000000) {
            this.unlockAchievement('millionaire');
        }

        // Update progress bars
        for (let id in this.achievements) {
            const progress = document.querySelector(`[data-id="${id}"] .progress`);
            if (progress) {
                let percent = 0;
                switch(id) {
                    case 'first-pc':
                        percent = Math.min(100, (this.pc / this.achievements[id].required) * 100);
                        break;
                    case 'clicker':
                        percent = Math.min(100, (this.totalClicks / this.achievements[id].required) * 100);
                        break;
                    case 'collector':
                        percent = Math.min(100, (totalUpgrades / this.achievements[id].required) * 100);
                        break;
                    case 'millionaire':
                        percent = Math.min(100, (this.pc / this.achievements[id].required) * 100);
                        break;
                }
                progress.style.width = percent + '%';
            }
        }
    }

    unlockAchievement(id) {
        console.log(`Achievement unlocked: ${id}`);
        this.achievements[id].checked = true;
        document.querySelector(`[data-id="${id}"]`).classList.add('unlocked');
        
        const popup = document.createElement('div');
        popup.className = 'achievement-popup';
        popup.innerHTML = `
            <span class="icon">🏆</span>
            <div>
                <strong>Succès débloqué !</strong><br>
                ${document.querySelector(`[data-id="${id}"] h3`).textContent}
            </div>
        `;
        document.body.appendChild(popup);
        setTimeout(() => popup.remove(), 5000);

        // Bonus de succès
        this.pcPerClick *= 1.1;
        this.updateDisplay();
    }

    switchSection(section) {
        document.querySelectorAll('.menu-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.menu === section);
        });

        document.querySelector('.game-section').classList.toggle('active', section === 'game');
        document.querySelector('.achievements-section').classList.toggle('active', section === 'achievements');
    }

    switchCategory(category) {
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });

        document.querySelectorAll('.shop-items').forEach(items => {
            items.classList.toggle('hidden', items.id !== category);
        });
    }

    spawnGoldenPC() {
        if (Math.random() > this.goldenChance) return;

        const golden = document.createElement('div');
        golden.className = 'golden-pc';
        golden.innerHTML = '💻';
        golden.style.left = Math.random() * (window.innerWidth - 100) + 'px';
        golden.style.top = Math.random() * (window.innerHeight - 100) + 'px';
        
        golden.onclick = () => {
            const bonus = Math.floor(Math.random() * 1000) + 500;
            this.pc += bonus;
            golden.remove();
            this.checkAchievements();
            this.updateDisplay();
        };
        
        this.goldenPcArea.appendChild(golden);
        setTimeout(() => golden.remove(), 10000);
    }

    updateDisplay() {
        this.pcCountEl.textContent = `${this.formatNumber(this.pc)} PC`;
        this.pcPerSecondEl.textContent = `${this.formatNumber(this.pcPerSecond)} PC/s`;
        this.multiplierEl.textContent = `x${this.multiplier} Multiplicateur`;

        document.querySelectorAll('.shop-item').forEach(item => {
            const type = item.dataset.item;
            const upgrade = this.upgrades[type];
            item.querySelector('.cost').textContent = `${this.formatNumber(upgrade.cost)} PC`;
            item.querySelector('.owned').textContent = upgrade.owned;
            item.classList.toggle('available', this.pc >= upgrade.cost);
        });
    }

    tick() {
        this.pc = Math.round((this.pc + this.pcPerSecond) * 100) / 100;
        this.checkAchievements();
        this.updateDisplay();
        this.spawnGoldenPC();
    }

    formatNumber(num) {
        if (num < 1000) return Math.round(num * 100) / 100;
        if (num >= 1e12) return (num / 1e12).toFixed(2) + 'T';
        if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
        if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
        if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
        return Math.floor(num);
    }

    save() {
        localStorage.setItem('pcClickerSave', JSON.stringify({
            pc: this.pc,
            pcPerClick: this.pcPerClick,
            pcPerSecond: this.pcPerSecond,
            multiplier: this.multiplier,
            totalClicks: this.totalClicks,
            upgrades: this.upgrades,
            achievements: this.achievements
        }));
    }

    load() {
        const save = localStorage.getItem('pcClickerSave');
        if (!save) return;

        const data = JSON.parse(save);
        this.pc = data.pc;
        this.pcPerClick = data.pcPerClick;
        this.pcPerSecond = data.pcPerSecond;
        this.multiplier = data.multiplier;
        this.totalClicks = data.totalClicks || 0;
        this.upgrades = data.upgrades;
        
        if (data.achievements) {
            this.achievements = data.achievements;
            for (let id in this.achievements) {
                if (this.achievements[id].checked) {
                    document.querySelector(`[data-id="${id}"]`)?.classList.add('unlocked');
                }
            }
        }
        
        this.updateDisplay();
        this.checkAchievements();
    }

    reset() {
        if (confirm('Voulez-vous vraiment tout recommencer ?')) {
            localStorage.removeItem('pcClickerSave');
            location.reload();
        }
    }
}

new PCClicker();

// --- Variables principales ---
let pcCount = 0;
let pcPerClick = 1;
let clickCount = 0;
let achievements = {
    "first-pc": false,
    "beginner-clicker": false,
    "hardware-fan": false,
    "click-master": false,
    "golden-hunter": false,
    "software-expert": false,
    "millionaire": false,
    "network-king": false,
    "legendary": false
};

// --- DOM Elements ---
const pcCountEl = document.getElementById('pc-count');
const pcPerSecondEl = document.getElementById('pc-per-second');
const multiplierEl = document.getElementById('multiplier');
const clickablePc = document.querySelector('.clickable-pc');
const menuBtns = document.querySelectorAll('.menu-btn');
const gameSection = document.querySelector('.game-section');
const achievementsSection = document.querySelector('.achievements-section');
const achievementEls = document.querySelectorAll('.achievement');
const saveBtn = document.getElementById('save');
const resetBtn = document.getElementById('reset');

// --- Fonctions utilitaires ---
function updateStats() {
    pcCountEl.textContent = `${pcCount} PC`;
    pcPerSecondEl.textContent = `0 PC/s`;
    multiplierEl.textContent = `x${pcPerClick} Multiplicateur`;
}

function unlockAchievement(id) {
    if (!achievements[id]) {
        achievements[id] = true;
        const el = document.querySelector(`.achievement[data-id="${id}"]`);
        if (el) el.classList.add('unlocked');
        showAchievementPopup(id);
        updateAchievementsHeader();
    }
}

function updateAchievementsHeader() {
    const unlocked = Object.values(achievements).filter(Boolean).length;
    const total = Object.keys(achievements).length;
    const h2 = achievementsSection.querySelector('h2');
    if (h2) h2.textContent = `🏆 Succès (${unlocked}/${total})`;
}

function showAchievementPopup(id) {
    const el = document.querySelector(`.achievement[data-id="${id}"]`);
    if (!el) return;
    const icon = el.querySelector('.achievement-icon').textContent;
    const title = el.querySelector('h3').textContent;
    const reward = el.querySelector('.reward').textContent;
    const popup = document.createElement('div');
    popup.className = 'achievement-popup';
    popup.innerHTML = `<span class="icon">${icon}</span>
        <div>
            <div><strong>${title}</strong></div>
            <div class="reward">${reward}</div>
        </div>`;
    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 5000);
}

// --- Gestion des menus ---
menuBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        menuBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        if (btn.dataset.menu === 'game') {
            gameSection.classList.add('active');
            achievementsSection.classList.add('hidden');
        } else {
            gameSection.classList.remove('active');
            achievementsSection.classList.remove('hidden');
        }
    });
});

// --- Gestion du clic principal ---
if (clickablePc) {
    clickablePc.addEventListener('click', () => {
        pcCount += pcPerClick;
        clickCount++;
        updateStats();
        // Succès : premier PC
        if (pcCount >= 1) unlockAchievement('first-pc');
        // Succès : 100 clics
        if (clickCount >= 100) unlockAchievement('beginner-clicker');
        // Succès : 1000 clics
        if (clickCount >= 1000) unlockAchievement('click-master');
        // Succès : 1M PC
        if (pcCount >= 1000000) unlockAchievement('millionaire');

        // Progress bar
        achievementEls.forEach(el => {
            const id = el.dataset.id;
            const progress = el.querySelector('.progress');
            if (!progress) return;
            if (id === 'beginner-clicker') {
                progress.style.width = Math.min(100, clickCount / 100 * 100) + '%';
            }
            if (id === 'click-master') {
                progress.style.width = Math.min(100, clickCount / 1000 * 100) + '%';
            }
            if (id === 'first-pc') {
                progress.style.width = Math.min(100, pcCount * 100) + '%';
            }
            if (id === 'millionaire') {
                progress.style.width = Math.min(100, pcCount / 1000000 * 100) + '%';
            }
        });
    });
}

// --- Gestion de la boutique (exemple minimal) ---
document.querySelectorAll('.shop-item').forEach(item => {
    item.addEventListener('click', () => {
        const costEl = item.querySelector('.cost');
        let cost = parseInt(costEl.textContent);
        if (pcCount >= cost) {
            pcCount -= cost;
            let owned = item.querySelector('.owned');
            owned.textContent = parseInt(owned.textContent) + 1;
            pcPerClick += 0.1; // exemple
            updateStats();
            // Succès : 5 hardware
            if (item.dataset.item === 'mouse' && parseInt(owned.textContent) >= 5) {
                unlockAchievement('hardware-fan');
            }
        }
    });
});

// --- Gestion des catégories boutique ---
document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.shop-items').forEach(s => s.classList.add('hidden'));
        document.getElementById(btn.dataset.category).classList.remove('hidden');
    });
});

// --- Sauvegarde/Reset (localStorage) ---
saveBtn.addEventListener('click', () => {
    localStorage.setItem('pcClickerSave', JSON.stringify({
        pcCount, pcPerClick, clickCount, achievements
    }));
    alert('Sauvegardé !');
});
resetBtn.addEventListener('click', () => {
    if (confirm('Réinitialiser la partie ?')) {
        localStorage.removeItem('pcClickerSave');
        location.reload();
    }
});

// --- Chargement sauvegarde ---
window.addEventListener('DOMContentLoaded', () => {
    const save = localStorage.getItem('pcClickerSave');
    if (save) {
        const data = JSON.parse(save);
        pcCount = data.pcCount || 0;
        pcPerClick = data.pcPerClick || 1;
        clickCount = data.clickCount || 0;
        achievements = data.achievements || achievements;
        Object.keys(achievements).forEach(id => {
            if (achievements[id]) unlockAchievement(id);
        });
        updateStats();
    }
    updateStats();
    updateAchievementsHeader();
});