/*
 *  Document   : uiTables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables page
 */

var UiTables = function() {

    return {
        init: function() {
            //var receiveTable, outgoingTable;
            App.datatables();
           		
            //START ACTION TABLE
            $("#action-table").dataTable(
                {
                    ajax: {
                
                        url: base_url + '/actionTbl',
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
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
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
                        {
                            data: null,
                            render: function(data, type, row) {
                                // Create checkbox and set it based on selectedIdsAct array
                                var checked = selectedIdsAct.includes(row.docdetail) ? 'checked' : '';
                                //return '<input type="checkbox" class="row-checkbox large-checkbox" data-origoffice="'+row.originating+'" data-doctype="'+row.doctype+'" data-subject="'+row.subject+'" data-control="'+row.controlno+'" data-id="' + row.docdetail + '" data-listactiondone=' + row.listaction + ' data-actiondone="' + row.actiondone + '" ' + checked + '>';

                                return `
                                <label class="csscheckbox csscheckbox-warning"><input type="checkbox" class="row-checkbox large-checkbox" 
                                    data-origoffice="${row.originating}" 
                                    data-doctype="${row.doctype}" 
                                    data-subject="${row.subject}" 
                                    data-control="${row.controlno}" 
                                    data-id="${row.docdetail}" 
                                    data-listactiondone='${row.listaction}' 
                                    data-actiondone='${row.actiondone}' ${checked}>
                                    <span></span></label>
                            `;
                            },
                            orderable: false, 
                            searchable: false,
                        },
                        //{ data: "attachment" },
                        { data: "controlno" },
                        { data: "originating" },
                        { data: "previous" },
                        { data: "subject" },
                        { data: "remarks" },
                        { data: "doctype" },
                        //{ data: "actionrequire" },
                        { data: "datelog" },
                        { data: "btnaction" },
                        //{ data: "btnforward" },
                        //{ data: "btnreturn" },
                    ],
            
                    order: [
                        [8, 'desc'],
                    ],
            
                    "columnDefs": [
                        { 
                            "targets": [0,1,8],
                            "className": "text-center",
                            "orderable": false
                        }
                    ],
            
                    createdRow: function(row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('id', data.docdetail);
                    },
            
                    processing: true,
                    //columnDefs: [{ orderable: true }],
                    ordering: true,
                    pageLength: 10,
                    lengthMenu: [[10, 20, 100, 1000, -1], ['10 rows', '20 rows', '100 rows', '1000 rows', 'Show all']],
                    bDestroy: true,
                    language: {
                        emptyTable: "No data found"
                    },
            
                    initComplete: function() {
                        let inputRow = document.createElement('tr');
                        this.api().columns().every(function(index) {
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
                    drawCallback: function(settings) {
                        var api = this.api();
                        api.$('.row-checkbox').each(function() {
                            var checkbox = $(this);
                            var rowId = checkbox.data('id');
                            
                            if (selectedIdsAct.some(item => item.rowId === rowId)) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        });
                    }
                    
                    
                }
            );
            
            // Handle checkbox selection
            $('#action-table').on('change', '.row-checkbox', function () {
                var rowId = $(this).data('id');
                var controlId = $(this).data('control');
                var subj = $(this).data('subject');
                var doctype = $(this).data('doctype');
                var origoffice = $(this).data('origoffice');
                var actiondone = $(this).data('actiondone');
                var listactiondone = $(this).data('listactiondone');
                var thisrow = $(this).closest('tr');
            
                if ($(this).prop('checked')) {
                    // Check if the row is already in selectedIdsAct
                    var exists = selectedIdsAct.some(item => item.rowId === rowId);
                    if (!exists) {
                        // Add to selectedIdsAct array if not already present
                        selectedIdsAct.push({ rowId: rowId, controlId: controlId, subj: subj, doctype: doctype, origoffice: origoffice, actiondone: actiondone, listactions: listactiondone });
                    }

                    thisrow.addClass('warning');
                } else {
                    // Remove the unchecked row based on its rowId
                    selectedIdsAct = selectedIdsAct.filter(item => item.rowId !== rowId);

                    thisrow.removeClass('warning');
                }
            });
            
            $('#bulkAction').on('click', function() {
                
                $("#overlay").show();
                if(selectedIdsAct.length == 0){
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Please select Document to take Action",
                      });
                }else{

                    $('#selectedForAction').empty();

                    selectedIdsAct.forEach(function (item) {
                        // Parse listaction and generate dropdown options
                        //console.log(typeof item.listactions);
                        var listActions = item.listactions;
                        var dropdownOptions = listActions.map(function (action) {
                            var selected = action.action_code === item.actiondone ? 'selected' : '';
                            return `<option value="${action.action_code}" ${selected}>${action.action_desc}</option>`;
                        }).join('');
            
                        // Create dropdown HTML
                        var dropdown = `<select class="select-select2 action-done-dropdown" data-row-id="${item.rowId}" style="width:100%">
                                            ${dropdownOptions}
                                        </select>`;
            
                        // Append row to modal table
                        var rowHtml = `
                            <tr>
                                <td>${item.controlId}</td>
                                <td>${item.origoffice}</td>
                                <td>${item.subj}</td>
                                <td>${item.doctype}</td>
                                <td>${dropdown}</td>
                            </tr>`;
                        $('#selectedForAction').append(rowHtml);
                    });

                    console.log("Selected Rows: ", selectedIdsAct);
                    
                    $('.select-select2').select2();

                    $('#viewActionData').modal('show'); // Show modal

                }
                    $("#overlay").hide();
            
            });


            $('#selectedForAction').on('change', '.action-done-dropdown', function() {
                var rowId = $(this).data('row-id');
                var newActionDone = $(this).val();

                // Find the item in selectedIdsAct array by rowId
                var item = selectedIdsAct.find(item => item.rowId === rowId);

                if (item) {
                    // Update the actiondone property
                    item.actiondone = newActionDone;
                    console.log(selectedIdsAct);
                } else {
                    console.log('Item not found with rowId:', rowId);
                }
            });
            //END ACTION TABLE
			
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