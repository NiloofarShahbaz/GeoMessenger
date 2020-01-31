let options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };

function hideOrRevealPassword() {
    let input = document.getElementById('loginPass');
    let icon = document.getElementById('passIcon');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

function changeDropdownIcon() {
    let dropdownIcon = document.getElementById('dropdown-icon');
    dropdownIcon.classList.toggle('fa-map-marker');
    dropdownIcon.classList.toggle('fa-spinner');
    dropdownIcon.classList.toggle('fa-spin');
    showOnMap();
}

function checkChangeDropdownIcon() {
    let dropdownIcon = document.getElementById('dropdown-icon');
    if (dropdownIcon){
        dropdownIcon.classList.remove('fa-spinner');
        dropdownIcon.classList.remove('fa-spin');
        dropdownIcon.classList.add('fa-map-marker');
    }
}

function createPopupContent(username, online, status, location_dateTime, request) {
    let html = '<div class="popup_content"><div>';
    html += '<div class="popup_username mb-2">'+ username ;
    if(online)
        html += '<i class="ml-1 fa-xs fas fa-circle" style="color: lawngreen;padding-bottom: 1px;"></i>';
    else
        html += '<i class="ml-1 fa-xs fas fa-circle" style="color: red"></i>';
    html += '</div>';
    if (request)
        html += '<button class="popup_btn" type="button">request</button>';
    html += '</div>';
    html += '<div class="popup_status">' + status + '</div>';
    let date = new Date(location_dateTime);
    html += '<div class="popup_datetime mt-2"><i class="far fa-calendar-alt" style="margin-right: 3px;"></i>'+
        date.toLocaleDateString('en-Us', options) + '</div>';
    html += '</div>';
    return html;
}

function goToChatRoom(user_id) {
    let elem = $('#friends');
    elem.html('').css('padding-left', '10px', 'padding-right', '10px');

}