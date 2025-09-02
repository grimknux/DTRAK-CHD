/*
 *  Document   : uiTables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables page
 */

var UiTables = function () {

    return {
        init: function () {
            //var receiveTable, outgoingTable;
            App.datatables();

            //START RELEASE TABLE
            $("#release-table").dataTable(
                {
                    ajax: {

                        url: base_url + '/releaseTbl',
                        dataSrc: function (json) {
                            if (json.hasOwnProperty('error')) {
                                console.log("Ajax error occurred: " + json.error);
                                return [];
                            } else {
                                console.log("Success");
                                return json;
                            }
                        },
                        type: "post",
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                        },
                        error: function (xhr, status, error) {
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
                        {
                            data: null,
                            render: function (data, type, row) {
                                // Create checkbox and set it based on selectedIdsRel array
                                var checked = selectedIdsRel.includes(row.docdetail) ? 'checked' : '';

                                return `
                                <label class="csscheckbox csscheckbox-info"><input type="checkbox" class="row-checkbox large-checkbox" 
                                    data-origoffice="${row.originating}" 
                                    data-doctype="${row.doctype}" 
                                    data-subject="${row.subject}" 
                                    data-control="${row.controlno}" 
                                    data-id="${row.docdetail}"
                                    data-actioncode='${row.actioncode}'
                                    data-actiondesc='${row.actiondesc}' ${checked}>
                                    <span></span></label>
                            `;
                            },
                            orderable: false,
                            searchable: false,
                        },
                        { data: "controlno" },
                        { data: "originating" },
                        { data: "previous" },
                        { data: "subject" },
                        { data: "remarks" },
                        { data: "doctype" },
                        { data: "datelog" },
                        { data: "btnaction" },
                    ],

                    order: [
                        [8, 'desc'],
                    ],

                    "columnDefs": [
                        {
                            "targets": [0, 1, 8],
                            "className": "text-center",
                            "orderable": false
                        }
                    ],

                    createdRow: function (row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('id', data.docdetail);
                    },

                    processing: true,
                    ordering: true,
                    pageLength: 10,
                    lengthMenu: [[10, 20, 100, 1000, -1], ['10 rows', '20 rows', '100 rows', '1000 rows', 'Show all']],
                    bDestroy: true,
                    language: {
                        emptyTable: "No data found"
                    },

                    initComplete: function () {
                        let inputRow = document.createElement('tr');
                        this.api().columns().every(function (index) {
                            let column = this;
                            if ([1, 2, 3, 4, 5, 6, 7].includes(index)) {
                                let input = document.createElement('input');
                                input.style.width = '90%';
                                input.style.margin = '5px auto';
                                input.style.display = 'block';
                                let th = document.createElement('th');
                                th.style.textAlign = 'center';
                                th.appendChild(input);
                                inputRow.appendChild(th);
                                input.addEventListener('keyup', () => {
                                    if (column.search() !== input.value) {
                                        column.search(input.value).draw();
                                    }
                                });
                            } else {
                                let th = document.createElement('th');
                                th.style.textAlign = 'center';
                                inputRow.appendChild(th);
                            }
                        });
                        let header = this.api().table().header();
                        header.parentNode.insertBefore(inputRow, header);
                    },
                    drawCallback: function (settings) {
                        var api = this.api();
                        api.$('.row-checkbox').each(function () {
                            var checkbox = $(this);
                            var rowId = checkbox.data('id');

                            if (selectedIdsRel.some(item => item.rowId === rowId)) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        });
                    }


                }
            );

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Search');

            /* Select/Deselect all checkboxes in tables */
            $('thead input:checkbox').click(function () {
                var checkedStatus = $(this).prop('checked');
                var table = $(this).closest('table');

                $('tbody input:checkbox', table).each(function () {
                    $(this).prop('checked', checkedStatus);
                });
            });

            /* Table Styles Switcher */
            var genTable = $('#general-table');
            var styleBorders = $('#style-borders');

            $('#style-default').on('click', function () {
                styleBorders.find('.btn').removeClass('active');
                $(this).addClass('active');

                genTable.removeClass('table-bordered').removeClass('table-borderless');
            });

            $('#style-bordered').on('click', function () {
                styleBorders.find('.btn').removeClass('active');
                $(this).addClass('active');

                genTable.removeClass('table-borderless').addClass('table-bordered');
            });

            $('#style-borderless').on('click', function () {
                styleBorders.find('.btn').removeClass('active');
                $(this).addClass('active');

                genTable.removeClass('table-bordered').addClass('table-borderless');
            });

            $('#style-striped').on('click', function () {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-striped');
                } else {
                    genTable.removeClass('table-striped');
                }
            });

            $('#style-condensed').on('click', function () {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-condensed');
                } else {
                    genTable.removeClass('table-condensed');
                }
            });

            $('#style-hover').on('click', function () {
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