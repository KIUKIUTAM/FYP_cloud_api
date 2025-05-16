package com.dji.sample.mission.model;

import com.baomidou.mybatisplus.annotation.*;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

import java.io.Serializable;

/**
 * Entity for media_mission_ref table that links media files to missions
 */
@TableName(value = "media_mission_ref")
@Data
@Getter
@Setter
@Builder
@AllArgsConstructor
@NoArgsConstructor
public class MediaMissionRefEntity implements Serializable {

    @TableId(value = "file_name")
    private String fileName;
    
    @TableField(value = "mission_id")
    private Integer missionId;

    @TableField(value = "longitude")
    private Double longitude;

    @TableField(value = "latitude")
    private Double latitude;

    @TableField(value = "altitude")
    private Double altitude;

    @TableField(value = "relative_altitude")
    private Double relativeAltitude;

    @TableField(value = "gimbal_yaw_degree")
    private Double gimbalYawDegree;

}
