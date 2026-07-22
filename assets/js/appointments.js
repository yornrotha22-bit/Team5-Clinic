const searchInput = document.getElementById('searchPatient');
const tableBody = document.getElementById('appointmentsTable');

if(searchInput && tableBody){

    searchInput.addEventListener('keyup', function(){

        const value = this.value.toLowerCase();

        tableBody.querySelectorAll('tr').forEach(row => {

            row.style.display =
                row.innerText.toLowerCase().includes(value)
                    ? ''
                    : 'none';
        });
    });
}