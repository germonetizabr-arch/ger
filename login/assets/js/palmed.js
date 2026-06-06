/**
 * PALMED Clinic - Core JavaScript
 */

const Palmed = {
    init() {
        this.initAgeCalculator();
        this.initBmiCalculator();
        this.initAlerts();
    },

    initAgeCalculator() {
        const dobInput = document.getElementById('date_of_birth');
        const ageInput = document.getElementById('age');
        if (!dobInput || !ageInput) return;

        dobInput.addEventListener('change', () => {
            if (!dobInput.value) return;
            const dob = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            ageInput.value = age;
        });
    },

    initBmiCalculator() {
        const weightInput = document.getElementById('weight');
        const heightInput = document.getElementById('height');
        const bmiInput = document.getElementById('bmi');
        if (!weightInput || !heightInput || !bmiInput) return;

        const calculate = () => {
            const weight = parseFloat(weightInput.value);
            const height = parseFloat(heightInput.value);
            if (weight > 0 && height > 0) {
                const heightM = height / 100;
                bmiInput.value = (weight / (heightM * heightM)).toFixed(2);
            }
        };

        weightInput.addEventListener('input', calculate);
        heightInput.addEventListener('input', calculate);
    },

    initAlerts() {
        document.querySelectorAll('.alert-palmed[data-auto-dismiss]').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }, 5000);
        });
    },

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert-palmed alert-${type}`;
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:280px;';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
};

document.addEventListener('DOMContentLoaded', () => Palmed.init());
