/*
 *  Document   : uiTables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables page
 */

var UiTables = function() {

    return {
        init: function(base_url,csrf_token) {
            //var receiveTable, outgoingTable;
            App.datatables();
           		

            //START RECEIVE TABLE
			
            var documentManagementTable = $("#document-management-table").dataTable(
                {
                    serverSide: true,
                    processing: true,
                    deferLoading: 0,
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin"></i> Loading data...'
                    },
                    ajax: {
                
                        url: base_url + '/admin/document_management/table',
                        data: function(d) {
                            // You can modify the data object here if needed
                            d.routeNoFilter = $('#route_no').val() || '';
                            d.documentControlFilter = $('#control_no').val() || '';
                            d.subjectFilter = $('#subject').val() || '';
                        },
                        dataSrc: function (json) {
                            if (json.hasOwnProperty('error')) {
                                console.log("Ajax error occurred: " + json.error);
                                return [];
                            } else {
                                if (json.office) {
                                    $('#table_office').html(json.office);
                                }
                                if (json.report_date) {
                                    $('#table_office').html(json.office);
                                }   

                                console.log("Success");
                                return json.data;
                            }
                        },
                        type: "post",
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
                        { data: "action" },
                        { data: "routeno" },
                        { data: "docno" },
                        { data: "ref_controlno" },
                        { data: "subject" },
                        { data: "doctype" },
                        { data: "orig_office" },
                        { data: "entryby" },
                        { data: "pageno" },
                        { data: "attachment" },
                        { data: "remarks" },
                        { data: "status" },
                    ],
                    createdRow: function(row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('routeno', data.routeno);
                    },

                    drawCallback: function(settings) {
                        if (!settings.json) {
                            // No JSON returned yet (first load, error, or empty)
                            $('.dataTables_paginate').hide();
                            return;
                        }

                        var totalRecords = settings.json.recordsFiltered || 0;
                        var pageLength   = settings._iDisplayLength || 1;

                        var pageCount    = Math.ceil(totalRecords / pageLength);
                        var currentPage  = Math.ceil(settings._iDisplayStart / pageLength);

                        $('.dataTables_paginate').toggle(pageCount > 1);

                        console.log("Current Page: " + currentPage + ", Total Pages: " + pageCount);
                    },
                    columnDefs: [
                        { 
                            "targets": [0,1,2,3,6,7,8,9,10,11],
                            "className": "text-center",
                            "orderable": false
                        }
                    ],
                    //ordering: true,
                    pageLength: 20,
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

            this.bindEvents(documentManagementTable);
            
        },

        bindEvents: function(documentManagementTable) {
            $('#document_management_form').on('submit', this.filterReportTable.bind(this, documentManagementTable));
            $(document)
                .on('click', '#cancelSearch', this.cancelSearchBtn.bind(this,documentManagementTable))
                .on('click', '#resetFilters', this.clearForm.bind(this))
                ;
        },

        filterReportTable: function(tbl,event) {
            event.preventDefault();
            tbl.api().ajax.reload();
            //$('#filter_datefrom').val("");
            //$('#filter_dateto').val("");

            $('#cancelSearch').show();
            $('#resetFilters').hide();
        },

        cancelSearchBtn: function(tbl,event) {
            event.preventDefault();
            this.clearForm();

            tbl.api().ajax.reload();

            $('#cancelSearch').hide();
            $('#resetFilters').show();
            
        },

        clearForm: function(event){

            $('#document_management_form')[0].reset();
            $('#route_no').val(null);
            $('#control_no').val(null);
            $('#subject').val(null);
        },

    };
}();