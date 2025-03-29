<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Tank Management System</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        


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
            background: repeating-linear-gradient( 45deg, transparent, transparent 10px, rgba(255, 0, 43, 0.76) 10px, rgba(255, 0, 43, 0.76) 20px
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
            margin-top: 1rem;
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

        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .status-table th,
        .status-table td {
            padding: 0.75rem;
            text-align: center;
            border: 1px solid rgb(229, 231, 235);
        }

        .status-table th {
            background: rgb(243, 244, 246);
            font-weight: 600;
            color: rgb(31, 41, 55);
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
            max-height:500px;
        }
        .tank-status-table
        {
            height: 100%;
            overflow: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mb-2">
        <h1 class="text-center">Water Tank Pump Automation Management System</h1>
        <div class="row d-flex align-items-center">
            <div class="col-12  ">
                <div class="row d-flex justify-content-end align-items-center"> 
                    <div class="col-xl-3 col-lg-4 d-flex justify-content-end align-items-center">
                        <p class="m-0" id="update_time"><span class="text-body-tertiary">Updated On : </span><span id="auto_update_date_time"></span></p>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center">
                        <select class="form-select pointer" id="group-list" aria-label="Large select example">
                            <option value="KAZIPET" >Kazipet</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-6 d-flex align-items-center device_id_section" id="device_id_section">
                        <select class="form-select pointer" id="device_id" name="device_id">
                            <option value="PUMP_2" >PUMP_2</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid controls-grid mt-2 ">
            <div class="card card-hight p-2" style="grid-column: span 4;">
                <h3>Pumping Motor</h3>
                <div id="mainMotor">
                </div>
            </div>
            <div id="mainTank" class="card card-hight p-2" style="grid-column: span 4;"></div>
            <div class="card card-hight p-2" style="grid-column: span 4;">
                <h3>Distribution Motor</h3>
                <div id="distributionMotor" >
                </div>
            </div>
        </div>

        <div class="d-flex align-items-end justify-content-end my-3" style="height: 100%;">
            <div id="addNewTank" class="d-flex">
                <button class="btn btn-primary" onclick="showAddNewTank()">Add New Tank</button>
            </div>
        </div>

        <div class="grid controls-grid ">
            <div  class="card grid-card-hight p-2" style="grid-column: span 4;">

                <h3>Tank Filling Priorities <button class="btn btn-primary" onclick="updateTankPriority()" >Update</button></h3>
                <div id="priorityList">

                </div>


            </div>
            <div class="card grid-card-hight p-2" style="grid-column: span 8;">
                <h2 style="text-align: center;">Tank Status Overview</h2>
                <div class="tank-status-table">
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th>Tank Name</th>
                                <th>Water Level</th>
                                <th>Value Status</th>
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

    <script>

        updatePriorityList();
        updateTanksStatus();
        updateMainTankStatus();
        motorStatus();

        setInterval(function() {
            
            updateTanksStatus();
            updateMainTankStatus();
            motorStatus();          

        }, 20000);

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
            console.log(updatedValues)
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



        function updateTanksStatus()
        {
            let deviceId = document.getElementById('device_id').value;
            $.ajax({
                url: 'fetch_status.php',
                method: 'POST',
                dataType: 'json',
                data:{device_id:deviceId, status: "TABLE"},
                success: function (response) {
                    let tableBody = document.getElementById('statusTableBody');
                    tableBody.innerHTML = ""; 

                    response.forEach(tank => {
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
                        <td>${tank.comsumed_water} </td>
                        <td>${tank.capacity} </td>
                        <td>${tank.voltage_1} </td>
                        <td>${tank.voltage_2} </td>
                        <td>${tank.gateway_id} </td>
                        <td>${tank.date_time} </td>
                        </tr>
                        `;
                    });

                    let tankCards = document.getElementById('tanksGrid');
                    if (tankCards) {
                        const cardsHtml = response.map(tank => {
                            const isReceivingWater = tank.current_status === "Filling";
                            const isFull = tank.tank_status === "Full";
                            percentFull=20;
                            if(isFull)
                            {
                                percentFull=100;
                            }

                            return `
                            <div id="${tank.id}" class="card p-2" style="grid-column: span 4;">
                            <h3>${tank.tank_name}</h3>
                            <div class="tank">
                            <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                            <div class="water ${isReceivingWater ? 'filling' : ''}"style="height: ${percentFull}%"></div>
                            </div>
                            <div class="tank-info">
                            <span>Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                            <span>Capacity: ${tank.capacity} L</span>
                            </div>
                            <div class="tank-capacity">
                            Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                            </div>
                            <div class="valve-controls">
                            <button class="valve-btn valve-btn-on" 
                            onclick="toggleValve('${tank.tank_id}', 1)">
                            Open
                            </button>
                            <button class="valve-btn valve-btn-off"
                            onclick="toggleValve('${tank.tank_id}', 0)">
                            Close
                            </button>
                            </div>
                            </div>
                            `;
                        }).join('');
                        tankCards.innerHTML = cardsHtml;
                    } else {
                        console.log('Element with ID "tankCards" not found');
                    }


                },
                error: function () {
                    console.log('Failed to fetch table data.');
                }
            });
        }


        function updateMainTankStatus(){
            let deviceId = document.getElementById('device_id').value;
            $.ajax({
                url: 'fetch_main_tank_status.php',
                method: 'POST',
                dataType: 'json', 
                success: function (response) 
                {
                    let tankCards = document.getElementById('mainTank');
                    if (tankCards) 
                    {
                        const cardsHtml = response.map(tank => 
                        {
                            const isReceivingWater = tank.current_status === "Filling";
                            const isFull = tank.tank_status === "FULL";
                            percentFull=20;
                            if(isFull)
                            {
                                percentFull=100;
                            }
                            return `
                            <h3>${tank.tank_name}</h3>
                            <div class="tank">
                            <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                            <div class="water ${isReceivingWater ? 'filling' : ''}"style="height: ${percentFull}%"></div>
                            </div>
                            <div class="tank-info">
                            <span>Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                            <span>Capacity: ${tank.capacity} L</span>
                            </div>
                            <div class="tank-capacity">
                            Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                            </div> `;
                        }).join('');

                        tankCards.innerHTML = cardsHtml;
                    } else {
                        console.log('Element with ID "tankCards" not found');
                    }


                },
                error: function () {
                    console.log('Failed to fetch main-tank data.');
                }
            });
        }

        function motorStatus()
        {
            let deviceId = document.getElementById('device_id').value;
            $.ajax({
                url: 'fetch_motors_status.php',
                method: 'POST',
                dataType: 'json',
                data: {device_id:deviceId },
                success: function (response) 
                {

                    if(response.length>0)
                    {
                        let inMotorCards = document.getElementById('mainMotor');
                        let outMotorCards = document.getElementById('distributionMotor');
                        let inMotor="";
                        let outMotor="";
                        inMotorCards.innerHTML = "";
                        outMotorCards.innerHTML = "";
                        for(let i=0; i<response.length; i++)
                        {
                            let isRunning = response[i].running_status === "Running";
                            if(response[i].flow=="IN")
                            {
                                inMotor = `
                                <div class="motor-control">
                                <h6>${response[i].motor_id}</h6>
                                <div class="motor-buttons">
                                <button class="btn-motor btn-motor-on" 
                                onclick="toggleMotor('${response[i].motor_id}', true)">
                                On
                                </button>
                                <button class="btn-motor btn-motor-off"
                                onclick="toggleMotor('${response[i].motor_id}', false)">
                                Off
                                </button>
                                </div>
                                </div>
                                <div class="tank-info">
                                <span>Status:</span>
                                <span class="${isRunning ? 'status-active' : 'status-inactive'}">
                                ${isRunning ? 'Running' : 'Stopped'}
                                </span>
                                </div>
                                <div class="flow-rate">
                                Flow Rate: ${isRunning ? response[i].flow_rate : 0} L/min
                                </div>
                                <div class="flow-rate">
                                Date&Time:<b> ${response[i].date_time}
                                </b></div>
                                `;  
                            }
                            else
                            {
                                outMotor = `
                                <div class="motor-control">
                                <h6>${response[i].motor_id}</h6>
                                <div class="motor-buttons">
                                <button class="btn-motor btn-motor-on" 
                                onclick="toggleMotor('${response[i].motor_id}', true)">
                                On
                                </button>
                                <button class="btn-motor btn-motor-off"
                                onclick="toggleMotor('${response[i].motor_id}', false)">
                                Off
                                </button>
                                </div>
                                </div>
                                <div class="tank-info">
                                <span>Status:</span>
                                <span class="${isRunning ? 'status-active' : 'status-inactive'}">
                                ${isRunning ? 'Running' : 'Stopped'}
                                </span>
                                </div>
                                <div class="flow-rate">
                                Flow Rate: ${isRunning ? response[i].flow_rate : 0} L/min
                                </div>

                                <div class="flow-rate">
                                Date&Time: <b>${response[i].date_time}
                                </b></div>`;  

                            }
                        }
                        inMotorCards.innerHTML = inMotor;
                        outMotorCards.innerHTML = outMotor;
                    }
                },
                error: function () 
                {
                    console.log('Failed to fetch motors data.');
                }
            });
}

</script>
</body>
</html>