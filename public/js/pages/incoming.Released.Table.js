var UiTables = function () {

    return {
        init: function () {
            App.datatables();
            
            $("#released-table").dataTable({
                
                processing: true,
                language: {
                    emptyTable: "No data found",
                    processing: '<i class="fa fa-spinner fa-spin"></i> Loading data...',
                },
                ajax: {
                    url: base_url + "/releasedTbl",
                    dataSrc: function (json) {
                        if (json.hasOwnProperty("error")) {
                            console.log("Ajax error occurred: " + json.error);
                            return [];
                        } else {
                            console.log("Success");
                            return json;
                        }
                    },
                    type: "post",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("X-CSRF-Token", csrfToken);
                    },
                    error: function (xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            alert(
                                "Error Code " +
                                xhr.status +
                                ": " +
                                error +
                                "\n" +
                                "Message: " +
                                xhr.responseJSON.error
                            );
                        } else {
                            alert("An unknown error occurred.");
                        }
                    },
                },
                className: "tbody-sm",
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
                columnDefs: [
                    {
                        targets: [0, 7, 8],
                        className: "text-center",
                        orderable: false,
                    },
                ],
                ordering: true,
                order: [[8, "desc"]],
                pageLength: 10,
                lengthMenu: [
                    [10, 20, 100, 1000, -1],
                    ["10 rows", "20 rows", "100 rows", "1000 rows", "Show all"],
                ],

                createdRow: function (row, data, dataIndex) {
                    $(row).attr("id", data.docdetailp);
                    $(row).attr("destoffice", data.destinationcode);
                },

                initComplete: function () {
                    let inputRow = document.createElement("tr");
                    this.api()
                        .columns()
                        .every(function (index) {
                            let column = this;
                            if ([0, 1, 2, 3, 4, 5, 6].includes(index)) {
                                let input = document.createElement("input");
                                input.style.width = "90%";
                                input.style.margin = "5px auto";
                                input.style.display = "block";
                                let th = document.createElement("th");
                                th.style.textAlign = "center";
                                th.appendChild(input);
                                inputRow.appendChild(th);
                                input.addEventListener("keyup", () => {
                                    if (column.search() !== input.value) {
                                        column.search(input.value).draw();
                                    }
                                });
                            } else {
                                let th = document.createElement("th");
                                th.style.textAlign = "center";
                                inputRow.appendChild(th);
                            }
                        });
                    let header = this.api().table().header();
                    header.parentNode.insertBefore(inputRow, header);
                },
            });

            $('.dataTables_filter input').attr('placeholder', 'Search');
            
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
        }
    };
}();
