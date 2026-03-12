import './bootstrap';

function setButtonsDisabled(disabled) {
    const drawButton = document.getElementById('draw-team-button');
    const resetButton = document.getElementById('reset-cycle-button');
    const clearHistoryButton = document.getElementById('clear-history-button');

    if (drawButton) drawButton.disabled = disabled;
    if (resetButton) resetButton.disabled = disabled;
    if (clearHistoryButton) clearHistoryButton.disabled = disabled;
}

function setFeedback(message, type) {
    const feedback = document.getElementById('feedback');
    if (!feedback) return;

    feedback.classList.remove('info', 'success', 'error');
    feedback.classList.add(type);
    feedback.textContent = message;
}

function renderRemainingTeams(remainingTeams) {
    const list = document.getElementById('remaining-teams');
    if (!list) return;

    list.innerHTML = '';

    if (!remainingTeams.length) {
        const empty = document.createElement('li');
        empty.className = 'empty-state';
        empty.textContent = 'Nessuna squadra rimanente: al prossimo click parte un nuovo ciclo.';
        list.appendChild(empty);
        return;
    }

    remainingTeams.forEach((team) => {
        const li = document.createElement('li');
        li.textContent = team;
        list.appendChild(li);
    });
}

function renderDrawHistory(drawHistory) {
    const list = document.getElementById('draw-history');
    if (!list) return;

    list.innerHTML = '';

    if (!drawHistory.length) {
        const empty = document.createElement('li');
        empty.className = 'empty-state';
        empty.textContent = 'Ancora nessuna estrazione registrata.';
        list.appendChild(empty);
        return;
    }

    drawHistory.forEach((entry) => {
        const li = document.createElement('li');

        const team = document.createElement('span');
        team.className = 'history-team';
        team.textContent = entry.team;

        const meta = document.createElement('span');
        meta.className = 'history-meta';
        meta.textContent = `${entry.drawNumber}° estrazione`;

        li.appendChild(team);
        li.appendChild(meta);
        list.appendChild(li);
    });
}

function parseJsonAttribute(value, fallback) {
    if (!value) return fallback;

    try {
        return JSON.parse(value);
    } catch (error) {
        return fallback;
    }
}

function normalizeCycle(cycle) {
    if (cycle === null || cycle === undefined || cycle === '') return null;

    const parsed = Number.parseInt(String(cycle), 10);
    return Number.isNaN(parsed) ? null : parsed;
}

function updateCycleSelect(cycleSelect, historyCycles, selectedCycle) {
    if (!cycleSelect) return;

    cycleSelect.innerHTML = '';

    if (!historyCycles.length) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Nessun ciclo disponibile';
        cycleSelect.appendChild(option);
        cycleSelect.disabled = true;
        return;
    }

    cycleSelect.disabled = false;

    historyCycles.forEach((cycle) => {
        const option = document.createElement('option');
        option.value = String(cycle);
        option.textContent = `Ciclo ${cycle}`;
        option.selected = cycle === selectedCycle;
        cycleSelect.appendChild(option);
    });
}

function resolveSelectedCycle(historyCycles, preferredCycle) {
    if (!historyCycles.length) return null;
    if (preferredCycle && historyCycles.includes(preferredCycle)) return preferredCycle;

    return historyCycles[0];
}

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

async function postJson(url) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({}),
    });

    if (!response.ok) {
        let message = 'Richiesta non riuscita.';

        try {
            const payload = await response.json();
            if (payload.message) {
                message = payload.message;
            }
        } catch (error) {
            // Keep default message when response is not JSON.
        }

        throw new Error(message);
    }

    return response.json();
}

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const drawUrl = body.dataset.drawUrl;
    const resetUrl = body.dataset.resetUrl;
    const clearHistoryUrl = body.dataset.clearHistoryUrl;

    let historyByCycle = parseJsonAttribute(body.dataset.historyByCycle, {});
    let historyCycles = parseJsonAttribute(body.dataset.historyCycles, []).map((cycle) => normalizeCycle(cycle)).filter((cycle) => cycle !== null);
    let selectedCycle = normalizeCycle(body.dataset.selectedHistoryCycle);

    selectedCycle = resolveSelectedCycle(historyCycles, selectedCycle);

    const drawButton = document.getElementById('draw-team-button');
    const resetButton = document.getElementById('reset-cycle-button');
    const clearHistoryButton = document.getElementById('clear-history-button');
    const drawnTeam = document.getElementById('drawn-team');
    const drawNumber = document.getElementById('draw-number');
    const completedCycles = document.getElementById('completed-cycles');
    const cycleSelect = document.getElementById('history-cycle-select');
    const historyToggle = document.getElementById('history-toggle');
    const historyContent = document.getElementById('history-content');
    const historyDialog = document.getElementById('clear-history-dialog');
    const confirmClearHistory = document.getElementById('confirm-clear-history');
    const cancelClearHistory = document.getElementById('cancel-clear-history');

    if (!drawButton || !resetButton || !drawUrl || !resetUrl) {
        return;
    }

    const renderHistoryForSelectedCycle = () => {
        selectedCycle = resolveSelectedCycle(historyCycles, selectedCycle);
        const cycleKey = selectedCycle !== null ? String(selectedCycle) : '';
        const selectedHistory = cycleKey ? (historyByCycle[cycleKey] || []) : [];

        updateCycleSelect(cycleSelect, historyCycles, selectedCycle);
        renderDrawHistory(selectedHistory);

        if (clearHistoryButton) {
            clearHistoryButton.disabled = selectedHistory.length === 0;
        }
    };

    const applyHistoryPayload = (data) => {
        if (data.historyByCycle && Array.isArray(data.historyCycles)) {
            historyByCycle = data.historyByCycle;
            historyCycles = data.historyCycles
                .map((cycle) => normalizeCycle(cycle))
                .filter((cycle) => cycle !== null);

            selectedCycle = resolveSelectedCycle(
                historyCycles,
                normalizeCycle(data.selectedHistoryCycle) ?? selectedCycle
            );
        }

        renderHistoryForSelectedCycle();
    };

    renderHistoryForSelectedCycle();

    if (historyToggle && historyContent) {
        historyToggle.addEventListener('click', () => {
            const expanded = historyToggle.getAttribute('aria-expanded') === 'true';
            historyToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            historyContent.hidden = expanded;
        });
    }

    if (cycleSelect) {
        cycleSelect.addEventListener('change', () => {
            selectedCycle = normalizeCycle(cycleSelect.value);
            renderHistoryForSelectedCycle();
        });
    }

    if (clearHistoryButton && historyDialog && confirmClearHistory && cancelClearHistory && clearHistoryUrl) {
        clearHistoryButton.addEventListener('click', () => {
            historyDialog.showModal();
        });

        cancelClearHistory.addEventListener('click', () => {
            historyDialog.close();
        });

        confirmClearHistory.addEventListener('click', async () => {
            try {
                setButtonsDisabled(true);
                setFeedback('Cancellazione storico in corso...', 'info');

                const data = await postJson(clearHistoryUrl);
                applyHistoryPayload(data);

                setFeedback('Storico cancellato.', 'success');
            } catch (error) {
                setFeedback(error.message || 'Errore durante la cancellazione dello storico.', 'error');
            } finally {
                historyDialog.close();
                setButtonsDisabled(false);
            }
        });
    }

    drawButton.addEventListener('click', async () => {
        try {
            setButtonsDisabled(true);
            setFeedback('Estrazione in corso...', 'info');

            const data = await postJson(drawUrl);

            if (drawnTeam) drawnTeam.textContent = data.team;
            if (drawNumber) {
                drawNumber.textContent = data.drawNumber > 0
                    ? `${data.drawNumber}° squadra estratta`
                    : '';
            }
            if (completedCycles) {
                completedCycles.textContent = `Cicli completati: ${data.completedCycles}`;
            }

            renderRemainingTeams(data.remainingTeams || []);
            applyHistoryPayload(data);
            setFeedback('Estrazione completata con successo.', 'success');
        } catch (error) {
            setFeedback(error.message || "Errore durante l'estrazione. Riprova.", 'error');
        } finally {
            setButtonsDisabled(false);
        }
    });

    resetButton.addEventListener('click', async () => {
        try {
            setButtonsDisabled(true);
            setFeedback('Reset in corso...', 'info');

            const data = await postJson(resetUrl);

            if (drawnTeam) drawnTeam.textContent = 'Nessuna squadra estratta';
            if (drawNumber) drawNumber.textContent = '';
            if (completedCycles) {
                const safeCompletedCycles = Number.isInteger(data.completedCycles)
                    ? data.completedCycles
                    : 0;
                completedCycles.textContent = `Cicli completati: ${safeCompletedCycles}`;
            }

            renderRemainingTeams(data.remainingTeams || []);
            applyHistoryPayload(data);
            setFeedback('Reset completato.', 'success');
        } catch (error) {
            setFeedback(error.message || 'Errore durante il reset. Riprova.', 'error');
        } finally {
            setButtonsDisabled(false);
        }
    });
});
