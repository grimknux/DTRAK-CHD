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
			
            var timelineTable = $("#adminreport-timeline-table").dataTable(
                {
                    serverSide: true,
                    processing: true,
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin"></i> Loading data...'
                    },
                    ajax: {
                
                        url: base_url + '/admin/report/table/timeline',
                        data: function(d) {
                            // You can modify the data object here if needed
                            d.officeFilter = $('#office').val() || '';
                            d.doctypeFilter = $('#document_type').val() || '';
                            d.docstatusFilter = $('#document_status').val() || '';
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
                        { data: "cnt" },
                        { data: "docno" },
                        { data: "originating" },
                        { data: "current" },
                        { data: "subject" },
                        { data: "doctype" },
                        { data: "rcv_day" },
                        { data: "rcv_hours" },
                        { data: "rcv_minutes" },
                        { data: "rel_day" },
                        { data: "rel_hours" },
                        { data: "rel_minutes" },
                        { data: "doc_remarks" },
                    ],

                    drawCallback: function(settings) {
                        var totalRecords = settings.json.recordsFiltered;
                        var pageLength = settings._iDisplayLength;
                        
                        var pageCount = Math.ceil(totalRecords / pageLength);
                
                        var currentPage = Math.ceil(settings._iDisplayStart / pageLength);
                
                        if (pageCount <= 1) {
                            $('.dataTables_paginate').hide();
                        } else {
                            $('.dataTables_paginate').show(); 
                        }
                
                        // Optional: You can add any custom logic or actions after each redraw
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

            this.bindEvents(timelineTable);
            
        },

        bindEvents: function(timelineTable) {
            $('#timeline_form_report').on('submit', this.filterReportTable.bind(this, timelineTable));
            $(document)
                .on('click', '#cancelSearch', this.cancelSearchBtn.bind(this,timelineTable))
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

            $('#timeline_form_report')[0].reset();
            $('#office').val(null).trigger('change');
            $('#document_type').val(null).trigger('change');
            $('#document_status').val('all').trigger('change');
        },

    };
}();