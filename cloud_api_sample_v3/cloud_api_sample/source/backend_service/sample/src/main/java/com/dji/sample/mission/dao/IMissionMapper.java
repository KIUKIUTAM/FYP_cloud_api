package com.dji.sample.mission.dao;
import com.baomidou.mybatisplus.core.mapper.BaseMapper;

import com.dji.sample.mission.model.MissionTableEntity;
import org.apache.ibatis.annotations.Select;
import org.apache.ibatis.annotations.Update;

import java.util.List;

public interface IMissionMapper extends BaseMapper<MissionTableEntity> {

    @Select("SELECT * FROM missiontable")
    List<MissionTableEntity> getAllMissionTables();
    
    @Update("UPDATE missiontable SET device_sn = #{deviceSn} WHERE mission_id = #{missionId}")
    int updateMission_DeviceSn(MissionTableEntity mission);

    @Select("SELECT mission_path FROM missiontable WHERE device_sn = #{deviceSn}")
    String getMissionPathByDeviceSn(String deviceSn);

    @Update("UPDATE missiontable SET status = #{status}, device_sn = #{deviceSn} WHERE mission_id = #{missionId}")
    int updateMissionStatusAndDeviceSn(MissionTableEntity mission);

    @Select("SELECT mission_id FROM missiontable WHERE device_sn = #{deviceSn}")
    Integer getMissionIdByDeviceSn(String deviceSn);

}
