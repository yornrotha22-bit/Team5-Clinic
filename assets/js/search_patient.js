const searchInput = document.getElementById('searchPatient');
const tableBody = document.getElementById('appointmentsTable');

if(searchInput){
    searchInput.addEventListener('keyup', function(){

        const value = this.value.toLowerCase();

        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
}