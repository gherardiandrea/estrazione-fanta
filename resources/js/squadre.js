import './bootstrap';

function setButtonsDisabled(disabled) {
    const estraiButton = document.getElementById('estrai-squadra');
    const resetButton = document.getElementById('reset-squadre');

    if (estraiButton) estraiButton.disabled = disabled;
    if (resetButton) resetButton.disabled = disabled;
}

function setFeedback(message, type) {
    const feedback = document.getElementById('feedback');
    if (!feedback) return;

    feedback.classList.remove('info', 'success', 'error');
    feedback.classList.add(type);
    feedback.textContent = message;
}

function renderSquadreRestanti(squadreRestanti) {
    const list = document.getElementById('squadre-restanti');
    if (!list) return;

    list.innerHTML = '';

    if (!squadreRestanti.length) {
        const empty = document.createElement('li');
        empty.className = 'empty-state';
        empty.textContent = 'Nessuna squadra rimanente: al prossimo click parte un nuovo ciclo.';
        list.appendChild(empty);
        return;
    }

    squadreRestanti.forEach((squadra) => {
        const li = document.createElement('li');
        li.textContent = squadra;
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
        throw new Error('Request failed');
    }

    return response.json();
}

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const estraiUrl = body.dataset.estraiUrl;
    const resetUrl = body.dataset.resetUrl;

    const estraiButton = document.getElementById('estrai-squadra');
    const resetButton = document.getElementById('reset-squadre');
    const squadraEstratta = document.getElementById('squadra-estratta');
    const numeroEstrazione = document.getElementById('numero-estrazione');
    const cicliCompletati = document.getElementById('cicli-completati');

    if (!estraiButton || !resetButton || !estraiUrl || !resetUrl) {
        return;
    }

    estraiButton.addEventListener('click', async () => {
        try {
            setButtonsDisabled(true);
            setFeedback('Estrazione in corso...', 'info');

            const data = await postJson(estraiUrl);

            if (squadraEstratta) squadraEstratta.textContent = data.squadra;
            if (numeroEstrazione) {
                numeroEstrazione.textContent = data.numeroEstrazione > 0
                    ? `${data.numeroEstrazione}° squadra estratta`
                    : '';
            }
            if (cicliCompletati) {
                cicliCompletati.textContent = `Cicli completati: ${data.cicliCompletati}`;
            }

            renderSquadreRestanti(data.squadreRestanti || []);
            setFeedback('Estrazione completata con successo.', 'success');
        } catch (error) {
            setFeedback("Errore durante l'estrazione. Riprova.", 'error');
        } finally {
            setButtonsDisabled(false);
        }
    });

    resetButton.addEventListener('click', async () => {
        try {
            setButtonsDisabled(true);
            setFeedback('Reset in corso...', 'info');

            const data = await postJson(resetUrl);

            if (squadraEstratta) squadraEstratta.textContent = 'Nessuna squadra estratta';
            if (numeroEstrazione) numeroEstrazione.textContent = '';
            if (cicliCompletati) cicliCompletati.textContent = 'Cicli completati: 0';

            renderSquadreRestanti(data.squadreRestanti || []);
            setFeedback('Reset completato.', 'success');
        } catch (error) {
            setFeedback('Errore durante il reset. Riprova.', 'error');
        } finally {
            setButtonsDisabled(false);
        }
    });
});
