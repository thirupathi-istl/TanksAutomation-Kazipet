<?php
require_once 'config-path.php';
require_once '../config_db/config.php';

require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Tanks Data Report</title>

    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Tanks Data Report</span></p>
                </div>
            </div>
            <div class="row">
                <div class="container mb-2">
                    <div class="row d-flex align-items-center">
                        <div class="col-12  ">
                            <div class="row d-flex justify-content-end align-items-center">
                                <div class="col-xl-3 col-lg-4 d-flex justify-content-end align-items-center">
                                    <!-- <p class="m-0" id="update_time"><span class="text-body-tertiary">Updated On : </span><span id="auto_update_date_time"></span></p> -->
                                </div>
                                <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center">
                                    <select class="form-select pointer" id="group-list" aria-label="Large select example">
                                        <option value="KAZIPET">Kazipet</option>
                                    </select>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center device_id_section" id="device_id_section">
                                    <select class="form-select pointer" id="device_id" name="device_id">
                                        <option value="PUMP_2">PUMP_2</option>
                                       <!--  <option value="PUMP_1">PUMP_1</option> -->
                                    </select>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center device_id_section" id="tank_id_section">
                                    <select class="form-select pointer" id="tank_id" name="tank_id">
                                        <option value="Tank_1" selected>Tank_1</option>
                                        <option value="Tank_2">Tank_2</option>
                                        <option value="Tank_3">Tank_3</option>
                                        <option value="Tank_4">Tank_4</option>
                                        <option value="Tank_5">Tank_5</option>
                                        <option value="Tank_6">Tank_6</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 p-0">
                        <div class="container-fluid text-center p-0 mt-3">
                            <div class="row d-flex align-items-center">
                                <div class="col-auto ms-auto">
                                    <div class="input-group">
                                        <input type="date" class="form-control" aria-label="date" id="search_date" value="yyyy-mm-dd" aria-describedby="button-addon2">
                                        <button class="btn btn-primary" type="button" onclick="search_records()">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive rounded mt-2 border">
                            <table class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100">
                                <thead class="sticky-header text-center" id="tank_data_header">
                                    <tr>
                                        <th class="table-header-row-1">Tank Name</th>
                                        <th class="table-header-row-1">Water Level</th>
                                        <th class="table-header-row-1">Valve Status</th>
                                        <th class="table-header-row-1">Current Status</th>
                                        <th class="table-header-row-1">Flow Rate (L/min)</th>
                                        <th class="table-header-row-1">Estimated Time</th>
                                        <th class="table-header-row-1">Consumed Time</th>
                                        <th class="table-header-row-1">Total Pumped water(L)</th>
                                        <th class="table-header-row-1">Voltage-1 (V)</th>
                                        <th class="table-header-row-1">Voltage-2 (V)</th>
                                        <th class="table-header-row-1">Gateway</th>
                                        <th class="table-header-row-1">Date Time</th>
                                    </tr>
                                </thead>
                                <tbody id="tank_data_body"></tbody>
                            </table>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-secondary btn-sm mt-2" id="btn_add_more" onclick="add_more_records()">+ More Records</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- Modals links -->
    </main>
    <script>
        let selectedTank = document.getElementById('tank_id');
        let tank_id = selectedTank.value;
        let deviceList = document.getElementById('device_id');
        let device_id = deviceList.value;
        let savedTankId = localStorage.getItem("selectedTankId");

        document.addEventListener("DOMContentLoaded", function() {
            let savedTankId = localStorage.getItem("selectedTankId");
            if (savedTankId) {
                document.getElementById("tank_id").value = savedTankId;
            }
            updateDashboardData(savedTankId, device_id, "LATEST", "");

        });
        updateDashboardData(tank_id, device_id, "LATEST", "");

        selectedTank.addEventListener('change', function() {
            let tank_id = selectedTank.value;
            if (tank_id !== "" && tank_id !== null) {
                $("#pre-loader").css('display', 'block');
                updateDashboardData(tank_id, device_id, "LATEST", "");
                localStorage.setItem("selectedTankId", this.value);
            }
        });

        deviceList.addEventListener('change', function() {
            device_id = deviceList.value;
            let tank_id = selectedTank.value;
            if (device_id !== "" && device_id !== null) {
                $("#pre-loader").css('display', 'block');
                updateDashboardData(tank_id, device_id, "LATEST", "");
            }
        });

        function search_records() {
            let tank_id = document.getElementById('tank_id').value;
            let device_id = document.getElementById('device_id').value;
            let searched_date = document.getElementById('search_date').value;
            searched_date = searched_date.trim();

            if (searched_date != null && searched_date != "") {
                updateDashboardData(tank_id, device_id, "DATE", searched_date);
            } else {
                updateDashboardData(tank_id, device_id, "LATEST", "");
            }
        }

        function add_more_records() {
            let tank_id = document.getElementById('tank_id').value;
            let device_id = document.getElementById('device_id').value;
            var rowCount = document.getElementById('tank_data_body').getElementsByTagName('tr').length;

            if (rowCount > 0) {
                var lastRow = document.querySelector('#tank_data_body tr:last-child');
                if (lastRow) {
                    var date_time = lastRow.querySelector('td:last-child').textContent;
                  
                    if (date_time && date_time.indexOf("Found") === -1) {
                        document.getElementById('pre-loader').style.display = 'block';
                        $.ajax({
                            type: "POST",
                            url: 'fetchTanksData.php',
                            traditional: true,
                            data: {
                                tank_id: tank_id,
                                device_id: device_id,
                                RECORDS: "ADD",
                                DATE_TIME: date_time
                            },
                            dataType: "json",
                            success: function(response) {
                                $("#pre-loader").css('display', 'none');

                                if (response.error) {
                                    if (window.error_toast && window.error_message_text) {
                                        error_message_text.textContent = response.error;
                                        error_toast.show();
                                    }
                                    return;
                                }

                                if (response.tankStatus && response.tankStatus.length > 0) {
                                    let newRows = "";
                                    response.tankStatus.forEach(tank => {
                                        const statusClass = tank.tank_status === "Empty" ? "status-empty" : "status-full";

                                        newRows += `
                                <tr>
                                    <td>${tank.tank_id}</td>
                                    <td class="${statusClass}">${tank.tank_status}</td>
                                    <td>${tank.valve_status}</td>
                                    <td>${tank.current_status}</td>
                                    <td>${tank.flow_rate}</td>
                                    <td>${tank.estimated_time}</td>
                                    <td>${tank.consumed_time}</td>
                                    <td>${tank.consumed_water}</td>
                                    <td>${tank.voltage_1}</td>
                                    <td>${tank.voltage_2}</td>
                                    <td>${tank.gateway_id}</td>
                                    <td>${tank.date_time}</td>
                                </tr>`;
                                    });

                                    $("#tank_data_body").append(newRows);
                                } else {
                                    if (window.error_toast && window.error_message_text) {
                                        error_message_text.textContent = "No more records found";
                                        error_toast.show();
                                    }
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                $("#pre-loader").css('display', 'none');
                                if (window.error_toast && window.error_message_text) {
                                    error_message_text.textContent = "Error getting the data";
                                    error_toast.show();
                                }
                            }
                        });
                    } else {
                        if (window.error_toast && window.error_message_text) {
                            error_message_text.textContent = "Records are not found";
                            error_toast.show();
                        }
                    }
                }
            } else {
                if (window.error_toast && window.error_message_text) {
                    error_message_text.textContent = "No records to add";
                    error_toast.show();
                }
            }
        }

        function updateDashboardData(tank_id, device_id, records, searched_date) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                url: 'fetchTanksData.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    tank_id: tank_id,
                    device_id: device_id,
                    RECORDS: records,
                    DATE: searched_date
                },
                success: function(response) {
                    $("#pre-loader").css('display', 'none');
                    let tableBody = document.getElementById('tank_data_body');

                    if (response.error) {
                        tableBody.innerHTML = `<tr><td colspan="12" class="text-danger text-center">${response.error}</td></tr>`;
                        return;
                    }

                    if (!response.tankStatus || response.tankStatus.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="12" class="text-center">No records found</td></tr>';
                        return;
                    }

                    let rows = ""; // Store rows in a variable to reduce reflow
                    response.tankStatus.forEach(tank => {
                        const statusClass = tank.tank_status === "Empty" ? "status-empty" : "status-full";

                        rows += `
                <tr>
                    <td>${tank.tank_id}</td>
                    <td class="${statusClass}">${tank.tank_status}</td>
                    <td>${tank.valve_status}</td>
                    <td>${tank.current_status}</td>
                    <td>${tank.flow_rate}</td>
                    <td>${tank.estimated_time}</td>
                    <td>${tank.consumed_time}</td>
                    <td>${tank.consumed_water}</td>
                    <td>${tank.voltage_1}</td>
                    <td>${tank.voltage_2}</td>
                    <td>${tank.gateway_id}</td>
                    <td>${tank.date_time}</td>
                </tr>`;
                    });

                    tableBody.innerHTML = rows; // Update the DOM once
                },
                error: function(xhr, status, error) {
                    $("#pre-loader").css('display', 'none');
                    console.log('Failed to fetch data:', error);
                    let tableBody = document.getElementById('tank_data_body');
                    tableBody.innerHTML = '<tr><td colspan="12" class="text-danger text-center">Error loading data. Please try again.</td></tr>';
                    if (window.error_toast && window.error_message_text) {
                        error_message_text.textContent = "Error getting the data";
                        error_toast.show();
                    }
                }
            });
        }
    </script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>