$(document).ready(function () {
    let getUserLocations = function () {
        let sessionId = get_session_id();
        $.ajax({
            type: "GET",
            url: "sendUserLocations.php",
            dataType: 'text',
            data: {'value': 1, 'id': sessionId}
        }).done(function (data) {
            $('#myLocations').append(data);
        })
    };
    getUserLocations();

    $('#mapForm').submit(function (event) {
        $('#form-button-icon').removeClass('fa-share').addClass('fa-circle-notch fa-spin');
        $('#form-result').removeClass('mt-1 mb-1').remove('color', 'red');
        $('#form-result-status').text('');
        $('#form-result-icon').removeClass('fa-exclamation-circle');


        let latitude = $('#lat');
        let longitude = $('#lng');
        (async () => {
            while (!latitude.val() || !longitude.val()) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            latitude = latitude.val();
            longitude = longitude.val();
            let formData = {
                'latitude': latitude,
                'longitude': longitude,
                'status': $('textarea[name=status]').val()
            };

            $.ajax({
                type: 'POST',
                url: 'shareLocationForm.php',
                data: formData,
                dataType: 'json',
            })
                .done(function (data) {
                    $('#form-button-icon').removeClass('fa-circle-notch fa-spin').addClass('fa-share');
                    $('#form-result').addClass('mt-1 mb-1');
                    $('#form-result-status').text(data['message']);

                    if (!data.success) {
                        $('#form-result-icon').addClass('fa-exclamation-circle');
                        $('#form-result').css('color', 'red');
                    } else {
                        $('#form-result-icon').addClass('fa-check-circle');
                        $('#form-result').css('color', 'green');
                        $("#dropdown").removeClass("dropdown-show");
                        checkChangeDropdownIcon();
                        window.location = 'index.php'
                    }
                });

        })();
        event.preventDefault();
    });
    $('select').on('change', function () {
        markers.clearLayers();

        let icon = $('#mapFilterStatusIcon');
        let span = $('#mapFilterStatus');
        icon.removeClass('fa-check-circle').removeClass('fa-exclamation-circle');
        span.text('');
        let value = this.value;
        if (value === '5') { //All
            icon.removeClass('fa-check-circle').removeClass('fa-exclamation-circle');
            span.text('');

            $.ajax({
                type: "GET",
                url: "sendUserLocations.php",
                dataType: 'json',
                data: {'value': 5}
            }).done(function (data) {
                span.text(data.message);
                if (!data.success) {
                    span.parent().css('color', 'red');
                    icon.addClass('fa-exclamation-circle');
                } else {
                    span.parent().css('color', 'green');
                    icon.addClass('fa-check-circle');

                    let result = data.result;
                    let thisUserId = get_session_id();

                    $.ajax({
                        type: "GET",
                        url: "sendUserFriends.php",
                        dataType: 'json',
                    }).done(function (data) {
                        if (!data.success) {
                            span.text(data.message);
                            span.parent().css('color', 'red');
                            icon.addClass('fa-exclamation-circle');
                        } else {
                            let friends = data.result;
                            setMarkers(result, thisUserId, friends, value);
                        }
                    })

                }
            })
        }
        else if (value === '4') {
            $.ajax({
                type: "GET",
                url: "sendUserLocations.php",
                dataType: 'json',
                data: {'value': 4}
            }).done(function (data) {
                span.text(data.message);
                if (!data.success) {
                    span.parent().css('color', 'red');
                    icon.addClass('fa-exclamation-circle');
                } else {
                    span.parent().css('color', 'green');
                    icon.addClass('fa-check-circle');

                    let result = data.result;
                    setMarkers(result, undefined, undefined, value);
                }
            })
        }
        else if (value === '3') {
            $.ajax({
                type: "GET",
                url: "sendUserLocations.php",
                dataType: 'json',
                data: {'value': 3}
            }).done(function (data) {
                span.text(data.message);
                if (!data.success) {
                    span.parent().css('color', 'red');
                    icon.addClass('fa-exclamation-circle');
                } else {
                    span.parent().css('color', 'green');
                    icon.addClass('fa-check-circle');

                    let result = data.result;
                    setMarkers(result, undefined, result, value, true);
                }
            })
        }
        else if (value === '2') {
            $.ajax({
                type: "GET",
                url: "sendUserLocations.php",
                dataType: 'json',
                data: {'value': 2}
            }).done(function (data) {
                span.text(data.message);
                if (!data.success) {
                    span.parent().css('color', 'red');
                    icon.addClass('fa-exclamation-circle');
                } else {
                    span.parent().css('color', 'green');
                    icon.addClass('fa-check-circle');

                    let result = data.result;
                    setMarkers(result, get_session_id(), undefined, value);
                }
            })
        }
    });

});

function setMarkers(result, thisUserId, friends, value, resultIsFriends) {
    let marker;
    for (let i = 0; i < result.length; i++) {
        if ((value === '5' || value === '2') && thisUserId == result[i]['user_id']) {
            // this user location only
            marker = new L.marker([result[i]['location_lat'], result[i]['location_lng']], {icon: myPosIcon})
                .bindPopup(createPopupContent('Me!', true, result[i]['location_status'],
                    result[i]['location_dateTime'], false));
        } else if ((value === '5' || value === '3') && (friends.includes(result[i]['user_id']) || resultIsFriends)) {
            marker = new L.marker([result[i]['location_lat'], result[i]['location_lng']], {icon: myFriendsPosIcon})
                .bindPopup(createPopupContent(result[i]['username'], result[[i]['user_status']],
                    result[i]['location_status'], result[i]['location_dateTime'], false));
        } else if (value === '5' || value === '4') {
            // others
            marker = new L.marker([result[i]['location_lat'], result[i]['location_lng']])
                .bindPopup(createPopupContent(result[i]['username'], result[[i]['user_status']],
                    result[i]['location_status'], result[i]['location_dateTime'], true));
        }
        markers.addLayer(marker);
    }

}