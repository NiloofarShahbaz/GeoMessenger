function dropdownClickEvent() {
    document.getElementById("dropdown").classList.toggle("dropdown-show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
    if (!(event.target.matches('.dropdown-btn') || event.target.parentNode.matches('.dropdown-content')
        || event.target.matches('.leaflet-marker-icon')
        || event.target.parentNode.parentNode.matches('.dropdown-content'))) {

        let dropdowns = document.getElementsByClassName("dropdown-content");
        let i;
        for (i = 0; i < dropdowns.length; i++) {
            let openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('dropdown-show')) {
                openDropdown.classList.remove('dropdown-show');
            }
        }
        checkChangeDropdownIcon();
    }
};