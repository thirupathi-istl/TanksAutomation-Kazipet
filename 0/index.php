<?php
require_once 'config-path.php';
require_once '../config_db/config.php';

require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <title>Dashboard</title> 
  <style>


    h1, h2, h3 {
        color: rgb(31, 41, 55);
        font-weight: bold;
    }

    h1 { font-size: 1.875rem; margin-bottom: 2rem; }
    h2 { font-size: 1.5rem; margin-bottom: 1rem; }
    h3 { font-size: 1.25rem; margin-bottom: 0.5rem; }

    .grid {
        display: grid;
        gap: 1.5rem;
    }

    @media (min-width: 1024px) {
        .controls-grid { grid-template-columns: repeat(12, 1fr); }
        .tanks-grid { grid-template-columns: repeat(12, 1fr); }
    }

    .tank {
        position: relative;
        height: 12rem;
        background: rgb(229, 231, 235);
        border-radius: 0.5rem;
        overflow: hidden;
        margin: 1rem 0;
        border: 1px solid #ddd;
    }

    .tank-full-message {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        z-index: 10;
        font-weight: bold;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tank-full-message.visible {
        opacity: 1;
    }

    .water {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: linear-gradient(to top, #66b2ff, #52d4ff);
        transition: height 0.5s ease-in-out;
    }

    .water.filling::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 0;
        width: 100%;
        height: 20px;
        background: repeating-linear-gradient( 45deg, transparent, transparent 10px, rgba(0, 122, 37, 0.96) 10px, rgba(0, 122, 37, 0.96) 20px
        );
        animation: waterFlow 1.8s linear infinite;
    }

    @keyframes waterFlow {
        0% { transform: translateX(-20px); }
        100% { transform: translateX(0); }
    }

    .water.filling::after {
        content: '';
        position: absolute;
        top: -5px;
        left: 0;
        width: 100%;
        height: 5px;
        background: rgba(255, 255, 255, 0.3);
        animation: waterSplash 0.5s linear infinite;
    }

    @keyframes waterSplash {
        0%, 100% { transform: scaleY(1); }
        50% { transform: scaleY(1.5); }
    }

    .valve-controls {
        display: flex;
        justify-content: center;
        gap: 0.75rem;

    }

    .valve-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        min-width: 100px;
    }

    .valve-btn:hover {
        transform: translateY(-1px);
    }

    .valve-btn-on {
        background: rgb(34, 197, 94);
        color: white;
    }

    .valve-btn-off {
        background: rgb(239, 68, 68);
        color: white;
    }

    .motor-control {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .motor-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-motor {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        min-width: 60px;
    }

    .btn-motor-on {
        background: rgb(34, 197, 94);
        color: white;
    }

    .btn-motor-off {
        background: rgb(239, 68, 68);
        color: white;
    }



    .status-table th,
    .status-table td {
        padding: 0.75rem;
        text-align: center;
        border: 1px solid rgb(229, 231, 235);
    }



    .status-table td {
        color: rgb(75, 85, 99);
    }

    .status-active {
        color: rgb(34, 197, 94) !important;
        font-weight: 500;
    }

    .status-next {
        color: rgb(245, 158, 11) !important;
        font-weight: 500;
    }

    .status-inactive {
        color: rgb(239, 68, 68) !important;
    }

    .tank-info {
        display: flex;
        justify-content: space-between;
        margin: 0.5rem 0;
        color: rgb(75, 85, 99);
        font-size: 0.875rem;
    }

    .tank-capacity {
        text-align: center;
        color: rgb(75, 85, 99);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: bold;
    }

    .priority-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .priority-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: rgb(249, 250, 251);
        border-radius: 0.5rem;
    }

    .priority-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .priority-btn {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid rgb(229, 231, 235);
        background: white;
        cursor: pointer;
    }

    .priority-value {
        min-width: 2rem;
        text-align: center;
        font-weight: 500;
        color: rgb(31, 41, 55);
    }

    .flow-rate {
        font-size: 0.875rem;
        color: rgb(75, 85, 99);
        margin-top: 0.5rem;
    }

    .level-indicator {
        position: absolute;
        right: 0.5rem;
        top: 0.5rem;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
    }
    .card-hight 
    {   

        max-height:600px;
        overflow-y: auto;
    }
    .grid-card-hight
    {
        max-height:600px;
    }


    .tank-status-table {
        height: 600px; /* or your preferred height */
        overflow: auto; /* Enable both horizontal and vertical scrolling */
        position: relative; /* Ensure the table stays within the container */
    }


    .status-table {
        width: 100%; /* Full-width table for scrolling */
        border-collapse: collapse;
        margin-top: 1rem;
        background: white;
        border-radius: 0.5rem;
        overflow: auto;
    }

    .status-table th {
        position: sticky; /* Keep header in place while scrolling */
        top: 0; /* Stick to the top of the container */
        background: rgb(243, 244, 246); /* Header background color */
        z-index: 1; /* Ensure it appears above table rows */
        font-weight: 600;
        color: rgb(31, 41, 55);
    }

    .card.inactive {
        opacity: 0.5;
        pointer-events: none;
    }

    .inactive-message {
        color: red;
        font-weight: bold;
        text-align: center;
        margin-top: 10px;
    }

    .tank.inactive {
        opacity: 0.3;
        background-color: #fd9b9b; /* Light red background */
        position: relative;
    }

    .tank.inactive .inactive-message {
        position: absolute;
        transform: translate(-50%, -50%);
        font-weight: bold;
        font-size: 1.2rem;

        text-align: center;
        z-index: 10;
    }

    .inactive-message {
        position: absolute;
        top: 15%;
        left: 0;
        width: 100%;
        font-size: 20px;
        color: red;

    }
    .card {
        position: relative; /* Ensure proper positioning for absolute elements */
    }       
    .tank {
        position: relative;

    }


</style> 
<?php
include(BASE_PATH."assets/html/start-page.php");
?>
<div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
    <div class="container-fluid">
      <div class="row d-flex align-items-center">
        <div class="col-12 p-0">
          <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Dashboard</span></p>
      </div>
  </div>
  <?php
     // include(BASE_PATH."dropdown-selection/device-list.php");
  ?>
  <div class="row">


      <div class="container mb-2">
        <!-- <h1 class="text-center">Smart Water Tank Filling Management System</h1> -->
        <div class="row d-flex align-items-center">
            <div class="col-12  ">
                <div class="row d-flex justify-content-end align-items-center"> 
                    <div class="col-xl-3 col-lg-4 d-flex justify-content-end align-items-center">
                        <!-- <p class="m-0" id="update_time"><span class="text-body-tertiary">Updated On : </span><span id="auto_update_date_time"></span></p> -->
                    </div>
                    <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center">
                        <select class="form-select pointer" id="group-list" aria-label="Large select example">
                            <option value="KAZIPET" >Kazipet</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center device_id_section" id="device_id_section">
                        <select class="form-select pointer" id="device_id" name="device_id">
                            <!-- <option value="PUMP_1" >PUMP_1</option> -->
                            <option value="PUMP_2" >PUMP_2</option>
                            
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid controls-grid mt-2 ">
            <div class="card card-hight p-2" style="grid-column: span 4;">
                <h3>SYSTEM CONTROLS</h3>

                <label for="pump-select">Select Pump</label>
                <div class="d-flex mt-2 mb-2">
                    <select class="form-select me-2" aria-label="Default select example" id='pump_id'>
                        <option value="1">PUMP_1</option>
                        <option value="2">PUMP_2</option>
                    </select>
                    <button class="btn btn-primary" onclick="insertPump()">Confirm</button>
                </div>

                <label for="priority-select" class="mt-2">Priority Type</label>
                <div class="d-flex mt-2">
                    <select class="form-select me-2" aria-label="Default select example" id="priority_id">
                        <option value="PRIORITY">Custom Priority</option>
                        <option value="THRESHOLD">Threshold Priority</option>
                    </select>
                    <button class="btn btn-primary" onclick="insertPriority()">Confirm</button>
                </div>
            </div>

            <div id="mainTank" class="card card-hight p-2" style="grid-column: span 4;"></div>
            <div class="card card-hight p-2" style="grid-column: span 4;">
                <h3>Distribution Motor</h3>
                <div id="distributionMotor" >
                </div>

            </div>
        </div>



        <div class="d-flex align-items-end justify-content-end my-3" >
            <div id="addNewTank" class="d-flex">
                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                   <!--  <div class="btn-group me-2" role="group" aria-label="First group">
                        <button class="btn btn-primary" onclick="showAddNewTank()">Add New Tank</button>
                    </div>-->
                    <div class="btn-group me-2" role="group" aria-label="First group">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tankModal">Tank Capacity</button>
                    </div> 
                    <div class="btn-group me-2" role="group" aria-label="Second group">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#priorityTable" >Tanks Priority</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid controls-grid ">
            <div class="card grid-card-hight p-2" style="grid-column: span 12;">
                <h2 style="text-align: center;">Tank Status Overview</h2>
                <div class="tank-status-table">
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th>Tank Name</th>
                                <th>Water Level</th>
                                <th>Valve Status</th>
                                <th>Current Status</th>
                                <th>Flow Rate (L/min)</th>
                                <th>Estimated Time</th>
                                <th>Consumed Time</th>
                                <th>Total Pumped water(L)</th>
                                <th>Capacity (L)</th>
                                <th>Voltage-1 (V)</th>
                                <th>Voltage-2 (V)</th>
                                <th>Gateway</th>
                                <th>Date Time</th>
                            </tr>
                        </thead>
                        <tbody id="statusTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="tanksGrid" class="grid tanks-grid mt-3">
            <!-- Tanks will be dynamically added here -->
        </div>
    </div>

</div>
</div>
</div>
</div>
<div class="modal fade" id="addNewTankModal" tabindex="-1" aria-labelledby="addNewTankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="addNewTankModalLabel">Add New Tank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container mt-4">
                    <h3 class="mb-3">Add New Tank</h3>
                    <form id="tankForm" class="needs-validation" novalidate> 
                        <div class="mb-3">
                            <label for="tankName" class="form-label">Tank Name</label>
                            <input type="text" class="form-control" id="tankName" placeholder="Enter Tank Name" required>
                            <div class="invalid-feedback">Please enter a valid tank name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacity (L)</label>
                            <input type="number" class="form-control" id="capacity" placeholder="Enter Tank Capacity in Liters" required>
                            <div class="invalid-feedback">Please enter a valid capacity.</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" id="saveTankButton" class="btn btn-primary" onclick="saveNewTank()">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="priorityTable" tabindex="-1" aria-labelledby="priorityTableLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tank Filling Priorities </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="col-12 p-2">

                <div id="priorityList" class="mb-2">

                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" id="saveTankButton" class="btn btn-primary" onclick="updateTankPriority()" >Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tankModal" tabindex="-1" aria-labelledby="tankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tankModalLabel">Update Tank Capacity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tank-form">
                    <div class="mb-3">
                        <!-- Dropdown to select a tank -->
                        <label for="tank-selector" class="form-label">Select Tank:</label>
                        <select id="tank-selector" class="form-select">
                            <option value="">--Select Tank--</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <!-- Input for capacity -->
                        <label for="tank-capacity" class="form-label">Enter Capacity (in liters):</label>
                        <input type="number" id="tank-capacity" class="form-control" placeholder="Enter capacity in liters" min="1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!-- Update button -->
                <button id="update-capacity-btn" class="btn btn-success">Update Capacity</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>






<!-- Modals links -->
</main>
<script>



    function insertPump() { 
        let db_name = document.getElementById('device_id').value.trim();
        let pump_id = document.getElementById('pump_id').value.trim();

        if (!db_name || !pump_id) {
            alert("Please enter both Device ID and Pump ID");
            return;
        }

        let data = JSON.stringify({
            database: db_name,
            pump_id: pump_id
        });
        if(confirm("Are you Sure?"))
        {

            fetch('insertpump.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: data
            })
    .then(response => response.text())  // First check the raw response
    .then(result => {
        console.log('Raw Response:', result);
        try {
            let json = JSON.parse(result);
            if (json.status === "success") {
                alert(json.message);
            } else {
                alert(`Error: ${json.message} - ${json.sql_error || 'Unknown error'}`);
            }
        } catch (e) {
            console.error('Invalid JSON response:', result);
            alert("Unexpected server response. Check console.");
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert("Error inserting pump data");
    });
}
}


function insertPriority() { 
    let db_name = document.getElementById('device_id').value.trim();
    let priority_id = document.getElementById('priority_id').value.trim();

    if (!db_name || !priority_id) {
        alert("Please enter both Device ID and Pump ID");
        return;
    }

    let data = JSON.stringify({
        database: db_name,
        priority_id: priority_id
    });


    if(confirm("Are you Sure?"))
    {

        fetch('insertpriority.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: data
        })

    .then(response => response.text())  // First check the raw response
    .then(result => {
        console.log('Raw Response:', result);
        try {
            let json = JSON.parse(result);
            if (json.status === "success") {
                alert(json.message);
            } else {
                alert(`Error: ${json.message} - ${json.sql_error || 'Unknown error'}`);
            }
        } catch (e) {
            console.error('Invalid JSON response:', result);
            alert("Unexpected server response. Check console.");
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert("Error inserting pump data");
    });
}
}



updatePriorityList();
updateDashboardData();
setInterval(updateDashboardData, 20000);
function showAddNewTank() {
    console.log("showAddNewTank called");
    let modal = new bootstrap.Modal(document.getElementById("addNewTankModal"));
    modal.show();
}

function updateTankPriority()
{
    const priorityItems = document.querySelectorAll(".priority-item");
    const updatedValues = [];

    priorityItems.forEach((item) => {
      const tankId = item.querySelector(".priority-value").id;
      const priorityValue = parseInt(item.querySelector(".priority-value").innerText, 10);

      updatedValues.push({ tankId, priorityValue });
  });

    sendUpdatedPriorities(updatedValues);
}

function sendUpdatedPriorities(updatedValues) {
    const deviceId =document.getElementById('device_id').value;
    if(confirm("Are you sure modify the priority of tanks?"))
    {
        $.ajax({
            url: "update-priorities.php",
            type: "POST",
            data: {
                device_id: deviceId,
                priorities: JSON.stringify(updatedValues)
            },
            success: function (response) {
                alert(response);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }
}
function motorOnOff(deviceId, operation)
{

    if(confirm("Are you sure you want to turn "+operation+" the Motor?"))
    {
        $.ajax({
            url: "update-motor-on-off-status.php",
            type: "POST",
            data: {
                ID: deviceId,
                STATUS: operation
            },
            success: function (response) {
                alert(response);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }


}

function toggleValve(tank_id, status)
{
    var check_sts="close";
    if(status==1)
    {
        check_sts='open';
    }

    if(confirm("Are you sure you want to "+check_sts+" the valve?"))
    {
        $.ajax({
            url: "update-tank-value-status.php",
            type: "POST",
            data: {
                tank_id: tank_id,
                status: status
            },
            success: function (response) {
                alert(response);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

}


function updatePriority(tankId, change)
{

    const priorityElement = document.getElementById(tankId);
    let currentPriority = parseInt(priorityElement.innerText, 10);
    const newPriority = currentPriority + change;

    if (newPriority < 1) {
        alert("Priority cannot be less than 1.");
        return;
    }
    priorityElement.innerText = newPriority;
}



function updatePriorityList()
{

    let deviceId = document.getElementById('device_id').value;

    $.ajax({
        url: 'fetch_status.php',
        method: 'POST',
        dataType: 'json',
        data:{device_id:deviceId, status: "PRIORITY"},
        success: function (response) {
            const html = `
            <div class="priority-list">
                ${response .sort((a, b) => a.priority - b.priority) .map(tank => `
                <div class="priority-item">
                <span>${tank.tank_id}</span>
                <div class="priority-controls">
                <button class="priority-btn" 
                onclick="updatePriority('${tank.tank_id}', -1)">
                -
                </button>
                <span class="priority-value" id="${tank.tank_id}">${tank.priority}</span>
                <button class="priority-btn" 
                onclick="updatePriority('${tank.tank_id}', 1)">
                +
                </button>
                </div>
                </div>
                `).join('')}
            </div>
            `;
            document.getElementById('priorityList').innerHTML = html;
        }
    });
}


function updateDashboardData() 
{
    let deviceId = document.getElementById('device_id').value;

    $.ajax({
        url: 'fechDashboardData.php',
        method: 'POST',
        dataType: 'json',
        data: { device_id: deviceId },
        success: function (response) 
        {
            let tableBody = document.getElementById('statusTableBody');
            tableBody.innerHTML = "";
            response.tankStatus.forEach(tank => {
                const statusClass = tank.tank_status === "Empty" ? "status-empty" : "status-full";
                const isReceivingWater = tank.current_status === "Filling";

                tableBody.innerHTML += `
                <tr>
                <td>${tank.tank_name}</td>
                <td class="${statusClass}">${tank.tank_status}</td>
                <td>${tank.valve_status}</td>
                <td>${tank.current_status}</td>
                <td>${isReceivingWater ? tank.flow_rate : 0}</td>
                <td>${tank.estimated_time}</td>
                <td>${tank.consumed_time}</td>
                <td>${tank.comsumed_water}</td>
                <td>${tank.capacity}</td>
                <td>${tank.voltage_1}</td>
                <td>${tank.voltage_2}</td>
                <td>${tank.gateway_id}</td>
                <td>${tank.date_time}</td>
                    </tr>`;
                });


            let tankCards = document.getElementById('tanksGrid');
            if (tankCards) 
            {
                    /*tankCards.innerHTML = response.tankStatus.map(tank => {
                        const isReceivingWater = tank.current_status === "Filling";
                        const isFull = tank.tank_status === "Full";
                        let percentFull = isFull ? 100 : 20;

                        return `
                        <div id="${tank.id}" class="card p-2" style="grid-column: span 4;">
                        <h3>${tank.tank_name}</h3>
                        <div class="tank">
                        <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                        <div class="water ${isReceivingWater ? 'filling' : ''}" style="height: ${percentFull}%"></div>
                        </div>
                        <div class="tank-info">
                        <span>Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                        <span>Capacity: ${tank.capacity} L</span>
                        </div>
                        <div class="tank-capacity">
                        Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                        </div>
                        <div class="col-12 text-center mt-3"> <small>Valve Open/Close? </small></div>
                        <div class="valve-controls col-12">

                        <button class="valve-btn valve-btn-on" onclick="toggleValve('${tank.tank_id}', 1)">Open</button>
                        <button class="valve-btn valve-btn-off" onclick="toggleValve('${tank.tank_id}', 0)">Close</button>

                        </div>
                        </div>`;
                    }).join('');*/




                /*tankCards.innerHTML = response.tankStatus.map(tank => {
                    
                    return `
                    
                   
                    ${isInactive ? '<div class="inactive-message">Inactive</div>' : ''}
                    <div class="tank ${isInactive ? 'inactive' : ''}">
                    <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                    <div class="water ${isReceivingWater ? 'filling' : ''}" style="height: ${percentFull}%"></div>
                    </div>
                    <div class="tank-info">
                    <span>Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                    <span>Capacity: ${tank.capacity} L</span>
                    </div>
                    <div class="tank-capacity">
                    Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                    </div>
                    <div class="col-12 text-center mt-3"> <small>Valve Open/Close? </small></div>
                    <div class="valve-controls col-12">
                    <button class="valve-btn valve-btn-on" onclick="toggleValve('${tank.tank_id}', '1')">Open</button>
                    <button class="valve-btn valve-btn-off" onclick="toggleValve('${tank.tank_id}', '0')">Close</button>
                    </div>
                    </div>`;
                }).join('');*/


                const currentTime = new Date();
                tankCards.innerHTML = response.tankStatus.map(tank => {
                    const tankTime = new Date(tank.date_time);
                    const timeDifference = (currentTime - tankTime) / (1000 * 60); 
                    const isInactive = timeDifference > 15;
                    const isReceivingWater = tank.current_status === "Filling";
                    const isFull = tank.tank_status === "Full";
                    let percentFull = isFull ? 100 : 20;

                    return `
                    <div id="${tank.id}" class="card p-2 " style="grid-column: span 4;">
                    <h3>${tank.tank_name}</h3>
                    ${isInactive ? '<div class="inactive-message">Inactive</div>' : ''}
                    <div class="tank ${isInactive ? 'inactive' : ''}">
                    <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                    <div class="water ${isReceivingWater ? 'filling' : ''}" style="height: ${percentFull}%"></div>

                    </div>
                    <div class="tank-info">
                    <span>Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                    <span>Capacity: ${tank.capacity} L</span>
                    </div>
                    <div class="tank-capacity">
                    Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                    </div>
                    <div class="col-12 text-center mt-3"> <small>Valve Open/Close? </small></div>
                    <div class="valve-controls col-12">
                    <button class="valve-btn valve-btn-on" onclick="toggleValve('${tank.tank_id}', '1')">Open</button>
                    <button class="valve-btn valve-btn-off" onclick="toggleValve('${tank.tank_id}', '0')">Close</button>
                    </div>
                        </div>`;

                    }).join('');

            }

            let mainTank = document.getElementById('mainTank');
            if (mainTank) 
            {
                mainTank.innerHTML = response.mainTankStatus.map(tank => {
                    const isReceivingWater = tank.current_status === "Filling";
                    const isFull = tank.tank_status === "FULL";
                    let percentFull = isFull ? 100 : 20;

                    return `
                    <h3>${tank.tank_name}</h3>
                    <div class="tank">
                    <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                    <div class="water ${isReceivingWater ? 'filling' : ''}" style="height: ${percentFull}%"></div>
                    </div>
                    <div class="tank-info">
                    <span>Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                    <span>Capacity: ${tank.capacity} L</span>
                    </div>
                    <div class="tank-capacity">
                    Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                        </div>`;
                    }).join('');
            }


            let inMotor = "", outMotor = "";

            response.motorsStatus.forEach(motor => {
                let isRunning = motor.running_status === "Running";
                let motorHTML = `
                <div class="motor-control">
                <h6>${motor.motor_id}</h6>
                <div class="motor-buttons">
                <button class="btn-motor btn-motor-on" onclick="motorOnOff('${motor.motor_id}', 'ON')">On</button>
                <button class="btn-motor btn-motor-off" onclick="motorOnOff('${motor.motor_id}', 'OFF')">Off</button>
                </div>
                </div>
                <div class="tank-info">
                <span>Status:</span>
                <span class="${isRunning ? 'status-active' : 'status-inactive'}">${isRunning ? 'Running' : 'Stopped'}</span>
                </div>
                <div class="flow-rate" style="display:none">Flow Rate: ${isRunning ? motor.flow_rate : 0} L/min</div>
                <div class="flow-rate">Date&Time: <b>${motor.date_time}</b></div>
                <div class="flow-rate">Volatges(V) : R = <b>${motor.ph_r_v}</b>, Y = <b>${motor.ph_y_v}</b>, B = <b>${motor.ph_b_v}</b></div>
                    <div class="flow-rate">Current(Amps) : R = <b>${motor.ph_r_i}</b>, Y = <b>${motor.ph_y_i}</b>, B = <b>${motor.ph_b_i}</b></div>`;

                    if (motor.flow === "IN") {
                        inMotor = motorHTML;
                    } else {
                        outMotor = motorHTML;
                    }
                });
            // document.getElementById('mainMotor').innerHTML = inMotor;
            document.getElementById('distributionMotor').innerHTML = outMotor;
        },
        error: function () {
            console.log('Failed to fetch data.');
        }
    });
}

$('#tankModal').on('show.bs.modal', function () {
    fetchTanks();
});

function fetchTanks() {
    let deviceId = document.getElementById('device_id').value;
    $.ajax({
        url: 'fetch_tanks.php',
        type: 'POST',
        data: { device_id: deviceId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                const tankSelector = $('#tank-selector');
                    tankSelector.empty(); // Clear existing options
                    tankSelector.append('<option value="">--Select Tank--</option>');
                    data.tanks.forEach(tank => {
                        tankSelector.append(
                    `<option value="${tank.tank_id}" data-capacity="${tank.capacity}">${tank.tank_id} (Capacity: ${tank.capacity}L)</option>`
                    );
                    });
                } else {
                    alert('Error fetching tanks!');
                }
            },
            error: function () {
                alert('Error connecting to server!');
            }
        });
}

    // Update capacity button click
$('#update-capacity-btn').click(function () {
    const tankId = $('#tank-selector').val();
    const newCapacity = $('#tank-capacity').val();

    if (!tankId) {
        alert('Please select a tank!');
        return;
    }

    if (!newCapacity || newCapacity <= 0) {
        alert('Please enter a valid capacity!');
        return;
    }
    confirm("Are you sure you want to update the tank's capacity?")
    {
        $.ajax({
            url: 'update_tank_capacity.php',
            type: 'POST',
            data: { tank_id: tankId, capacity: newCapacity },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    alert('Capacity updated successfully!');
                    $('#tank-capacity').val("");
                    fetchTanks();
                } else {
                    alert(data.message);
                }
            },
            error: function () {
                alert('Error updating capacity!');
            }
        });
    }
});

</script>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<?php
include(BASE_PATH."assets/html/body-end.php"); 
include(BASE_PATH."assets/html/html-end.php"); 
?>

