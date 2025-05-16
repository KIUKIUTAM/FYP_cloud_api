package com.dji.sample.mission.dao;

import com.baomidou.mybatisplus.core.mapper.BaseMapper;
import com.dji.sample.mission.model.MediaMissionRefEntity;
import org.apache.ibatis.annotations.Insert;

import org.apache.ibatis.annotations.Select;


import java.util.List;

/**
 * Mapper interface for the media_mission_ref table
 */
public interface IMediaMissionRefMapper extends BaseMapper<MediaMissionRefEntity> {
    
    /**
     * Find all media files associated with a specific mission
     */
    @Select("SELECT * FROM media_mission_ref WHERE mission_id = #{missionId}")
    List<MediaMissionRefEntity> getMediaFilesByMissionId(Integer missionId);
    
    /**
     * Associate a media file with a mission
     */
    @Insert("INSERT INTO media_mission_ref(file_name, mission_id, longitude, latitude, altitude, relative_altitude, gimbal_yaw_degree) " +
            "VALUES(#{fileName}, #{missionId}, #{longitude}, #{latitude}, #{altitude}, #{relativeAltitude}, #{gimbalYawDegree})")
    int insertMediaMissionRef(MediaMissionRefEntity mediaMissionRef);
    
    /**
     * Find mission ID by file name
     */
    @Select("SELECT mission_id FROM media_mission_ref WHERE file_name = #{fileName}")
    Integer getMissionIdByFileName(String fileName);
}
