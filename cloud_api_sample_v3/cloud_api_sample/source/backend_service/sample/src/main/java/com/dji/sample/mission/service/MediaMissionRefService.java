package com.dji.sample.mission.service;

import com.dji.sample.mission.model.MediaMissionRefEntity;
import org.springframework.stereotype.Service;

import java.util.List;

/**
 * Service interface for media mission reference operations
 */
@Service
public interface MediaMissionRefService {
    /**
     * Find all media files associated with a specific mission
     * @param missionId The mission ID to find media files for
     * @return List of media mission reference entities
     */
    List<MediaMissionRefEntity> getMediaFilesByMissionId(Integer missionId);
    
    /**
     * Associate a media file with a mission
     * @param mediaMissionRef The media mission reference entity to insert
     * @return Number of rows affected
     */
    int createMediaMissionRef(MediaMissionRefEntity mediaMissionRef);
    
    /**
     * Find mission ID by file name
     * @param fileName The file name to find mission ID for
     * @return The mission ID or null if not found
     */
    Integer getMissionIdByFileName(String fileName);
    
    /**
     * Save a new media mission reference if it doesn't exist
     * @param fileName The file name
     * @param missionId The mission ID
     * @return true if successful, false otherwise
     */
    boolean saveIfNotExists(String fileName, Integer missionId);
}
