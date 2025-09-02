/*
 *  Document   : uiTables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables page
 */


var selectedIdsAct = [];

var UiTables = function() {

    return {
        init: function() {
            //var receiveTable, outgoingTable;
            App.datatables();
           		

            //START RECEIVE TABLE
			$("#receive-table").DataTable(
                {
                    ajax: {
                
                        url: base_url + '/receiveTbl',
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
                                // Create checkbox and set it based on selectedIdsRcv array
                                var checked = selectedIdsRcv.includes(row.docdetail) ? 'checked' : '';
                                return '<label class="csscheckbox csscheckbox-primary"><input type="checkbox" class="row-checkbox large-checkbox" data-origoffice="'+row.originating+'" data-doctype="'+row.doctype+'" data-subject="'+row.subject+'" data-control="'+row.controlno+'" data-id="' + row.docdetail + '" ' + checked + '><span></span></label>';
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
                        // Reapply the selected state after each draw (page change, sort, etc.)
                        var api = this.api();
                        api.$('.row-checkbox').each(function() {
                            var checkbox = $(this);
                            var rowId = checkbox.data('id');
                            if (selectedIdsRcv.some(item => item.rowId === rowId)) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        });
                    }
                    
                    
                }
			);

            // Handle checkbox selection
            $('#receive-table').on('change', '.row-checkbox', function () {
                var rowId = $(this).data('id');
                var controlId = $(this).data('control');
                var subj = $(this).data('subject');
                var doctype = $(this).data('doctype');
                var origoffice = $(this).data('origoffice');
                var thisrow = $(this).closest('tr');
            
                if ($(this).prop('checked')) {
                    // Check if the row is already in selectedIdsRcv
                    var exists = selectedIdsRcv.some(item => item.rowId === rowId);
                    if (!exists) {
                        // Add to selectedIdsRcv array if not already present
                        selectedIdsRcv.push({ rowId: rowId, controlId: controlId, subj: subj, doctype: doctype, origoffice: origoffice });
                    }

                    thisrow.addClass('success');
                } else {
                    // Remove the unchecked row based on its rowId
                    selectedIdsRcv = selectedIdsRcv.filter(item => item.rowId !== rowId);

                    thisrow.removeClass('success');
                }

                console.log(selectedIdsRcv);
            });

            $('#bulkReceive').on('click', function() {

                $("#overlay").show();

                if(selectedIdsRcv.length == 0){
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Please select Document to Receive",
                    });
                }else{
                    var toReceive = selectedIdsRcv.map(function(item) {
                        return {
                            rowId: item.rowId,
                            controlId: item.controlId,
                            subj: item.subj,
                            doctype: item.doctype,
                            origoffice: item.origoffice
                        };
                    });
                    $('#selectedForReceive').empty();  // Clear any previous data
                    toReceive.forEach(function(item) {
                        var rowHtml = '<tr>' +
                                    '<td>' + item.controlId + '</td>' + 
                                    '<td>' + item.origoffice + '</td>' + 
                                    '<td>' + item.subj + '</td>' + 
                                    '<td>' + item.doctype + '</td>' + 
                                    '</tr>';
                        $('#selectedForReceive').append(rowHtml);
                    });
                    
                    console.log("Selected Row IDs: ", toReceive);
        
                    $('#viewReceiveData').modal('show');
                }
        
                $("#overlay").hide();
                
            });
            //END RECEIVE TABLE
            
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
            /*$('#release-table').on('change', '.row-checkbox', function () {
                var rowId = $(this).data('id');
                var controlId = $(this).data('control');
                var subj = $(this).data('subject');
                var doctype = $(this).data('doctype');
                var origoffice = $(this).data('origoffice');
                var actioncode = $(this).data('actioncode');
                var actiondesc = $(this).data('actiondesc');
                var thisrow = $(this).closest('tr');
            
                if ($(this).prop('checked')) {
                    // Check if the row is already in selectedIdsAct
                    var exists = selectedIdsRel.some(item => item.rowId === rowId);
                    if (!exists) {
                        // Add to selectedIdsAct array if not already present
                        selectedIdsRel.push({ rowId: rowId, controlId: controlId, subj: subj, doctype: doctype, origoffice: origoffice, actioncode: actioncode, actiondesc: actiondesc });
                    }

                    thisrow.addClass('info');
                } else {
                    // Remove the unchecked row based on its rowId
                    selectedIdsRel = selectedIdsRel.filter(item => item.rowId !== rowId);

                    thisrow.removeClass('info');
                }
            });
            
            $('#bulkRelease').on('click', function() {

                if(selectedIdsRel.length == 0){
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Please select Document to Release",
                      });
                }else{


                    $.ajax({
                        url: base_url + "/getBulkReleaseData",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        beforeSend: function(xhr) {
            
                            $("#overlay").show();
            
                            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                        },
                        success: function(response) {
                            try {
                                if (response.success) {
                                    //alert(response.message);
                                    Swal.fire({
                                        position: "top-end",
                                        icon: "success",
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
            
                                    $('#viewActionData').modal('hide');
                                    $('#action-table').DataTable().ajax.reload(null, false); 
                                    
                                    selectedIdsAct = [];
                                    
                                } else {
            
                                    //alert(response.message);
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error!",
                                        text: response.message,
                                    });
                                    
                                    if(response.reload){
                                        $('#viewActionData').modal('hide');
                                        $('#action-table').DataTable().ajax.reload(null, false);
                                        selectedIdsAct = [];
                                    }
                                   
                                }
                            } catch (error) {
                                console.error("Error processing response:", error);
                            }
                        },
                        error: function(xhr, errorType, thrownError) {
                            if (xhr.status === 403 || xhr.status === 405) {
                                alert(xhr.responseText);
                                console.log("Server error: " + xhr.responseText);
                            } else {
                                var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : xhr.statusText;
                                //alert("Server error: " + errorMessage);
                                console.log("Server error: " + xhr.responseText);
                            }
                            
                        },
                        complete: function() {
                        
                            $("#overlay").hide();
                        }
                    });



                    $('#selectedForAction').empty();

                    selectedIdsRel.forEach(function (item) {
            
                        // Append row to modal table
                        var rowHtml = `
                            <tr>
                                <td>${item.controlId}</td>
                                <td>${item.origoffice}</td>
                                <td>${item.subj}</td>
                                <td>${item.doctype}</td>
                                <td>${item.actiondesc}</td>
                            </tr>`;
                        $('#selectedForRelease').append(rowHtml);
                    });

                    console.log("Selected Rows: ", selectedIdsRel);
                    
                    $('.select-select2').select2();
                    $('#viewReleaseData').modal('show');


                }
            
            });*/






            //END RELEASE TABLE


            //START RELEASED DOC TABLE
            $("#released-table").dataTable(
                {
                    ajax: {
                
                        url: base_url + '/releasedTbl',
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
                       
                        { data: "controlno" },
                        { data: "subject" },
                        { data: "remarks" },
                        { data: "doctype" },
                        { data: "status" },
                        { data: "destination" },
                        { data: "actionrequire" },
                        { data: "datelog" },
                        { data: "btnaction" },
                        { data: "timestamp", visible: false },
                    ],
            
                    order: [
                        [8, 'desc'],
                    ],
            
                    "columnDefs": [
                        { 
                            "targets": [0,7,8],
                            "className": "text-center",
                            "orderable": false
                        }
                    ],
            
                    createdRow: function(row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('id', data.docdetailp);
                        $(row).attr('destoffice', data.destinationcode);
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
                            if ([0, 1, 2, 3, 4, 5, 6].includes(index)) {
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
                    
                }
            );
            //END RELEASED DOC TABLE


            //START UNDONE DOC TABLE
            $("#undone-table").dataTable(
                {
                    ajax: {
                
                        url: base_url + '/undoneTbl',
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
                        [6, 'desc'],
                    ],
            
                    "columnDefs": [
                        { 
                            "targets": [0,1,6,7],
                            "className": "text-center",
                            "orderable": false
                        }
                    ],
            
                    createdRow: function(row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('id', data.docdetailp);
                        $(row).attr('destoffice', data.destinationcode);
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
                            if ([0, 1, 2, 3, 4, 5, 6].includes(index)) {
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
                    
                }
            );
            //END UNDONE DOC TABLE



            $("#outgoing-table").dataTable(
                {
                    ajax: {
                
                        url: base_url + '/outgoingTbl',
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
                        { data: "action" },
                        { data: "datelog" },
                        { data: "routeno" },
                        { data: "refcontrolno" },
                        { data: "subject" },
                        { data: "ddoctype" },
                        { data: "officecode" },
                        { data: "entryby" },
                        { data: "pageno" },
                        { data: "attachlist" },
                        { data: "remarks" },
                        { data: "attachment" },
                    ],

                    createdRow: function(row, data, dataIndex) {
                        // Add a data-id attribute to the row
                        $(row).attr('rid', data.routeno);
                    },

                    processing: true,
                    "columnDefs": [
                        { 
                            "targets": [0,6,8,11],
                            "className": "text-center",
                            "orderable": true
                        }
                    ],
                    ordering: true,
                    pageLength: 10,
                    lengthMenu: [[10, 20, 100, 1000, -1], ['10 rows', '20 rows', '100 rows', '1000 rows', 'Show all']],
                    bDestroy: true,
                    language: {
                        emptyTable: "No data found"
                    },

                    initComplete: function () {

                        let inputRow = document.createElement('tr');
                    
                        this.api()
                            .columns()
                            .every(function (index) {
                                let column = this;
                    
                                if (index === 1 || index === 2 || index === 3 || index === 4 || index === 5 || index === 6 || index === 7 || index === 8 || index === 9 || index === 10 || index === 11) {

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
                    
                        // Insert the input row above the header row
                        let header = this.api().table().header();
                        header.parentNode.insertBefore(inputRow, header);
                    }
                    
                    
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