package com.dji.sample.mission.service;

/**
 * Service interface for mission-related operations
 */
public interface IMissionService {
    
    /**
     * Get mission ID by device serial number
     * @param deviceSn The device serial number
     * @return The mission ID associated with the device
     */
    Integer getMissionIdByDeviceSn(String deviceSn);
}
