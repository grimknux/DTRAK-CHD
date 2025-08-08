$(document).ready(function() {
    function setupButtonClickHandler(base_url) {
        $('#receive-table tbody').on('click', '.insta-rcv', function() {
            var rowId = $(this).closest('tr').data('id'); // Get the row ID from data attribute


            alert(rowId);
            /*$.ajax({
                url: base_url + '/receiveData', // Replace with your server endpoint
                type: 'POST',
                data: { id: rowId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        console.log('Data retrieved successfully:', response.data);
                        // Process the response data
                    } else {
                        console.error('Error retrieving data:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });*/
        });
    }

    setupButtonClickHandler(baseUrl);
});

