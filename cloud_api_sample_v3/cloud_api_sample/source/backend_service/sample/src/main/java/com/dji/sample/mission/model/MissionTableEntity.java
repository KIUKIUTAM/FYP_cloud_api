package com.dji.sample.mission.model;

import com.baomidou.mybatisplus.annotation.*;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

import java.time.LocalDateTime;
import java.io.Serializable;


@TableName(value = "missiontable")
@Data
@Getter
@Setter
@Builder
@AllArgsConstructor
@NoArgsConstructor
public class MissionTableEntity implements Serializable {

    @TableId(type = IdType.AUTO)
    private int missionId;

    @TableField(value = "user_id")
    private int userId;

    @TableField(value = "device_sn")
    private String deviceSn;

    @TableField(value = "mission_name")
    private String missionName;

    @TableField(value = "mission_Flight_Route")
    private String missionPath;

    @TableField(value = "location")
    private String location;

    @TableField(value = "start_time")
    private LocalDateTime startTime;

    @TableField(value = "end_time")
    private LocalDateTime endTime;

    @TableField(value = "status")
    private String status;

    @TableField(value = "notes")
    private String notes;
}
