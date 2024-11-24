
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        if (dropdown.style.display === 'none') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    // Opcional: cerrar el menú si se hace clic fuera
    window.onclick = function(event) {
        const dropdown = document.getElementById('dropdown');
        if (!event.target.matches('.icon')) {
            dropdown.style.display = 'none';
        }
    }


        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }

        window.onclick = function(event) {
            const dropdown = document.getElementById('dropdown');
            if (!event.target.matches('.icon')) {
                dropdown.style.display = 'none';
            }
        }



