<?php
function getMapPage() {
  include '../_inc/db.inc.php';
  $pdo = dbconnect(); 

    // Use consistent session variable names
    if (isset($_SESSION['userid'])) {
      $user_id = $_SESSION['userid'];
    } else {
      echo '<p>Please <a href="../login/">login</a> to access the dashboard.</p>';
      return;
    }
    
    // Fetch mission data from the database
    $query = "SELECT mission_id, mission_name, location, start_time, end_time, status, notes FROM missiontable WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);  // Supply the user_id for the placeholder
    $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $missions_json = json_encode($missions);

  ?>
  
  <main class="col-md-9 mx-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Map</h1>
    </div>

    <div class="map-container mb-4">
      <div id="map" style="height: 600px; width: 100%;" class="rounded shadow"></div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="missionModal" tabindex="-1" aria-labelledby="missionModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="missionModalLabel">Mission Information</h5>
            <button type="button" class="btn btn-danger" id="closeModalButton">Close</button>
          </div>
          <div class="modal-body">
            <!-- Mission details will be injected here -->
            <p id="missionDetails"></p>
          </div>
          <div class="modal-footer d-flex justify-content-center">
            <button class="btn btn-primary" id="moreDetailButton">More Detail</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pass PHP missions data to JavaScript -->
    <script>
      const missions = <?php echo $missions_json; ?>;
    </script>

    <!-- Google Maps JavaScript API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBN3s1Zaa-s3p7rqP7MI2fP4VzdlhEeHCc&"></script>

    <!-- Map Initialization Script -->
    <script>
      let map;

      function initMap() {
        const hongKong = { lat: 22.3193, lng: 114.1694 };

        // Create map
        map = new google.maps.Map(document.getElementById("map"), {
          zoom: 12,
          center: hongKong,
          mapTypeId: "terrain",
        });

        // Add markers to the map from missions data
        missions.forEach((mission) => {
          if (mission.location) {
            const [latStr, lngStr] = mission.location.split(',');
            const position = {
              lat: parseFloat(latStr),
              lng: parseFloat(lngStr),
            };

            const marker = new google.maps.Marker({
              position: position,
              map: map,
              title: mission.mission_name,
            });

            // Attach click event to the marker
            marker.addListener('click', () => {
              const startTime = mission.start_time ? new Date(mission.start_time).toLocaleString() : 'N/A';
              const endTime = mission.end_time ? new Date(mission.end_time).toLocaleString() : 'N/A';

              // Set mission details in the modal
              document.getElementById('missionDetails').innerHTML = `
                <strong>Mission Name:</strong> ${mission.mission_name}<br>
                <strong>Start Time:</strong> ${startTime}<br>
                <strong>End Time:</strong> ${endTime}<br>
                <strong>Status:</strong> ${mission.status || 'N/A'}<br>
                <strong>Notes:</strong> ${mission.notes || 'N/A'}<br>
                <strong>Location:</strong> ${position.lat.toFixed(5)}, ${position.lng.toFixed(5)}
              `;

              // Show the modal
              $('#missionModal').modal('show');

              // Add event listener for 'More Detail' button
              document.getElementById('moreDetailButton').onclick = function() {
                // Redirect to mission detail page
                window.location.href = `../dashboard/?page=3&id=${encodeURIComponent(mission.mission_id)}`;
              };
            });
          }
        });
      }

      document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('closeModalButton').addEventListener('click', () => {
          $('#missionModal').modal('hide');
        });

        initMap();
      });
    </script>

    <!-- Bootstrap CSS and JS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Minified for better performance -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

  </main>

<?php } ?>
