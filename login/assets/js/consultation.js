/**
 * PALMED Clinic - Consultation Module JavaScript
 */

const ConsultationForm = {
    diagnosisIndex: 0,
    signaturePad: null,

    init() {
        this.form = document.getElementById('consultation-form');
        if (!this.form) return;

        this.diagnosisIndex = document.querySelectorAll('.diagnosis-row').length;
        this.initPatientSearch();
        this.initIcd10Search();
        this.initDiagnosisActions();
        this.initSignaturePad();
        this.initFormSubmit();
        this.initBmi();
    },

    initBmi() {
        const weight = document.getElementById('weight');
        const height = document.getElementById('height');
        const bmi = document.getElementById('bmi');
        if (!weight || !height || !bmi) return;

        const calc = () => {
            const w = parseFloat(weight.value);
            const h = parseFloat(height.value);
            if (w > 0 && h > 0) {
                bmi.value = (w / Math.pow(h / 100, 2)).toFixed(2);
            }
        };
        weight.addEventListener('input', calc);
        height.addEventListener('input', calc);
    },

    initPatientSearch() {
        const input = document.getElementById('patient_search');
        const results = document.getElementById('patient_search_results');
        const hiddenId = document.getElementById('patient_id');
        if (!input || !results) return;

        let timeout;
        input.addEventListener('input', () => {
            clearTimeout(timeout);
            const q = input.value.trim();
            if (q.length < 2) {
                results.classList.remove('show');
                return;
            }
            timeout = setTimeout(async () => {
                try {
                    const res = await fetch(`${window.PALMED_BASE_URL}/api/patients/search?q=${encodeURIComponent(q)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    results.innerHTML = '';
                    data.results.forEach(p => {
                        const item = document.createElement('div');
                        item.className = 'icd10-result-item';
                        item.innerHTML = `<strong>${p.name}</strong> — ${p.document}`;
                        item.addEventListener('click', () => {
                            hiddenId.value = p.id;
                            input.value = `${p.name} (${p.document})`;
                            results.classList.remove('show');
                        });
                        results.appendChild(item);
                    });
                    results.classList.add('show');
                } catch (e) {
                    console.error(e);
                }
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.classList.remove('show');
            }
        });
    },

    initIcd10Search() {
        document.addEventListener('input', (e) => {
            if (!e.target.classList.contains('icd10-search')) return;
            this.searchIcd10(e.target);
        });
    },

    async searchIcd10(input) {
        const q = input.value.trim();
        const resultsEl = input.parentElement.querySelector('.icd10-results');
        if (!resultsEl || q.length < 2) {
            if (resultsEl) resultsEl.classList.remove('show');
            return;
        }

        try {
            const res = await fetch(`${window.PALMED_BASE_URL}/api/icd10/search?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            resultsEl.innerHTML = '';
            data.results.forEach(item => {
                const div = document.createElement('div');
                div.className = 'icd10-result-item';
                const lang = document.documentElement.lang || 'es';
                const desc = lang === 'en' && item.description_en ? item.description_en : item.description_es;
                div.innerHTML = `<strong>${item.code}</strong> - ${desc}`;
                div.addEventListener('click', () => {
                    const row = input.closest('.diagnosis-row');
                    row.querySelector('.diagnosis-code').value = item.code;
                    row.querySelector('.diagnosis-description').value = desc;
                    row.querySelector('.diagnosis-icd10-id').value = item.id;
                    input.value = item.code;
                    resultsEl.classList.remove('show');
                });
                resultsEl.appendChild(div);
            });
            resultsEl.classList.add('show');
        } catch (e) {
            console.error(e);
        }
    },

    initDiagnosisActions() {
        document.getElementById('add-diagnosis')?.addEventListener('click', () => this.addDiagnosisRow());
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-diagnosis')) {
                e.target.closest('.diagnosis-row')?.remove();
            }
        });
    },

    addDiagnosisRow() {
        const container = document.getElementById('diagnoses-container');
        const idx = this.diagnosisIndex++;
        const row = document.createElement('div');
        row.className = 'diagnosis-row row g-2 mb-2 align-items-end';
        row.innerHTML = `
            <div class="col-md-2 position-relative">
                <input type="text" class="form-control icd10-search" placeholder="CIE-10" autocomplete="off">
                <div class="icd10-results"></div>
                <input type="hidden" name="diagnosis_code[]" class="diagnosis-code">
                <input type="hidden" name="diagnosis_icd10_id[]" class="diagnosis-icd10-id">
            </div>
            <div class="col-md-8">
                <input type="text" name="diagnosis_description[]" class="form-control diagnosis-description" placeholder="Descripción del diagnóstico" required>
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input type="radio" name="diagnosis_primary" value="${idx}" class="form-check-input" id="primary_${idx}">
                    <label class="form-check-label small" for="primary_${idx}">P</label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-diagnosis">&times;</button>
            </div>
        `;
        container.appendChild(row);
    },

    initSignaturePad() {
        const canvas = document.getElementById('signature-canvas');
        const hidden = document.getElementById('digital_signature');
        if (!canvas || !hidden) return;

        const ctx = canvas.getContext('2d');
        let drawing = false;

        const resize = () => {
            const rect = canvas.parentElement.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = 150;
            ctx.strokeStyle = '#1a2b3c';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
        };
        resize();
        window.addEventListener('resize', resize);

        const getPos = (e) => {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        };

        const start = (e) => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); e.preventDefault(); };
        const draw = (e) => {
            if (!drawing) return;
            const p = getPos(e);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            e.preventDefault();
        };
        const stop = () => {
            if (drawing) {
                drawing = false;
                hidden.value = canvas.toDataURL('image/png');
            }
        };

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stop);
        canvas.addEventListener('mouseleave', stop);
        canvas.addEventListener('touchstart', start);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stop);

        document.getElementById('clear-signature')?.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hidden.value = '';
        });
    },

    initFormSubmit() {
        const saveBtn = document.getElementById('save-consultation');
        if (!saveBtn) return;

        saveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const formData = new FormData(this.form);
            const action = this.form.action;
            const method = this.form.method || 'POST';

            saveBtn.disabled = true;
            saveBtn.textContent = 'Guardando...';

            try {
                const res = await fetch(action, {
                    method,
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.success) {
                    Palmed.showToast(data.message, 'success');
                    if (data.id && !action.includes('/consultations/' + data.id)) {
                        window.history.replaceState({}, '', `${window.PALMED_BASE_URL}/consultations/${data.id}/edit`);
                        this.form.action = `${window.PALMED_BASE_URL}/consultations/${data.id}`;
                    }
                } else {
                    Palmed.showToast(data.message || 'Error al guardar', 'error');
                }
            } catch (err) {
                Palmed.showToast('Error de conexión', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar consulta';
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => ConsultationForm.init());
