package com.dji.sample.delect.service.impl;

import com.dji.sample.delect.service.delectSendService;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.web.client.RestTemplate;
import org.springframework.http.HttpEntity;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpMethod;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.util.LinkedMultiValueMap;
import org.springframework.util.MultiValueMap;
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
        
        if(file_name == null || file_name.isEmpty()) {
            throw new IllegalArgumentException("File name cannot be null or empty");
        }

        if(mission_id == null || mission_id.isEmpty()) {
            mission_id = "0";
        }
        if(model == null || model.isEmpty()) {
            model = "crack";
        }

        // Use MultiValueMap for form data
        MultiValueMap<String, String> requestBody = new LinkedMultiValueMap<>();
        requestBody.add("file_name", file_name);
        requestBody.add("mission_id", mission_id);
        requestBody.add("model", model);

        // Set headers for form data
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_FORM_URLENCODED);
        
        HttpEntity<MultiValueMap<String, String>> request = new HttpEntity<>(requestBody, headers);

        try {
            ResponseEntity<String> response = restTemplate.postForEntity(url, request, String.class);
            if (!response.getStatusCode().is2xxSuccessful()) {
                throw new RuntimeException("Python API returned error: " + response.getBody());
            }
        } catch (Exception e) {
            throw new RuntimeException("Failed to send data to Python API: " + e.getMessage(), e);
        }
    }
}
