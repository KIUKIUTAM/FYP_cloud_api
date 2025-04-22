<?php
function getMissionsPage() {
  require_once '../_inc/db.inc.php'; 
  $pdo = dbconnect();

  if (isset($_SESSION['userid'])) {
    $user_id = $_SESSION['userid'];
  } else {
    echo '<p>Please <a href="../login/">login</a> to access the dashboard.</p>';
    return;
  }

  // Fetch mission data from the database using PDO
  $query = "SELECT mission_id, mission_name, status, start_time FROM missiontable WHERE user_id = ? ORDER BY start_time DESC";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$user_id]);
  $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!empty($missions)) {
    $missionId = $missions[0]['mission_id'];
  }
  
  ?>
  <main class="col-md-9 mx-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Missions</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        <!-- Additional buttons can go here -->
      </div>
    </div>

    <!-- Search input -->
    <div class="input-group mb-3">
      <input type="text" id="searchInput" class="form-control" placeholder="Search for missions..." onkeyup="filterTable()">
    </div>

  <?php if (count($missions) > 0) { ?>
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
      <?php foreach ($missions as $row) { 
          $missionId = htmlspecialchars($row['mission_id']);
          $missionName = htmlspecialchars($row['mission_name']);
          $status = htmlspecialchars($row['status']);
          $startTime = htmlspecialchars($row['start_time']); 
      ?>
        <tr id="mission-<?php echo $missionId; ?>">
          <td><?php echo $missionId; ?></td>
          <td><?php echo $missionName; ?></td>
          <td><?php echo date('Y-m-d', strtotime($startTime)); ?></td>
          <td><?php echo ucfirst($status); ?></td>
          <td>
          <a href="?page=3&id=<?php echo urlencode($missionId); ?>" class="btn btn-primary btn-sm">View</a>

            <!-- <a href="mission.inc.php" data-mission-id="<?php /*echo $missionId; */?>" class="btn btn-danger btn-sm delete-mission">Delete</a> -->
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  <?php } else { ?>
    <p>No missions found.</p>
  <?php } ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteButtons = document.querySelectorAll('.delete-mission');

        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                var missionId = this.getAttribute('data-mission-id');

                if (confirm('Are you sure you want to delete this mission?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '../dashboard/_main/delete_mission.inc.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Remove the mission row from the table
                            var row = document.getElementById('mission-' + missionId);
                            if (row) {
                                row.parentNode.removeChild(row);
                            }
                        }
                    };
                    xhr.send('id=' + encodeURIComponent(missionId));
                }
            });
        });
    });
  </script>


    <script>
      function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('missionsTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) {
          const tr = trs[i];
          const tds = tr.getElementsByTagName('td');
          let found = false;
          
          for (let j = 0; j < tds.length; j++) {
            const td = tds[j];
            if (td) {
              if (td.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
              }
            }
          }
          tr.style.display = found ? '' : 'none';
        }
      }

    </script>
  </main>
  <?php } ?>
