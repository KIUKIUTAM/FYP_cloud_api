package com.dji.sample.delect.service.impl;

import com.dji.sample.delect.service.delectSendService;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.web.client.RestTemplate;
import java.util.HashMap;
import java.util.Map;

@Service
public class delectSendServiceImpl implements delectSendService {

    @Value("${python.host}")
    private String pythonHost;

    @Value("${python.port}")
    private String pythonPort;

    private final RestTemplate restTemplate;

    public delectSendServiceImpl() {
        this.restTemplate = new RestTemplate();
    }

    @Override
    public void sendDelectData(String file_name, String mission_id, String model) {
        String url = String.format("http://%s:%s/auto-upload", pythonHost, pythonPort);
        
        Map<String, String> requestBody = new HashMap<>();
        requestBody.put("file_name", file_name);
        requestBody.put("mission_id", mission_id);
        requestBody.put("model", model);

        try {
            restTemplate.postForObject(url, requestBody, String.class);
        } catch (Exception e) {
            // Handle any exceptions that occur during the request
            throw new RuntimeException("Failed to send data to Python API: " + e.getMessage(), e);
        }
    }
}
