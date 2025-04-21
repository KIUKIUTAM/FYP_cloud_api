package com.dji.sample.mission.service;
import com.dji.sample.mission.model.MissionTableEntity;
import java.util.List;
import org.springframework.stereotype.Service;

@Service
public interface MissionService {
    List<MissionTableEntity> getAllMissions();
    MissionTableEntity getMissionById(int id);
    void createMission(MissionTableEntity mission);
    void updateMission(MissionTableEntity mission);
    void deleteMission(String id);
    void updateMissionDeviceSn(MissionTableEntity mission);
    String getMissionPathByDeviceSn(String deviceSn);
    void updateMissionStatusAndDeviceSn(MissionTableEntity mission);
    Integer getMissionIdByDeviceSn(String deviceSn);
}