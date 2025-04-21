<template>
    <div class="project-mission-wrapper">
        <div>
            <h1 style="color: black; margin-left: 30px;">Mission Assignment</h1>
        </div>

        <div class="container mt-4 pb-4">
            <!-- Mission Cards Grid -->
            <div class="row g-4">
                <div class="col-md-4" v-for="(drone, index) in drones" :key="index">
                    <div class="card mb-3 h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ drone.name }}</h5>
                            <small class="text-white-50">SN: {{ drone.sn }}</small>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="text-muted mb-3">Assigned Mission</h6>
                            <div class="mb-3 w-100">
                              <div v-if="getAssignedMission(drone.sn)">
                                <div class="d-flex align-items-center">
                                  <strong>{{ getAssignedMission(drone.sn)?.name }}</strong>
                                  <span class="badge ms-2" :class="[statusClass(getAssignedMission(drone.sn)?.status || 'Pending')]">
                                    {{ getAssignedMission(drone.sn)?.status }}
                                  </span>
                                </div>
                                <!-- Control buttons for assigned missions -->
                                <div class="mt-2 d-flex gap-2 justify-content-end">
                                  <button
                                    type="button"
                                    class="btn btn-success btn-sm"
                                    @click="confirmMissionCompletion(getAssignedMission(drone.sn), drone)"
                                  >
                                    Complete
                                  </button>
                                  <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    @click="confirmMissionPlanned(getAssignedMission(drone.sn), drone)"
                                  >
                                    Cancel
                                  </button>
                                </div>
                              </div>
                              <div v-else>
                                <em>No mission assigned</em>
                              </div>
                            </div>

                            <!-- Mission Assignment Controls -->
                            <div class="mb-3" v-if="!getAssignedMission(drone.sn)">
                              <label :for="'missionSelect-' + index" class="form-label">Select Mission</label>
                              <select
                                class="form-select"
                                :id="'missionSelect-' + index"
                                v-model="drone.selectedMission"
                                @change="changeButtonColor(drone)"
                              >
                                <option :value="null">Select a mission...</option>
                                <option
                                  v-for="mission in availableMissions.filter(m => m.status !== 'Completed')"
                                  :key="mission.id"
                                  :value="mission"
                                >
                                  {{ mission.name }}
                                </option>
                              </select>
                            </div>

                            <div class="d-flex justify-content-end" v-if="!getAssignedMission(drone.sn) && drone.selectedMission">
                              <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                @click="confirmMissionAssignment(drone)"
                                :class="{ 'btn-danger': pendingChanges.has(drone.sn) }"
                              >
                                Confirm
                              </button>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">Last updated: {{ drone.lastUpdate }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Existing Form -->
        <div class="container mt-4 pb-4">
            <form>
                <!-- ... existing form code ... -->
            </form>
        </div>

    </div>
</template>

<script lang="ts" setup>
import { OnlineDevice, EModeCode, OSDVisible, EDockModeCode, DeviceOsd, Device } from '/@/types/device'
import { RocketOutlined, EyeInvisibleOutlined, EyeOutlined, RobotOutlined, DoubleRightOutlined } from '@ant-design/icons-vue'
import { EHmsLevel, ELocalStorageKey } from '/@/types/enums'
import { useMyStore } from '/@/store'
import { getMissionList, setMissionCompleted, setMissionDeviceSn, setMissionPlanned } from '/@/api/mission'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import { ref, onMounted, nextTick } from 'vue'

const store = useMyStore()

interface Mission {
  id: number;
  name: string;
  status: 'Pending' | 'In Progress' | 'Completed';
  device_sn: string;
}

interface Drone {
  sn: string;
  name: string;
  selectedMission: Mission | null;
  lastUpdate: string;
}

// Initialize missions with default values that will be replaced by API data
const availableMissions = ref<Mission[]>([
  { id: 1, name: 'Area Survey', status: 'Pending', device_sn: '' },
  { id: 2, name: 'Security Patrol', status: 'In Progress', device_sn: '' },
  { id: 3, name: 'Mapping Task', status: 'Completed', device_sn: '' },
])

// Initialize drones array with a sample drone
const drones = ref<Drone[]>([
  // Sample drone that's always present
  {
    sn: 'SAMPLE-DRONE-001',
    name: 'Sample Drone',
    selectedMission: null,
    lastUpdate: new Date().toLocaleString()
  }
])

// Track which drones have pending mission changes
const pendingChanges = ref(new Set<string>())

// Function to check if a drone has pending changes
const isPendingConfirmation = (sn: string): boolean => {
  return pendingChanges.value.has(sn)
}

// Function to fetch missions from the API
const fetchMissions = async () => {
  try {
    const workspaceId = localStorage.getItem(ELocalStorageKey.WorkspaceId) || ''
    if (workspaceId) {
      const response = await getMissionList(workspaceId)
      if (response.code === 0 && response.data && response.data.length > 0) {
        console.log('Fetched missions:', response.data)
        availableMissions.value = response.data
      } else {
        console.warn('API returned empty or invalid mission data, using defaults')
      }
    } else {
      console.warn('No workspace ID found in localStorage')
    }
  } catch (error) {
    console.error('Error fetching missions:', error)
  }
}

// Populate drones array from the deviceInfo in the store and fetch missions
onMounted(async () => {
  // Fetch missions from API
  await fetchMissions()

  // If we have the main visible drone, add it first
  if (store.state.osdVisible && store.state.osdVisible.sn) {
    drones.value.push({
      name: store.state.osdVisible.callsign || 'Main Drone',
      sn: store.state.osdVisible.sn,
      selectedMission: null,
      lastUpdate: new Date().toLocaleString()
    })
  }

  // Loop through all devices in deviceInfo
  const deviceInfoEntries = Object.entries(store.state.deviceState.deviceInfo)
  for (const [sn, deviceData] of deviceInfoEntries) {
    // Skip if this device is already in the list (from osdVisible)
    if (drones.value.some(drone => drone.sn === sn)) {
      continue
    }

    drones.value.push({
      name: `Drone ${drones.value.length + 1}`,
      sn: sn,
      selectedMission: null,
      lastUpdate: new Date().toLocaleString()
    })
  }

  await nextTick()
})

// Function to find a mission that matches a drone SN
const getAssignedMission = (droneSn: string): Mission | null => {
  // Find a mission where device_sn matches the drone's SN
  return availableMissions.value.find(m => m.device_sn === droneSn) || null
}

const statusClass = (status: string) => {
  return {
    Pending: 'bg-warning text-dark',
    'In Progress': 'bg-primary',
    Completed: 'bg-success'
  }[status]
}

const updateMission = (drone: Drone) => {
  drone.lastUpdate = 'Just now'
  // Add any additional logic for mission updates here
}

const changeButtonColor = (drone: Drone) => {
  // Add drone SN to the set of pending changes
  pendingChanges.value.add(drone.sn)
  console.log(`Mission changed for ${drone.name}, pending confirmation`)
}

const confirmMissionAssignment = async (drone: Drone) => {
  if (drone.selectedMission) {
    try {
      const workspaceId = localStorage.getItem(ELocalStorageKey.WorkspaceId) || ''
      if (workspaceId) {
        const response = await setMissionDeviceSn(workspaceId, String(drone.selectedMission.id), drone.sn)
        if (response.code === 0) {
          // Remove from pending changes after successful confirmation
          pendingChanges.value.delete(drone.sn)

          // Clear any previous missions assigned to this drone
          availableMissions.value.forEach(m => {
            if (m.device_sn === drone.sn) {
              m.device_sn = ''
            }
          })

          // Update the mission's device_sn to match the drone
          drone.selectedMission.device_sn = drone.sn

          alert(`Mission "${drone.selectedMission.name}" assigned to drone ${drone.name} successfully!`)
          console.log('Mission assignment confirmed:', response.data)
          drone.lastUpdate = new Date().toLocaleString()

          // Refresh the mission list from the API to reflect changes
          await fetchMissions()
        } else {
          alert(`Failed to assign mission: ${response.message || 'Unknown error'}`)
          console.warn('Failed to confirm mission assignment:', response)
        }
      } else {
        alert('No workspace ID found. Please log in again.')
        console.warn('No workspace ID found in localStorage')
      }
    } catch (error) {
      alert('Error confirming mission assignment. See console for details.')
      console.error('Error confirming mission assignment:', error)
    }
  } else {
    alert('Please select a mission first')
  }
}

const confirmMissionCompletion = async (mission: Mission | null, drone: Drone) => {
  if (mission) {
    try {
      const workspaceId = localStorage.getItem(ELocalStorageKey.WorkspaceId) || ''
      if (workspaceId) {
        const response = await setMissionCompleted(workspaceId, String(mission.id))
        if (response.code === 0) {
          // Remove from pending changes after successful confirmation
          pendingChanges.value.delete(drone.sn)

          alert(`Mission "${mission.name}" completed successfully!`)
          console.log('Mission completion confirmed:', response.data)
          drone.lastUpdate = new Date().toLocaleString()

          // Refresh the mission list from the API to reflect changes
          await fetchMissions()
        } else {
          alert(`Failed to complete mission: ${response.message || 'Unknown error'}`)
          console.warn('Failed to confirm mission completion:', response)
        }
      } else {
        alert('No workspace ID found. Please log in again.')
        console.warn('No workspace ID found in localStorage')
      }
    } catch (error) {
      alert('Error confirming mission completion. See console for details.')
      console.error('Error confirming mission completion:', error)
    }
  } else {
    alert('Please select a mission first')
  }
}

const confirmMissionPlanned = async (mission: Mission | null, drone: Drone) => {
  if (mission) {
    try {
      const workspaceId = localStorage.getItem(ELocalStorageKey.WorkspaceId) || ''
      if (workspaceId) {
        const response = await setMissionPlanned(workspaceId, String(mission.id))
        if (response.code === 0) {
          // Remove from pending changes after successful confirmation
          pendingChanges.value.delete(drone.sn)

          alert(`Mission "${mission.name}" planned successfully!`)
          console.log('Mission planning confirmed:', response.data)
          drone.lastUpdate = new Date().toLocaleString()

          // Refresh the mission list from the API to reflect changes
          await fetchMissions()
        } else {
          alert(`Failed to plan mission: ${response.message || 'Unknown error'}`)
          console.warn('Failed to confirm mission planning:', response)
        }
      } else {
        alert('No workspace ID found. Please log in again.')
        console.warn('No workspace ID found in localStorage')
      }
    } catch (error) {
      alert('Error confirming mission planning. See console for details.')
      console.error('Error confirming mission planning:', error)
    }
  } else {
    alert('Please select a mission first')
  }
}

</script>

<style lang="scss">
.project-mission-wrapper {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 335PX;
    right: 0;
    z-index: 100;
    background: #f6f8fa;
    overflow-y: auto;  // Add scroll for overflow content
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
    min-height: 300px;
    display: flex;
    flex-direction: column;

    &:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        flex: 1 1 auto;
        overflow-y: auto;
    }
}

.list-group-item {
    background-color: rgba(255, 255, 255, 0.5);
}

.bg-warning.text-dark {
  color: #000 !important;
}
</style>
