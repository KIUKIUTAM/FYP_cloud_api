import request, { IWorkspaceResponse } from '/@/api/http/request'

const HTTP_PREFIX = '/mission/api/v1'

/**
 * Set drone SN to the mission
 * @param workspaceId workspace id
 * @param missionId mission id
 * @param droneSn drone serial number
 * @returns response
 */
export const setMissionDeviceSn = async function (workspaceId: string, missionId: string, droneSn: string): Promise<IWorkspaceResponse<any>> {
  const url = `${HTTP_PREFIX}/workspaces/${workspaceId}/set_DeviceSn?mission_id=${missionId}&drone_sn=${droneSn}`
  const result = await request.post(url)
  return result.data
}

/**
 * Get mission list
 * @param workspaceId workspace id
 * @returns response with list of mission table entities
 */
export const getMissionList = async function (workspaceId: string): Promise<IWorkspaceResponse<any[]>> {
  const url = `${HTTP_PREFIX}/workspaces/${workspaceId}/get-mission`
  const result = await request.get(url)

  // Map the API response fields to match the expected format in the component
  if (result.data && result.data.data) {
    result.data.data = result.data.data.map((mission: any) => ({
      id: mission.mission_id,
      name: mission.mission_name,
      status: mapMissionStatus(mission.status),
      // Keep the original fields too
      mission_id: mission.mission_id,
      mission_name: mission.mission_name,
      user_id: mission.user_id,
      device_sn: mission.device_sn,
      location: mission.location,
      start_time: mission.start_time,
      end_time: mission.end_time,
      notes: mission.notes
    }))
  }

  return result.data
}

/**
 * Set mission completed
 * @param workspaceId workspace id
 * @param missionId mission id
 * @returns response
 */
export const setMissionCompleted = async function (workspaceId: string, missionId: string): Promise<IWorkspaceResponse<any>> {
  const url = `${HTTP_PREFIX}/workspaces/${workspaceId}/set_MissionCompleted?mission_id=${missionId}`
  const result = await request.post(url)
  return result.data
}

/**
 * Set mission planned
 * @param workspaceId workspace id
 * @param missionId mission id
 * @returns response
 */
export const setMissionPlanned = async function (workspaceId: string, missionId: string): Promise<IWorkspaceResponse<any>> {
  const url = `${HTTP_PREFIX}/workspaces/${workspaceId}/set_MissionPlanned?mission_id=${missionId}`
  const result = await request.post(url)
  return result.data
}

/**
 * Map API mission status to UI status
 * @param status The API status string
 * @returns The UI status string
 */
function mapMissionStatus (status: string): 'Pending' | 'In Progress' | 'Completed' {
  switch (status?.toLowerCase()) {
    case 'completed':
      return 'Completed'
    case 'in progress':
    case 'in_progress':
    case 'ongoing':
      return 'In Progress'
    case 'planned':
    case 'pending':
    default:
      return 'Pending'
  }
}
