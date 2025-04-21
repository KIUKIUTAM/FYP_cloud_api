package com.dji.sample.mission.service.impl;

import com.baomidou.mybatisplus.core.conditions.query.QueryWrapper;
import com.dji.sample.mission.dao.IMediaMissionRefMapper;
import com.dji.sample.mission.model.MediaMissionRefEntity;
import com.dji.sample.mission.service.MediaMissionRefService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;

/**
 * Implementation of MediaMissionRefService
 */
@Service
public class MediaMissionRefServiceImpl implements MediaMissionRefService {

    @Autowired
    private IMediaMissionRefMapper mediaMissionRefMapper;

    @Override
    public List<MediaMissionRefEntity> getMediaFilesByMissionId(Integer missionId) {
        return mediaMissionRefMapper.getMediaFilesByMissionId(missionId);
    }

    @Override
    public int createMediaMissionRef(MediaMissionRefEntity mediaMissionRef) {
        return mediaMissionRefMapper.insertMediaMissionRef(mediaMissionRef);
    }

    @Override
    public Integer getMissionIdByFileName(String fileName) {
        return mediaMissionRefMapper.getMissionIdByFileName(fileName);
    }

    @Override
    public boolean saveIfNotExists(String fileName, Integer missionId) {
        // Check if the file name already exists in the database
        QueryWrapper<MediaMissionRefEntity> queryWrapper = new QueryWrapper<>();
        queryWrapper.eq("file_name", fileName);
        MediaMissionRefEntity existingRef = mediaMissionRefMapper.selectOne(queryWrapper);
        
        // If the reference doesn't exist, create it
        if (existingRef == null) {
            MediaMissionRefEntity newRef = new MediaMissionRefEntity();
            newRef.setFileName(fileName);
            newRef.setMissionId(missionId);
            return mediaMissionRefMapper.insertMediaMissionRef(newRef) > 0;
        }
        
        // Reference already exists
        return true;
    }
}
