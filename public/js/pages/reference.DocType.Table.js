/*
 *  Document   : uiTables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables page
 */

var uiRefDocumentType = function() {

    return {
        init: function(base_url,csrf_token) {
            //var actionTable, outgoingTable;
            App.datatables();
           		

            //START RECEIVE TABLE
			
            var documenTypeTable = $("#document_type_table").dataTable(
                {
                    
                    ajax: {
                
                        url: base_url + '/admin/reference/document_type/table',
                        dataSrc: function (json) {
                            if (json.hasOwnProperty('error')) {
                                console.log("Ajax error occurred: " + json.error);
                                return [];
                            } else {
                                console.log("Success");
                                return json;
                            }
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf_token);
                        },
                        error: function(xhr, status, error) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                alert("Error Code " + xhr.status + ": " + error + "\n" +
                                    "Message: " + xhr.responseJSON.error);
                            } else {
                                alert('An unknown error occurred.');
                            }
                        }
                    }, 
                    "className": "tbody-sm",
                    columns: [
                        { data: "cnt" },
                        { data: "type_code" },
                        { data: "type_desc" },
                        { data: "btn" },
                    ],
                    createdRow: function(row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('type_code', data.type_code);
                        $(row).attr('type_desc', data.type_desc);
                    },
                    "drawCallback": function(settings) {
                        $('.dropdown-toggle').dropdown(); // reinitialize dropdowns
                    },
                    processing: true,
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin"></i> Loading data...',
                        emptyTable: "No data found"
                    },

                    columnDefs: [
                        { 
                            "targets": [0,1,3],
                            "className": "text-center",
                            "orderable": false
                        }
                    ],
                    //ordering: true,
                    pageLength: 10,
                    lengthMenu: [[10, 20, 100, 1000, -1], ['10 rows', '20 rows', '100 rows', '1000 rows', 'Show all']],
                    
                    
                }
			);

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Search');

            /* Select/Deselect all checkboxes in tables */
            $('thead input:checkbox').click(function() {
                var checkedStatus   = $(this).prop('checked');
                var table           = $(this).closest('table');

                $('tbody input:checkbox', table).each(function() {
                    $(this).prop('checked', checkedStatus);
                });
            });

            /* Table Styles Switcher */
            var genTable        = $('#general-table');
            var styleBorders    = $('#style-borders');

            $('#style-default').on('click', function(){
                styleBorders.find('.btn').removeClass('active');
                $(this).addClass('active');

                genTable.removeClass('table-bordered').removeClass('table-borderless');
            });

            $('#style-bordered').on('click', function(){
                styleBorders.find('.btn').removeClass('active');
                $(this).addClass('active');

                genTable.removeClass('table-borderless').addClass('table-bordered');
            });

            $('#style-borderless').on('click', function(){
                styleBorders.find('.btn').removeClass('active');
                $(this).addClass('active');

                genTable.removeClass('table-bordered').addClass('table-borderless');
            });

            $('#style-striped').on('click', function() {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-striped');
                } else {
                    genTable.removeClass('table-striped');
                }
            });

            $('#style-condensed').on('click', function() {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-condensed');
                } else {
                    genTable.removeClass('table-condensed');
                }
            });

            $('#style-hover').on('click', function() {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-hover');
                } else {
                    genTable.removeClass('table-hover');
                }
            });
            
        },

    };
}();