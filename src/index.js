function volunteerRegistration(e) {
    e.preventDefault();
    var firstname = $("#txtFirstName").val();
    var middlename = $("#txtMiddleName").val();
    var lastname = $("#txtLastName").val();
    var gender = $("#txtGender").val();
    var age = $("#txtAge").val();
    var number = $("#txtNumber").val();
    var email = $("#txtEmail").val();
    var password = $("#txtPassword").val();

    // Clear previous messages
    var messageBox = $("#messageBox");
    messageBox.removeClass("success error").hide();

    $.post("../php/volunteerRegistration.php", {
        xfirstname : firstname,
        xmiddlename : middlename,
        xlastname : lastname,
        xgender : gender,
        xage : age,
        xnumber : number,
        xemail : email,
        xpassword : password,

    }, function (data) {
        console.log("Response from server:", data);
        try {
            let dataX = JSON.parse(data);
            if (dataX.status == "success") {
                messageBox.addClass("success").text(dataX.message).show();
                // Redirect after short delay to show message
                setTimeout(function() {
                    window.location.href = "login.html";
                }, 1500);
            } else {
                messageBox.addClass("error").text(dataX.message).show();
            }
        } catch (err) {
            console.error("Error parsing JSON:", err);
            messageBox.addClass("error").text("").show();
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Request failed:", textStatus, errorThrown);
        messageBox.addClass("error").text("Failed to submit registration. Please try again.").show();
    });
}

function Signin(e) {
    e.preventDefault();
    var email = $("#txtEmail").val();
    var password = $("#txtPassword").val();

    // Clear previous messages
    var messageBox = $("#messageBox");
    messageBox.removeClass("success error").hide();

    $.post("../php/Signin.php", {
        xemail : email,
        xpassword : password
    }, function (data){
        try {
            let dataX = JSON.parse(data);
            if (dataX.status == "success") {
                // Store user email in localStorage for reservation functionality
                localStorage.setItem('userEmail', email);
                window.location.href="../src/userdashboard.html";
            } else {
                messageBox.addClass("error").text(dataX.message).show();
            }
        } catch (err) {
            console.error("Error parsing JSON:", err);
            messageBox.addClass("error").text("").show();
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Request failed:", textStatus, errorThrown);
        messageBox.addClass("error").text("Failed to sign in. Please try again.").show();
    });
}

function AdminSignin(e) {
    e.preventDefault();
    var username = $("#txtEmail").val();
    var password = $("#txtPassword").val();

    // Clear previous messages
    var messageBox = $("#messageBox");
    messageBox.removeClass("success error").hide();

    $.post("../php/adminlogin.php", {
        xusername : username,
        xpassword : password
    }, function (data){
        try {
            let dataX = JSON.parse(data);
            if (dataX.status == "success") {
                window.location.href="../src/admindashboard.html";
            } else {
                messageBox.addClass("error").text(dataX.message).show();
            }
        } catch (err) {
            console.error("Error parsing JSON:", err);
            messageBox.addClass("error").text("").show();
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Request failed:", textStatus, errorThrown);
        messageBox.addClass("error").text("Failed to log in. Please try again.").show();
    });
}

function submitReservation(e) {
    e.preventDefault();
    var fullname = $("#r-name").val();
    var email = $("#r-email").val();
    var number = $("#r-phone").val();
    var num_people = $("#r-people").val();
    var date = $("#r-date").val();
    var time = $("#r-time").val();
    var difficulty = $("#r-difficulty").val();
    var notes = $("#r-notes").val();
    var status = "pending";


    // Clear previous messages
    var messageBox = $("#messageBox");
    if (messageBox.length == 0) {
        $("#reservationForm").append('<div id="messageBox"></div>');
        messageBox = $("#messageBox");
    }
    messageBox.removeClass("success error").hide();

    $.post("../php/reservation.php", {
        fullname: fullname,
        email: email,
        number: number,
        num_people: num_people,
        date: date,
        time: time,
        difficulty: difficulty,
        notes: notes,
        status: status
    }, function (data) {
        console.log("Response from server:", data);
        try {
            let dataX = data; // jQuery parses JSON automatically if Content-Type is application/json
            if (dataX.status == "success") {
                messageBox.addClass("success").text(dataX.message).show();
                // Optionally clear form or redirect
                $("#reservationForm")[0].reset();
            } else {
                messageBox.addClass("error").text(dataX.message).show();
            }
        } catch (err) {
            console.error("Error parsing JSON:", err);
            messageBox.addClass("error").text("An error occurred. Please try again.").show();
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Request failed:", textStatus, errorThrown);
        messageBox.addClass("error").text("Failed to submit reservation. Please try again.").show();
    });
}
