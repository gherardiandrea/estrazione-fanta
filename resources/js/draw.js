import './bootstrap';

function setButtonsDisabled(disabled) {
    const drawButton = document.getElementById('draw-team-button');
    const resetButton = document.getElementById('reset-cycle-button');

    if (drawButton) drawButton.disabled = disabled;
    if (resetButton) resetButton.disabled = disabled;
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

    const drawButton = document.getElementById('draw-team-button');
    const resetButton = document.getElementById('reset-cycle-button');
    const drawnTeam = document.getElementById('drawn-team');
    const drawNumber = document.getElementById('draw-number');
    const completedCycles = document.getElementById('completed-cycles');

    if (!drawButton || !resetButton || !drawUrl || !resetUrl) {
        return;
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
            if (completedCycles) completedCycles.textContent = 'Cicli completati: 0';

            renderRemainingTeams(data.remainingTeams || []);
            setFeedback('Reset completato.', 'success');
        } catch (error) {
            setFeedback(error.message || 'Errore durante il reset. Riprova.', 'error');
        } finally {
            setButtonsDisabled(false);
        }
    });
});
