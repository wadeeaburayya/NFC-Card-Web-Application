const input = document.querySelector("#phone");
const countryInput = document.querySelector("#country_code");

window.intlTelInput(input, {
    initialCountry: "auto",
    hiddenInput: "full_phone",
    geoIpLookup: callback => {
        fetch("https://ipapi.co/json")
            .then(res => res.json())
            .then(data => {
                callback(data.country_code);
                countryInput.value = data.country_code; // Update the hidden input
            })
            .catch(() => {
                callback("us");
                countryInput.value = "us"; // Default to 'us' if there's an error
            });
    },
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js", // just for formatting/placeholders etc
});
$(document).ready(function () {
    // Listen for the form submission
    $('#contactForm').submit(function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Serialize the form data
        var formData = $(this).serialize();


        // Send an AJAX request
        $.ajax({
            type: 'POST',
            url: '/submit_contactus',
            data: formData,
            success: function (response) {

                $('#successMessage').show();

                // Reset the form after a delay (optional)
                setTimeout(function () {
                    $('#contactForm')[0].reset();
                }, 3000); // Reset form after 3 seconds
            },
            error: function (error) {
                console.log('Error:', error);
                // Handle any error if needed
            }
        });
    });
    $('#subscribeForm').submit(function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Serialize the form data
        var formData = $(this).serialize();


        // Send an AJAX request
        $.ajax({
            type: 'POST',
            url: '/subscribe',
            data: formData,
            success: function (response) {

                $('#subscribeForm').hide();
                $('#successMessageSubscribe').show();

                // Reset the form after a delay (optional)
                setTimeout(function () {
                    $('#subscribeForm')[0].reset();
                }, 3000); // Reset form after 3 seconds
            },
            error: function (error) {
                console.log('Error:', error);
                // Handle any error if needed
            }
        });
    });
});
