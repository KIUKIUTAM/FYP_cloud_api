package com.dji.sample.mission.service.impl;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.dji.sample.mission.dao.IMissionMapper;
import com.dji.sample.mission.model.MissionTableEntity;
import com.dji.sample.mission.service.MissionService;
import com.dji.sample.mission.service.IMissionService;

@Service
public class MissionServiceImpl implements MissionService, IMissionService {

    @Autowired
    private IMissionMapper mapper;

    @Override
    public List<MissionTableEntity> getAllMissions() {
        return mapper.getAllMissionTables();
    }

    @Override
    public MissionTableEntity getMissionById(int id) {
        MissionTableEntity entity = mapper.selectById(id);
        return entity;
    }

    @Override
    public void createMission(MissionTableEntity mission) {
        mapper.insert(mission);
    }

    @Override
    public void updateMission(MissionTableEntity mission) {
        mapper.updateById(mission);
    }

    @Override
    public void updateMissionStatusAndDeviceSn(MissionTableEntity mission) {
        mapper.updateMissionStatusAndDeviceSn(mission);
    }

    @Override
    public void deleteMission(String id) {
        mapper.deleteById(id);
    }
    
    @Override
    public void updateMissionDeviceSn(MissionTableEntity mission) {
        mapper.updateMission_DeviceSn(mission);
    }

    @Override
    public String getMissionPathByDeviceSn(String deviceSn) {
        return mapper.getMissionPathByDeviceSn(deviceSn);
    }

    @Override
    public Integer getMissionIdByDeviceSn(String deviceSn) {
        if(deviceSn == null || deviceSn.isEmpty()) {
            return null; // Default value if deviceSn is not provided
        }
        return mapper.getMissionIdByDeviceSn(deviceSn);
    }
}
