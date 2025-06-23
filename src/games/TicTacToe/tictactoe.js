class TicTacToe {
    constructor() {
        this.board = Array(9).fill('');
        this.currentPlayer = 'X';
        this.gameMode = null;
        this.difficulty = null;
        this.gameActive = false;
        this.slidingMode = false;
        this.pieces = {
            X: [], // [positions en ordre de plus récent à plus ancien]
            O: []
        };

        this.mainMenu = document.getElementById('mainMenu');
        this.difficultyMenu = document.getElementById('difficultyMenu');
        this.gameBoard = document.getElementById('gameBoard');
        this.status = document.getElementById('gameStatus');
        this.cells = document.querySelectorAll('.cell');

        this.setupEventListeners();
    }

    setupEventListeners() {
        document.getElementById('vsPlayerBtn').addEventListener('click', () => {
            this.startGame('player');
        });

        document.getElementById('vsAiBtn').addEventListener('click', () => {
            this.showDifficultyMenu();
        });

        document.getElementById('slidingModeBtn').addEventListener('click', () => {
            this.showSlidingModeMenu();
        });

        document.getElementById('vsPlayerSlidingBtn').addEventListener('click', () => {
            this.startGame('sliding-player');
        });

        document.getElementById('vsAiSlidingBtn').addEventListener('click', () => {
            this.showDifficultyMenu('sliding-ai');
        });

        document.querySelectorAll('#difficultyMenu .menu-btn[data-difficulty]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.difficulty = btn.dataset.difficulty;
                const mode = this.slidingMode ? 'sliding-ai' : 'ai';
                this.startGame(mode);
            });
        });

        document.querySelectorAll('.back-btn').forEach(btn => {
            btn.addEventListener('click', () => this.showMainMenu());
        });

        this.cells.forEach(cell => {
            cell.addEventListener('click', () => {
                if (this.gameActive) {
                    this.handleCellClick(parseInt(cell.dataset.index));
                }
            });
        });

        document.getElementById('restartBtn').addEventListener('click', () => {
            this.resetGame();
        });
    }

    startGame(mode) {
        console.log('Starting game:', mode);
        this.gameMode = mode;
        this.gameActive = true;
        this.board = Array(9).fill('');
        this.currentPlayer = 'X';
        this.slidingMode = mode.startsWith('sliding-');
        this.pieces = { X: [], O: [] };
        
        this.cells.forEach(cell => {
            cell.className = 'cell';
            cell.textContent = '';
        });
        
        this.updateStatus();
        this.showGameBoard();
    }

    handleCellClick(index) {
        if (!this.gameActive || this.board[index] !== '') return;
        this.placePiece(index);
    }

    placePiece(index) {
        if (this.slidingMode && this.pieces[this.currentPlayer].length >= 3) {
            const oldPos = this.pieces[this.currentPlayer].pop();
            this.board[oldPos] = '';
            this.cells[oldPos].textContent = '';
            this.cells[oldPos].className = 'cell';
        }

        this.board[index] = this.currentPlayer;
        this.cells[index].textContent = this.currentPlayer;
        this.pieces[this.currentPlayer].unshift(index);

        this.updatePieceAges();

        if (!this.checkGameEnd()) {
            this.switchPlayer();
        }
    }

    makeAiMove() {
        if (!this.gameActive || this.currentPlayer !== 'O') return;

        console.log('AI making move:', {
            difficulty: this.difficulty,
            slidingMode: this.slidingMode,
            currentPieces: this.pieces['O']
        });

        setTimeout(() => {
            let move;

            if (this.difficulty === 'hard') {
                move = this.findBestMove();
            }
            else if (this.difficulty === 'medium') {
                move = Math.random() < 0.7 ? this.findBestMove() : this.getRandomMove();
            }
            else {
                move = this.getRandomMove();
            }

            if (move !== null) {
                this.placePiece(move);
            }
        }, 500);
    }

    findBestMove() {
        // 1. Chercher une victoire
        const winMove = this.findWinningMove('O');
        if (winMove !== null) return winMove;

        // 2. Bloquer une victoire adverse
        const blockMove = this.findWinningMove('X');
        if (blockMove !== null) return blockMove;

        // 3. Stratégies selon le mode
        if (this.slidingMode) {
            return this.findBestSlidingMove();
        } else {
            return this.findBestClassicMove();
        }
    }

    findBestClassicMove() {
        // Centre
        if (this.board[4] === '') return 4;

        // Coins
        const corners = [0, 2, 6, 8];
        for (let i of corners) {
            if (this.board[i] === '') return i;
        }

        // Bords
        const edges = [1, 3, 5, 7];
        for (let i of edges) {
            if (this.board[i] === '') return i;
        }

        return this.getRandomMove();
    }

    findBestSlidingMove() {
        // Avec moins de 3 pièces
        if (this.pieces['O'].length < 3) {
            // Centre
            if (this.board[4] === '') return 4;
            
            // Coins
            const corners = [0, 2, 6, 8];
            for (let i of corners) {
                if (this.board[i] === '') return i;
            }
        }

        // Chercher la meilleure position
        let bestScore = -Infinity;
        let bestMove = null;

        const emptySpots = Array.from({length: 9})
            .map((_, i) => i)
            .filter(i => this.board[i] === '');

        for (let spot of emptySpots) {
            this.board[spot] = 'O';
            const score = this.evaluatePosition();
            this.board[spot] = '';

            if (score > bestScore) {
                bestScore = score;
                bestMove = spot;
            }
        }

        return bestMove || this.getRandomMove();
    }

    findWinningMove(player) {
        for (let i = 0; i < 9; i++) {
            if (this.board[i] === '') {
                this.board[i] = player;
                if (this.checkWin()) {
                    this.board[i] = '';
                    return i;
                }
                this.board[i] = '';
            }
        }
        return null;
    }

    evaluatePosition() {
        let score = 0;
        const lines = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6]
        ];

        // Points pour les alignements
        for (let line of lines) {
            const symbols = line.map(i => this.board[i]);
            const oCount = symbols.filter(s => s === 'O').length;
            const xCount = symbols.filter(s => s === 'X').length;
            const emptyCount = symbols.filter(s => s === '').length;

            if (oCount === 2 && emptyCount === 1) score += 10;
            if (oCount === 1 && emptyCount === 2) score += 3;
            if (xCount === 2 && emptyCount === 1) score += 8;
        }

        // Points pour les positions stratégiques
        if (this.board[4] === 'O') score += 3;
        for (let corner of [0, 2, 6, 8]) {
            if (this.board[corner] === 'O') score += 2;
        }

        return score;
    }

    getRandomMove() {
        const emptySpots = Array.from({length: 9})
            .map((_, i) => i)
            .filter(i => this.board[i] === '');

        if (emptySpots.length === 0) return null;
        return emptySpots[Math.floor(Math.random() * emptySpots.length)];
    }

    updatePieceAges() {
        ['X', 'O'].forEach(player => {
            this.pieces[player].forEach((pos, index) => {
                const cell = this.cells[pos];
                cell.className = 'cell';
                cell.classList.add(player.toLowerCase(), `age-${Math.min(index, 2)}`);
            });
        });
    }

    checkGameEnd() {
        if (this.checkWin()) {
            this.gameActive = false;
            this.status.textContent = `Joueur ${this.currentPlayer} gagne !`;
            this.highlightWinningCells();
            return true;
        }

        if (!this.slidingMode && !this.board.includes('')) {
            this.gameActive = false;
            this.status.textContent = "Match nul !";
            return true;
        }

        return false;
    }

    checkWin() {
        const lines = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6]
        ];

        for (let line of lines) {
            if (this.board[line[0]] &&
                this.board[line[0]] === this.board[line[1]] &&
                this.board[line[0]] === this.board[line[2]]) {
                this.winningLine = line;
                return true;
            }
        }
        return false;
    }

    highlightWinningCells() {
        if (this.winningLine) {
            this.winningLine.forEach(index => {
                this.cells[index].classList.add('win');
            });
        }
    }

    switchPlayer() {
        this.currentPlayer = this.currentPlayer === 'X' ? 'O' : 'X';
        this.updateStatus();

        // Vérifier si c'est au tour de l'IA
        console.log('Checking AI turn:', {
            mode: this.gameMode,
            player: this.currentPlayer,
            isAiMode: this.gameMode === 'ai' || this.gameMode === 'sliding-ai'
        });

        if ((this.gameMode === 'ai' || this.gameMode === 'sliding-ai') && 
            this.currentPlayer === 'O') {
            console.log('AI will play...');
            this.makeAiMove();
        }
    }

    updateStatus() {
        this.status.textContent = `Tour du Joueur ${this.currentPlayer}`;
    }

    showMainMenu() {
        document.querySelectorAll('.menu, .game-board, #gameOver').forEach(el => {
            el.classList.add('hidden');
        });
        this.mainMenu.classList.remove('hidden');
    }

    showDifficultyMenu(mode) {
        document.querySelectorAll('.menu, .game-board').forEach(el => {
            el.classList.add('hidden');
        });
        this.difficultyMenu.classList.remove('hidden');
        this.slidingMode = mode === 'sliding-ai';
    }

    showSlidingModeMenu() {
        document.querySelectorAll('.menu, .game-board').forEach(el => {
            el.classList.add('hidden');
        });
        document.getElementById('slidingModeMenu').classList.remove('hidden');
    }

    showGameBoard() {
        document.querySelectorAll('.menu').forEach(el => {
            el.classList.add('hidden');
        });
        this.gameBoard.classList.remove('hidden');
    }

    resetGame() {
        this.startGame(this.gameMode);
    }
}

window.addEventListener('load', () => {
    new TicTacToe();
});