function handleValidationErrors(response) {
    // Reset validation messages
    $('.has-success').removeClass('has-success');
    $('.has-error').removeClass('has-error');
    $('.help-block').empty();

    // Iterate over the response object and display validation errors
    var hasErrors = false; // Flag to track if any errors are found

    for (var field in response) {
        if (response.hasOwnProperty(field)) {
            var error = response[field];

            if (error) {
                var element = $('.' + field);
                var messageElement = $('.' + field + 'Message');

                element.removeClass('has-success').addClass('has-error');
                messageElement.html(error);

                hasErrors = true; // Set the flag to indicate error
            } else {
                var validElement = $('.' + field);
                var validMessageElement = $('.' + field + 'Message');

                validElement.removeClass('has-error').addClass('has-success');
                validMessageElement.empty();
            }
        }
    }
}