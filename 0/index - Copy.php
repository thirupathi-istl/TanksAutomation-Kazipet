<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location-Based Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Location-Based Project Routing</h1>

    <!-- Location Details -->
    <div class="mb-3">
        <label for="projectLocation" class="form-label">Project Location</label>
        <input type="text" id="projectLocation" class="form-control" placeholder="Enter location name">
    </div>

    <!-- Motor Details -->
    <div class="mb-3">
        <label for="motorId" class="form-label">Motor ID</label>
        <input type="text" id="motorId" class="form-control" placeholder="Enter motor ID">
    </div>

    <!-- Tank Details -->
    <div class="mb-3">
        <label for="tankId" class="form-label">Tank Details</label>
        <div class="input-group">
            <input type="text" id="tankId" class="form-control" placeholder="Tank ID">
            <input type="number" id="tankCapacity" class="form-control" placeholder="Tank Capacity">
            <button id="addTank" class="btn btn-primary">Add</button>
        </div>
    </div>
    <div id="tankList" class="mt-3"></div>

    <!-- Set Priority -->
    <div class="mt-4">
        <h4>Set Tank Priority</h4>
        <div id="priorityList" class="mt-2"></div>
        <button id="saveData" class="btn btn-success mt-3">Save to Database</button>
    </div>
</div>

<script>
    let tanks = []; // Array to hold tanks dynamically

    // Add Tank to the List
    $('#addTank').on('click', function () {
        const tankId = $('#tankId').val().trim();
        const tankCapacity = $('#tankCapacity').val().trim();

        if (tankId && tankCapacity) {
            tanks.push({ id: tankId, capacity: tankCapacity });
            updateTankList();
            $('#tankId').val('');
            $('#tankCapacity').val('');
        } else {
            alert('Please fill in all tank details.');
        }
    });

    // Update Tank List
    function updateTankList() {
        let html = '<ul class="list-group">';
        tanks.forEach((tank, index) => {
            html += `<li class="list-group-item">
                        ${tank.id} (Capacity: ${tank.capacity})
                        <span class="text-danger ms-3" onclick="removeTank(${index})" style="cursor: pointer;">Remove</span>
                     </li>`;
        });
        html += '</ul>';
        $('#tankList').html(html);
        updatePriorityList();
    }

    // Remove Tank from the List
    function removeTank(index) {
        tanks.splice(index, 1);
        updateTankList();
    }

    // Update Priority List
    function updatePriorityList() {
        let html = '';
        tanks.forEach((tank, index) => {
            html += `<div class="mb-2">
                        <label>${tank.id} (Capacity: ${tank.capacity})</label>
                        <input type="number" class="form-control" id="priority-${index}" placeholder="Priority">
                     </div>`;
        });
        $('#priorityList').html(html);
    }

    // Save Data to Database using AJAX
    $('#saveData').on('click', function () {
        const location = $('#projectLocation').val().trim();
        const motorId = $('#motorId').val().trim();
        const priorities = tanks.map((tank, index) => ({
            id: tank.id,
            capacity: tank.capacity,
            priority: $(`#priority-${index}`).val().trim(),
        }));

        if (!location || !motorId || priorities.some(tank => !tank.priority)) {
            alert('Please complete all details and priorities.');
            return;
        }

        $.ajax({
            url: 'save_project.php',
            method: 'POST',
            data: {
                location,
                motorId,
                tanks: JSON.stringify(priorities),
            },
            success: function (response) {
                alert('Data saved successfully!');
                console.log(response);
                // Clear inputs
                $('#projectLocation, #motorId').val('');
                tanks = [];
                updateTankList();
            },
            error: function () {
                alert('Failed to save data.');
            }
        });
    });
</script>
</body>
</html>
