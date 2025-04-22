package com.dji.sample.mission.controller;

import lombok.extern.slf4j.Slf4j;
import java.util.List;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RestController;

import com.dji.sdk.common.HttpResultResponse;
import org.springframework.util.StringUtils;

import com.dji.sample.mission.model.MissionTableEntity;
import com.dji.sample.mission.service.MissionService;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.media.Schema;

/**
 * @version 0.1
 * @date 2025/3/2
 */
@Slf4j
@RestController
public class MissionController {

    private static final String PREFIX = "mission/api/v1";

    @Autowired
    private MissionService missionService;

    @Operation(summary = "get missionList", description = "get mission",
        parameters = {@Parameter(name = "workspace_id", description = "workspace id", schema = @Schema(format = "uuid"))})
    @GetMapping(PREFIX + "/workspaces/{workspace_id}/get-mission")
    HttpResultResponse<List<MissionTableEntity>> getMissionList (@PathVariable(name = "workspace_id") String workspaceId, HttpServletRequest req, HttpServletResponse rsp){
        List<MissionTableEntity> missionTablList = missionService.getAllMissions();
        return HttpResultResponse.success(missionTablList);
    }

    @Operation(summary = "set drone sn", description = "set the Device Sn to the mission",
        parameters = {@Parameter(name = "workspace_id", description = "workspace id", schema = @Schema(format = "uuid"))})
    @PostMapping(PREFIX + "/workspaces/{workspace_id}/set_DeviceSn")
    HttpResultResponse setMissionDeviceSn(@PathVariable(name = "workspace_id") String workspaceId,
                                        @RequestParam(name = "mission_id") String missionId,
                                        @RequestParam(name = "drone_sn") String droneSn) {
        
        // Validate input parameters
        if (StringUtils.isEmpty(missionId) || StringUtils.isEmpty(droneSn)) {
            return HttpResultResponse.error("mission_id and drone_sn cannot be empty");
        }
        
        // Get mission
        MissionTableEntity missionTableEntity = missionService.getMissionById(Integer.parseInt(missionId));
        if (missionTableEntity == null) {
            return HttpResultResponse.error("mission not found");
        }
        
        // Update drone SN
        try {
            missionTableEntity.setDeviceSn(droneSn);
            missionService.updateMissionDeviceSn(missionTableEntity);
            return HttpResultResponse.success();
        } catch (Exception e) {
            log.error("Failed to set drone SN for mission: " + missionId, e);
            return HttpResultResponse.error("fail to set drone");
        }
    }
    

    @Operation(summary =  "set mission completed and remove Drone sn", description = "set the mission completed and remove the device sn",
        parameters = {@Parameter(name = "workspace_id", description = "workspace id", schema = @Schema(format = "uuid"))})
    @PostMapping(PREFIX + "/workspaces/{workspace_id}/set_MissionCompleted")
    HttpResultResponse setMissionCompleted(@PathVariable(name = "workspace_id") String workspaceId,
                                        @RequestParam(name = "mission_id") String missionId) {
        
        // Validate input parameters
        if (StringUtils.isEmpty(missionId)) {
            return HttpResultResponse.error("mission_id cannot be empty");
        }
        
        // Get mission
        MissionTableEntity missionTableEntity = missionService.getMissionById(Integer.parseInt(missionId));
        if (missionTableEntity == null) {
            return HttpResultResponse.error("mission not found");
        }
        
        // Update mission status
        try {
            missionTableEntity.setStatus("completed");
            missionTableEntity.setDeviceSn(null);
            missionService.updateMissionStatusAndDeviceSn(missionTableEntity);
            return HttpResultResponse.success();
        } catch (Exception e) {
            log.error("Failed to set mission status for mission: " + missionId, e);
            return HttpResultResponse.error("fail to set mission");
        }
    }
    

    @Operation(summary =  "set mission planned and remove Drone sn", description = "set the mission planned and remove the device sn",
        parameters = {@Parameter(name = "workspace_id", description = "workspace id", schema = @Schema(format = "uuid"))})
    @PostMapping(PREFIX + "/workspaces/{workspace_id}/set_MissionPlanned")
    HttpResultResponse setMissionPlanned(@PathVariable(name = "workspace_id") String workspaceId,
                                        @RequestParam(name = "mission_id") String missionId) {
        
        // Validate input parameters
        if (StringUtils.isEmpty(missionId)) {
            return HttpResultResponse.error("mission_id cannot be empty");
        }
        
        // Get mission
        MissionTableEntity missionTableEntity = missionService.getMissionById(Integer.parseInt(missionId));
        if (missionTableEntity == null) {
            return HttpResultResponse.error("mission not found");
        }
        
        // Update mission status
        try {
            missionTableEntity.setStatus("planned");
            missionTableEntity.setDeviceSn(null);
            missionService.updateMissionStatusAndDeviceSn(missionTableEntity);
            return HttpResultResponse.success();
        } catch (Exception e) {
            log.error("Failed to set mission status for mission: " + missionId, e);
            return HttpResultResponse.error("fail to set mission");
        }
    }
}
