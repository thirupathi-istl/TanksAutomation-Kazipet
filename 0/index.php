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

    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --success: #22c55e;
            --success-dark: #16a34a;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --warning-dark: #d97706;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        .wds-container {
            /* max-width: 1280px; */
            margin: 0 auto;
        }

        /* Modified grid to display all three sections in a single row */
        .wds-grid {
            display: grid;
            grid-template-columns: 5fr 4fr 3fr; /* 4×3 layout: Distribution (5), Voltage/Current (4), Main Tank (3) */
            gap: 1rem;
        }

        .wds-left-column, .wds-right-column {
            display: grid;
            gap: 0.5rem;
            height: 100%;
        }

        .wds-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--gray-100);
            padding: 1rem;
            height: 100%;
        }

        .wds-card-section {
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-100);
            margin-bottom: 0.75rem;
        }

        .wds-card-section:last-child {
            padding-bottom: 0;
            border-bottom: none;
            margin-bottom: 0;
        }

        .wds-section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .wds-section-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .wds-info-grid {
            display: grid;
            gap: 0.5rem;
        }

        .wds-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
        }


        .info-container {
    display: flex;
    /* gap: 1rem;   */
    justify-content: space-between;
    flex-wrap: wrap; /* Makes it responsive */
}

/* Each section stacks label and value */
.info-box {
    display: flex;
    flex-direction: column; /* Stack label and value */
    align-items: center;
    text-align: center;
    padding: 0.2rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
    flex: 1; /* Distribute equal space */
    min-width: 150px; /* Prevents shrinking on small screens */
}

/* Add spacing between label and value */
.info-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.5rem; /* Space between label and value */
}

.status-badge,
.info-value {
    font-size: 0.875rem;
    font-weight: 600;
    /* width: 100%; */
    text-align: center;
    border-radius:10px ;
    padding: 2px 5px;
}

/* Status Badge Colors */
.status-badge-red {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
    padding: 5px;
    border-radius: 5px;
}

.status-badge-yellow {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
    padding: 5px;
    border-radius: 5px;
}

.status-badge-green {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
    padding: 5px;
    border-radius: 5px;
}

        .wds-info-col {
            display: flex;
            align-items: center; /* Align items vertically */
            padding: 0.5rem 0.75rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
            flex-direction: column; /* Stack label and value */
            text-align: center; /* Center text inside */
        }

        .wds-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            align-self: flex-start;
        }

         .flowRate, .total-pumped-water, .mainTankDateTime  {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-800);
            width: 100%;
            text-align: center; 
            margin-top: 0.25rem; 
        }
       
        .wds-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .wds-status-badge.wds-yellow {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .wds-status-badge.wds-red {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .wds-status-badge.wds-green {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }


        .sub-tank-red {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 5px;
            border-radius: 20px;
        }

        .sub-tank-green {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 5px;
            border-radius: 10px;
        }

        .wds-readings-grid {
            display: grid;
            grid-template-rows: repeat(2, auto);
            gap: 1rem;
        }

        .wds-reading-section {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .wds-reading-section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .wds-reading-section-header h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }

        .wds-reading-boxes {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .wds-reading-box {
            background: var(--gray-50);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            flex: 1;
            min-width: 100px;
        }

        .wds-reading-header {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-bottom: 0.25rem;
            justify-content: center;
        }

        .wds-reading-header span {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        .wds-reading-value {
            display: flex;
            align-items: center;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-800);
            justify-content: center;
        }

        .wds-controls-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            align-items: end;
        }

        .wds-control-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .wds-control-group label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-700);
        }

        .wds-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            background: white;
            font-size: 0.875rem;
            color: var(--gray-800);
            height: 2.25rem;
        }

        .wds-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        /* .wds-button-group {
            display: flex;
            gap: 0.5rem;
            width: 30%;
        } */

        /* .wds-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            width: 100%;
            height: 2.25rem;
        } */

        .wds-button-group {
            display: flex;
            gap: 0.5rem;
            width: 100%;
            max-width: 300px; /* Prevents excessive stretching */
            justify-content: flex-end; /* Align buttons to the right */
        }

        /* Button styling */
        .wds-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            flex: 1;
            min-width: 120px;
            max-width: 150px;
            height: 2.5rem;
            white-space: nowrap;
        }

        /* Responsive adjustments for small screens */
        @media (max-width: 480px) {
            .wds-button-group {
                flex-direction: column;
                width: 100%;
                max-width: 100%;
            }

            .wds-btn {
                width: 100%;
                max-width: 100%;
            }
        }

        /* Large screen adjustments */
        @media (min-width: 1024px) {
            .wds-button-group {
                max-width: 250px; /* Reduce width slightly for large screens */
            }

            .wds-btn {
                max-width: 130px; /* Reduce button max-width */
            }
        }


        .wds-btn-green {
            background: var(--success);
            color: white;
        }

        .wds-btn-green:hover {
            background: var(--success-dark);
        }

        .wds-btn-red {
            background: var(--danger);
            color: white;
        }

        .wds-btn-red:hover {
            background: var(--danger-dark);
        }

        /* Updated responsive design for the new layout */
        @media (max-width: 1024px) {
            .wds-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            /* .wds-button-group {
                grid-column: span 2;
                width: 30%;
            }
             */
            .wds-readings-boxes {
                flex-direction: row;
            }
        }

        @media (max-width: 766px) {
            .wds-readings-boxes {
                flex-direction: column;
                align-items: center;
            }
            
            .wds-reading-box {
                width: 80%;
                text-align: center;
            }
            
            .wds-reading-header {
                justify-content: center;
            }
        }


        .pump-info-container {
            display: flex;
            gap: 1rem; /* Space between Selected Pump and Selected Priority */
            justify-content: space-between;
            flex-wrap: wrap; /* Makes it responsive */
            padding: 0.5rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
        }

        /* Each section stacks label and value */
        .pump-info-box {
            display: flex;
            flex-direction: column; /* Stack label and value */
            align-items: center;
            text-align: center;
            padding: 0.1rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
            flex: 1; /* Distribute equal space */
            min-width: 150px; /* Prevents shrinking on small screens */
        }

        /* Add spacing between label and value */
        .pump-info-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 0.3rem;
            text-align: start;
        }

        .pump-info-value {
            font-size: 0.875rem;
            font-weight: 600;
            text-align: center;
            color: var(--gray-800);
        }

        .selected-info-red {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 5px;
            border-radius: 5px;
        }

        .selected-info-green {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 5px;
            border-radius: 5px;
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

        <div class="wds-container mt-3  ">
        <div class="wds-grid">
            <!-- Distribution Motor Card -->
            <div class="wds-card wds-status-card card border-0" id="distribution-motor-card">
                <!-- System Status Section -->
                <div class="wds-card-section">
                    <div class="wds-section-header d-flex justify-content-between align-item-center">
                        <h6><i class="bi bi-activity text-primary"></i> Distribution Motor</h6>
             
                        <div class="wds-button-group">
                            <button class="wds-btn wds-btn-green" id="distribution_ON"><i class="bi bi-power"></i> On</button>
                            <button class="wds-btn wds-btn-red" id="distribution_OFF"><i class="bi bi-power"></i> Off</button>
                        </div>
                    </div>
                    
                    <div class="wds-controls-grid">
                        <div class="wds-control-group">
                            <label for="pump-select" class="heading-color">Select Pump</label>
                            <select id="pump_id" class="wds-select form-select">
                                <option value="1">PUMP_1</option>
                                <option value="2">PUMP_2</option>
                            </select>
                            <button class="btn btn-outline-primary" onclick="insertPump()">Confirm</button>
                        </div>

                        <div class="wds-control-group">
                            <label for="priority-select " class="heading-color">Select Priority Type</label>
                            <select id="priority_id" class="wds-select form-select">
                                <option value="PRIORITY">Custom Priority</option>
                                <option value="THRESHOLD">Threshold Priority</option>
                            </select>
                            <button class="btn btn-outline-primary" onclick="insertPriority()">Confirm</button>
                        </div>
                    </div>
                    
                    <div class="pump-info-container mt-2 mb-0 motor-bg-color">
                        <div class="pump-info-box motor-bg-color">
                            <span class="pump-info-label heading-color ">Selected Pump</span>
                            <span class="pump-info-value"  id="pump-style" > <span id="selected-pump">Not Selected</span> </span>
                        </div>

                        <div class="pump-info-box motor-bg-color">
                            <span class="pump-info-label heading-color">Selected Priority</span>
                            <span class="pump-info-value " id="priority-style"> <span id="selected-priority"> Not Selected</span></span>
                        </div>
                    </div>


                    <div class="info-container mt-1 motor-bg-color">
                        <div class="info-box motor-bg-color">
                            <span class="info-label heading-color">Motor Status</span>
                            <span class="status-badge status-badge-red" id="motorStatusCss" > <span id="motor-status"></span></span>
                        </div>

                        <div class="info-box motor-bg-color">
                            <span class="info-label heading-color">Last Updated</span>
                            <span class="info-value" id="last-updated"></span>
                        </div>
                    </div>

                </div>     
            </div>

            <!-- Voltage and Current Card -->
            <div class="wds-card card wds-status-card border-0" id="voltage-current-card">
                <div class="wds-card-section">
                    <div  class="text-center">
                        <h6><i class="bi bi-speedometer text-primary"></i> Motor Status</h6>
                    </div>
                    <div class="wds-readings-grid">
                        <!-- Voltage Section -->
                        <div class="wds-reading-section mt-2">
                            <div class="wds-reading-section-header">
                                <i class="bi bi-battery-charging text-primary"></i>
                                <h3>Voltage</h3>
                            </div>
                            <div class="wds-reading-boxes">
                                <div class="wds-reading-box">
                                    <div class="wds-reading-header">
                                        <i class="bi bi-battery"></i>
                                        <span>Phase R</span>
                                    </div>
                                    <span class="wds-reading-value"> <span id="voltage-r">0</span> V</span>
                                </div>
                                <div class="wds-reading-box">
                                    <div class="wds-reading-header">
                                        <i class="bi bi-battery"></i>
                                        <span>Phase Y</span>
                                    </div>
                                    <span class="wds-reading-value" ><span id="voltage-y">0</span> V</span>
                                </div>
                                <div class="wds-reading-box">
                                    <div class="wds-reading-header">
                                        <i class="bi bi-battery "></i>
                                        <span>Phase B</span>
                                    </div>
                                    <span class="wds-reading-value" ><span id="voltage-b">0</span> V</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Section -->
                        <div class="wds-reading-section">
                            <div class="wds-reading-section-header">
                                <i class="bi bi-lightning-charge-fill text-primary"></i>
                                <h3>Current</h3>
                            </div>
                            <div class="wds-reading-boxes">
                                <div class="wds-reading-box">
                                    <div class="wds-reading-header">
                                        <i class="bi bi-battery"></i>
                                        <span>Phase R</span>
                                    </div>
                                    <span class="wds-reading-value"><span id="current-r">0</span> A</span>
                                </div>
                                <div class="wds-reading-box">
                                    <div class="wds-reading-header">
                                        <i class="bi bi-battery"></i>
                                        <span>Phase Y</span>
                                    </div>
                                    <span class="wds-reading-value"><span id="current-y">0</span> A</span>
                                </div>
                                <div class="wds-reading-box">
                                    <div class="wds-reading-header">
                                        <i class="bi bi-battery"></i>
                                        <span>Phase B</span>
                                    </div>
                                    <span class="wds-reading-value"><span id="current-b">0</span> A</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Tank Card -->
            <div class="wds-card card border-0" id="main-tank-card">
                <div class="text-center">                  
                    <h6><i class="bi bi-droplet-half text-primary "></i> GLR TANK</h6>
                </div>

                <div class="wds-info-grid motor-bg-color">
                    <!-- <div class="wds-info-col motor-bg-color">
                        <span class="wds-label ">Capacity</span>
                        <span class="wds-value mt-1 wds-reading-value" id="main-tank-capacity">0 L</span>
                    </div> -->
                    <div class="wds-info-col motor-bg-color">
                        <span class="wds-label heading-color">Flow Rate</span>
                        <span class="flowRate mt-1 wds-reading-value" id="main-tank-flow-rate">0 L/Min</span>
                    </div>

                    <div class="wds-info-col motor-bg-color">
                        <span class="wds-label heading-color">Total Consumed Water</span>
                        <span class="total-pumped-water  wds-reading-value" style="margin-top:20px;" id="main-tank-consumed-water">0 L</span>
                    </div>

                    <div class="wds-info-col motor-bg-color">
                        <span class="wds-label heading-color">Date & Time</span>
                        <span class="mainTankDateTime mt-1 wds-reading-value"><span id="main-tank-date-time">0-0-0 0:0:0</span></span>
                    </div>
                    
                    <!-- <div class="wds-info-col motor-bg-color">
                        <span class="wds-label">Status</span>
                        <span class="wds-status-badge wds-yellow mt-1 wds-reading-value" id="main-tank-status">Empty</span>
                    </div> -->
                </div>
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="table-header-row-1">Tank Name</th>
                                <th class="table-header-row-1">Water Level</th>
                                <th class="table-header-row-1">Valve Status</th>
                                <th class="table-header-row-1">Current Status</th>
                                <th class="table-header-row-1">Flow Rate (L/min)</th>
                                <th class="table-header-row-1">Estimated Time</th>
                                <th class="table-header-row-1">Consumed Time</th>
                                <th class="table-header-row-1">Total Pumped water(L)</th>
                                <th class="table-header-row-1">Capacity (L)</th>
                                <th class="table-header-row-1">Voltage-1 (V)</th>
                                <th class="table-header-row-1">Voltage-2 (V)</th>
                                <th class="table-header-row-1">Gateway</th>
                                <th class="table-header-row-1">Date Time</th>
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
    .then(response => response.text()) 
    .then(result => {

        try {
            let json = JSON.parse(result);
            if (json.status === "success") {
                fetchPumpAndPriority();
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
                fetchPumpAndPriority();
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


function fetchPumpAndPriority() {
    let db_name = document.getElementById('device_id').value.trim();
    let pump=document.getElementById('selected-pump');
    let priority=document.getElementById('selected-priority');
    let pumpCSS=document.getElementById("pump-style");
    let priorityCSS=document.getElementById("priority-style");
    fetch("fetch_selected_pump_priority.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            database: db_name
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            if (data.selected_pump === "1") {
                
                pumpCSS.classList.add("selected-info-green");
                pump.innerHTML="PUMP_1";

            } else if (data.selected_pump === "2") {
                
                pumpCSS.classList.add("selected-info-green");
                pump.innerHTML="PUMP_2";
            } else {
             
                pumpCSS.classList.add("selected-info-red");
                pump.innerHTML="Not Selected";

            }

            if (data.selected_priority === "PRIORITY") {
                
                priorityCSS.classList.add("selected-info-green");
                priority.innerHTML="Custom Priority";
            } else if (data.selected_priority === "THRESHOLD") {
                
                priorityCSS.classList.add("selected-info-green");
                priority.innerHTML="Threshold Priority";
            } else {
                
                priorityCSS.classList.add("selected-info-red");
                priority.innerHTML="Not Selected";
            }
        } else {
            console.error("Error fetching data:", data.message);
        }
    })
    .catch(error => console.error("Request failed:", error));
}





updatePriorityList();
updateDashboardData();
fetchPumpAndPriority();
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

function updateDashboardData() {
            let deviceId = document.getElementById('device_id').value;

            $.ajax({
                url: 'fechDashboardData.php',
                method: 'POST',
                dataType: 'json',
                data: { device_id: deviceId },
                success: function (response) {
                    let tableBody = document.getElementById('statusTableBody');
                    if (tableBody) {
                        tableBody.innerHTML = "";
                        response.tankStatus.forEach(tank => {
                            const statusClass = tank.tank_status === "Empty" ? "status-empty" : "status-full";
                            const isReceivingWater = tank.current_status === "Filling";

                            tableBody.innerHTML += `
                            <tr>
                            <td >${tank.tank_name}</td>
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
                    }

                    let tankCards = document.getElementById('tanksGrid');
                    if (tankCards) {
                        const currentTime = new Date();
                        tankCards.innerHTML = response.tankStatus.map(tank => {
                            const tankTime = new Date(tank.date_time);
                            const timeDifference = (currentTime - tankTime) / (1000 * 60); 
                            var isInactive = timeDifference > 15;

                            var voltage = tank.voltage_1;
                            const isReceivingWater = tank.current_status === "Filling";
                            const isFull = tank.tank_status === "Full";
                            var status = "Inactive";

                            if (isInactive) {
                                status = "Inactive";
                            } else if (parseFloat(voltage) < 50) {
                                isInactive = true;
                                status = "Power Fail";
                            }

                            let percentFull = isFull ? 100 : 20;
                            const valveClass = tank.valve_status === "Open" ? "sub-tank-green" : "sub-tank-red";

                            return `
                            <div id="${tank.id}" class="card p-2" style="grid-column: span 4;">
                                <h3>${tank.tank_name}</h3>
                                ${isInactive ? '<div class="inactive-message" style="margin-top:50px">' + status + '</div>' : ''}
                                <div class="tank ${isInactive ? 'inactive' : ''}">
                                    <div class="tank-full-message ${isFull ? 'visible' : ''}">TANK FULL</div>
                                    <div class="water ${isReceivingWater && !isInactive ? 'filling' : ''}" style="height: ${percentFull}%"></div>
                                </div>
                                <div class="tank-info">
                                    <span class="heading-color">Flow Rate: ${isReceivingWater ? tank.flow_rate : 0} L/min</span>
                                    <span class="heading-color">Capacity: ${tank.capacity} L</span>
                                </div>
                                <div class="tank-capacity heading-color">
                                    Status: ${isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status}
                                </div>
                                <div class="col-12 text-center m-3 heading-color">
                                    <small>Valve Open/Close Status: <span class="${valveClass}">${tank.valve_status}</span></small>
                                </div>
                            </div>`;
                        }).join('');
                    }

                    // Update Main Tank - Using specific ID selectors instead of position-based selectors
                    if (response.mainTankStatus && response.mainTankStatus.length > 0) {
                        const tank = response.mainTankStatus[0];
                        const isReceivingWater = tank.current_status === "Filling";
                        const isFull = tank.tank_status === "FULL";
                        
                        // Update Main Tank values using specific IDs
                        // document.getElementById('main-tank-capacity').textContent = `${tank.capacity} L`;
                        document.getElementById('main-tank-flow-rate').textContent = `${tank.flow_rate} L/Min`;
                        document.getElementById('main-tank-consumed-water').textContent = `${tank.comsumed_water} L`;
                        document.getElementById('main-tank-date-time').textContent = tank.date_time;
                        
                        // Update Status with appropriate class for color coding
                        // const statusElement = document.getElementById('main-tank-status');
                        // statusElement.innerText = isFull ? 'Full' : isReceivingWater ? 'Filling' : tank.tank_status;

                        // // Update status color class dynamically
                        // statusElement.classList.remove('wds-yellow', 'wds-green', 'wds-red'); // Remove old classes
                        // if (isFull) {
                        //     statusElement.classList.add('wds-green'); // Full = Green
                        // } else if (isReceivingWater) {
                        //     statusElement.classList.add('wds-yellow'); // Filling = Yellow
                        // } else {
                        //     statusElement.classList.add('wds-red'); // Empty or other statuses = Red
                        // }
                    }

                    // Update Motor and Voltage/Current information
                    if (response.motorsStatus && response.motorsStatus.length > 0) {
                        response.motorsStatus.forEach(motor => {
                            const isRunning = motor.running_status === "Running";
                            const onButton = document.getElementById("distribution_ON");
                            const offButton = document.getElementById("distribution_OFF");
                            const motorStatus = document.getElementById("motorStatusCss");
                            const lastUpdated = document.getElementById("last-updated");

                            // Set button onclick handlers
                            if (onButton) onButton.setAttribute("onclick", `motorOnOff('${motor.motor_id}', 'ON')`);
                            if (offButton) offButton.setAttribute("onclick", `motorOnOff('${motor.motor_id}', 'OFF')`);
                            
                            // Update motor status
                            if (motorStatus) {
                                motorStatus.textContent = isRunning ? "Running" : "Stopped";
                                motorStatus.classList.remove("status-badge-red", "status-badge-green");
                                motorStatus.classList.add(isRunning ? "status-badge-green" : "status-badge-red");
                            }
                            
                            // Update last updated time
                            if (lastUpdated) lastUpdated.textContent = motor.date_time;
                            
                            // Update voltage and current readings
                            document.getElementById("voltage-r").textContent = motor.ph_r_v;
                            document.getElementById("voltage-y").textContent = motor.ph_y_v;
                            document.getElementById("voltage-b").textContent = motor.ph_b_v;

                            document.getElementById("current-r").textContent = motor.ph_r_i;
                            document.getElementById("current-y").textContent = motor.ph_y_i;
                            document.getElementById("current-b").textContent = motor.ph_b_i;
                        });
                    }
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

