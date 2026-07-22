const input = document.getElementById('searchPatient');
const resultBox = document.getElementById('result');

if (input) {

    input.addEventListener('keyup', async function () {

        const q = this.value.trim();

        if (q.length < 1) {
            resultBox.innerHTML = '';
            return;
        }

        try {

            const response = await fetch(
                `/Team5-Clinic/api/search_patients.php?q=${encodeURIComponent(q)}`
            );

            const data = await response.json();

            if (data.length === 0) {
                resultBox.innerHTML =
                    '<div class="search-item">No patient found</div>';
                return;
            }

            resultBox.innerHTML = data.map(p => `
                <div class="search-item">
                    <div class="search-name">👤 ${p.name}</div>
                    <div class="search-phone">📞 ${p.phone}</div>
                </div>
            `).join('');

        } catch (err) {

            resultBox.innerHTML =
                '<div class="search-item">Search error</div>';
        }
    });
}