package com.dji.sample.mission.model;

import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.time.LocalDateTime;

@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class MissionTableDTO {
    
    private int missionId;
    private int userId;
    private String deviceSn;
    private String missionName;
    private String missionPath;
    private String location;
    private LocalDateTime startTime;
    private LocalDateTime endTime;
    private String status;
    private String notes;



}
