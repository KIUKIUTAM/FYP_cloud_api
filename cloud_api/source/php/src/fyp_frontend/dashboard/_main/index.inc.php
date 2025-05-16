<?php
function getDashboard(){
  require_once '../_inc/db.inc.php'; 
  $pdo = dbconnect(); 
  
  // Use consistent session variable names
  if (isset($_SESSION['userid'])) {
      $user_id = $_SESSION['userid'];
  } else {
      echo '<p>Please <a href="../login/">login</a> to access the dashboard.</p>';
      return;
  }

 // Handle form submission to create a new project
 if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_project'])) {
    $mission_name = htmlspecialchars($_POST['mission_name']);
    $address = htmlspecialchars($_POST['address']);
    $location = htmlspecialchars($_POST['location']);  // This is the coordinates field
    $start_time = htmlspecialchars($_POST['start_time']);
    $detect_type = htmlspecialchars($_POST['detect_type']);
    $status = 'planned';

    // Validate location
    if (empty($location)) {
        echo '<div class="alert alert-danger">Please enter a valid address to get location coordinates.</div>';
        return;
    }

    // Insert new mission into the database
    $stmt = $pdo->prepare("INSERT INTO missiontable (user_id, mission_name, address, location, start_time, detect_type, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $mission_name, $address, $location, $start_time, $detect_type, $status])) {
        // Get the newly created mission ID
        $mission_id = $pdo->lastInsertId();
        
        // Include MinIO helper functions
        require_once 'minio_config.inc.php';
        
        // Create mission folders in MinIO
        $folders_created = createMissionFolders($mission_id, true);
        
        if (!$folders_created) {
            // Log the error but continue (non-critical)
            error_log("Failed to create MinIO folders for mission ID: {$mission_id}");
        }
        
        // Redirect to refresh the page
        echo '<script>window.location.href = window.location.pathname;</script>';
        exit();
    } else {
        echo '<div class="alert alert-danger">Error creating mission. Please try again.</div>';
    }
  }

  // Fetch recent missions from the database
  $stmt = $pdo->prepare("SELECT mission_id, mission_name, status, start_time, location, detect_type FROM missiontable WHERE user_id = ? ORDER BY start_time DESC LIMIT 5");
  $stmt->execute([$user_id]);
  $recent_missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="col-md-9 mx-auto col-lg-10 px-md-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
  </div>

  <div class="row">
    <!-- Left Column: Create Drone Project -->
    <div class="col-md-6">
      <h2>Create Drone Mission</h2>
      <form method="POST" action="" id="missionForm">
        <div class="form-group">
          <label for="mission_name">Mission Title:</label>
          <input type="text" class="form-control" id="mission_name" name="mission_name" required>
        </div>
        <div class="form-group">
          <label for="address">Address:</label>
          <input type="text" class="form-control" id="address" name="address" required>
          <small class="form-text text-muted">press enter to genreate the coordinates</small>
        </div>
        <div class="form-group">
          <label for="location">location (Latitude, Longitude):</label>
          <input type="text" class="form-control" id="location" name="location" readonly style="background-color: #f8f9fa;">
          <small class="form-text text-muted">location will be automatically generated from address</small>
        </div>
        <div class="form-group">
          <label for="start_time">Start Date:</label>
          <input type="date" class="form-control" id="start_time" name="start_time" required>
        </div>
        <div class="form-group">
          <label for="detect_type">Default Detection Type:</label>
          <select class="form-control" id="detect_type" name="detect_type" required>
          <option value="crack">Crack Detection</option>
            <option value="car">Car Detection</option>
          </select>
        </div>
        <br>
        <button type="submit" name="create_project" class="btn btn-primary" id="submitBtn" disabled>Create Project</button>
      </form>
    </div>

    <!-- Right Column: Recent Missions Table -->
    <div class="col-md-6">
      <h2>Recent Missions</h2>
      <!-- Search input -->
      <div class="input-group mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search for missions..." onkeyup="filterTable()">
      </div>
      <table class="table table-striped table-hover" id="missionsTable">
        <thead class="thead-dark">
          <tr>
          <th>Mission ID</th>
            <th>Mission Title</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $count = 0;
            foreach ($recent_missions as $mission): 
              if ($count >= 6) break;
              $missionId = htmlspecialchars($mission['mission_id']);
              $missionName = htmlspecialchars($mission['mission_name']);
              $status = htmlspecialchars($mission['status']);
              $startTime = htmlspecialchars($mission['start_time']);
              $count++;
            ?>
            <tr>
              <td><?php echo $missionId; ?></td>
              <td><?php echo $missionName; ?></td>
              <td><?php echo date('Y-m-d', strtotime($startTime)); ?></td>
              <td><?php echo ucfirst($status); ?></td>
              <td>
              <a href="?page=3&id=<?php echo urlencode($missionId); ?>" class="btn btn-primary btn-sm">View</a>
              </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <hr>
  <div class="row mt-4 mb-5">
    <div class="col-12">
      <div id="map" style="height: 400px;"></div>
    </div>
  </div>
</main>

<div class="py-3"></div>

<!-- Load Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBN3s1Zaa-s3p7rqP7MI2fP4VzdlhEeHCc"></script>

<!-- JavaScript to Initialize the Map and Add Markers -->
<script>
  function initMap() {
    const options = {
      zoom: 12,
      center: { lat: 22.3193, lng: 114.1694 }, // Hong Kong
      mapTypeId: 'terrain'
    };

    // New map
    const map = new google.maps.Map(document.getElementById('map'), options);

    // Array of missions
    const missions = [
      <?php foreach ($recent_missions as $mission):
        $location_parts = explode(',', $mission['location']);
        $lat = isset($location_parts[0]) ? trim($location_parts[0]) : '0';
        $lng = isset($location_parts[1]) ? trim($location_parts[1]) : '0';
        $missionName = htmlspecialchars($mission['mission_name'], ENT_QUOTES);
      ?>
      {
        coords: { lat: <?php echo $lat; ?>, lng: <?php echo $lng; ?> },
        content: '<h5><?php echo $missionName; ?></h5>'
      },
      <?php endforeach; ?>
    ];

    // Loop through missions and add markers
    for (let i = 0; i < missions.length; i++) {
      addMarker(missions[i], map);
    }

    // Add Marker Function
    function addMarker(props, map) {
      const marker = new google.maps.Marker({
        position: props.coords,
        map: map
      });

      // Check for custom content
      if (props.content) {
        const infoWindow = new google.maps.InfoWindow({
          content: props.content
        });

        marker.addListener('click', function () {
          infoWindow.open(map, marker);
        });
      }
    }
  }

  // Load the map when the page has finished loading
  google.maps.event.addDomListener(window, 'load', initMap);
</script>

<script>
  const apiKey = 'AIzaSyBN3s1Zaa-s3p7rqP7MI2fP4VzdlhEeHCc';
  const MIN_LENGTH = 3;  
  let timeoutId = null;

 // Handle address input change
 const addressInput = document.getElementById('address');
  const locationInput = document.getElementById('location');
  const submitBtn = document.getElementById('submitBtn');
  const loadingIndicator = document.createElement('span');
  loadingIndicator.innerHTML = '&nbsp;<i class="fas fa-spinner fa-spin"></i>';
  loadingIndicator.style.display = 'none';
  addressInput.parentNode.appendChild(loadingIndicator);

  function geocodeAddress(address) {
      loadingIndicator.style.display = 'inline';
      submitBtn.disabled = true;
      
      console.log('Geocoding address:', address);
      
      fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(address)}&key=${apiKey}`)
          .then(response => {
              if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
          })
          .then(data => {
              console.log('Geocoding response:', data);
              if (data.results && data.results.length > 0) {
                  const geoLocation = data.results[0].geometry.location;
                  const coordinates = `${geoLocation.lat},${geoLocation.lng}`;
                  locationInput.value = coordinates;
                  submitBtn.disabled = false;
              } else {
                  locationInput.value = '';
                  submitBtn.disabled = true;
                  alert('Address not found. Please enter a valid address.');
              }
          })
          .catch(error => {
              console.error('Error geocoding address:', error);
              locationInput.value = '';
              submitBtn.disabled = true;
              
              if (error.message.includes('403')) {
                  alert('API Key error: Please check your Google Maps API key');
              } else {
                  alert('Error processing address. Please try again.');
              }
          })
          .finally(() => {
              loadingIndicator.style.display = 'none';
          });
  }

  // Handle Enter key press
  addressInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
          const address = this.value.trim();
          
          // Reset values if address is empty
          if (!address) {
              locationInput.value = '';
              submitBtn.disabled = true;
              return;
          }

          // Only geocode if minimum length is reached
          if (address.length >= MIN_LENGTH) {
              geocodeAddress(address);
          } else {
              alert('Please enter at least 3 characters for the address.');
          }
      }
  });

  // Handle form submission
  document.getElementById('missionForm').addEventListener('submit', function(e) {
      if (locationInput.value === '') {
          e.preventDefault();
          alert('Please enter a valid address to get location.');
      }
  });
</script>

<!-- JavaScript to Filter the Missions Table -->
<script>
  function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('missionsTable');
    const trs = table.getElementsByTagName('tr');

    for (let i = 1; i < trs.length; i++) {
      const tr = trs[i];
      let txtValue = '';
      for (let j = 0; j < tr.cells.length; j++) {
        txtValue += tr.cells[j].textContent.toLowerCase() + ' ';
      }
      tr.style.display = txtValue.indexOf(filter) > -1 ? '' : 'none';
    }
  }
</script>

<?php
}
?>