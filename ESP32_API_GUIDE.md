# ESP32 API Integration Guide

## API Security

Semua endpoint API dilindungi dengan:
1. **API Key Authentication** - Header `X-API-Key` wajib
2. **Rate Limiting** - Maksimal 12 request per menit per IP

## API Key

Tambahkan di setiap request header:
```
X-API-Key: siram-iot-2026-secure-key-change-this-in-production
```

**PENTING:** Ganti API key di production! Edit `.env`:
```env
APP_API_KEY=your-super-secret-key-here
```

## Available Endpoints

### 1. Get Active Schedule
**GET** `/api/jadwal/active`

Response jika ada jadwal aktif:
```json
{
  "message": "Active schedule retrieved",
  "data": {
    "id": 1,
    "nama": "Jadwal Pagi",
    "waktu_aktif_pertama": "06:00:00",
    "waktu_aktif_kedua": "18:00:00",
    "lama_operasi": 30,
    "aktif": true,
    "run_now": false,
    "last_run_at": null,
    "hari": ["senin", "selasa", "rabu", "kamis", "jumat"],
    "automation_flow": [
      {
        "device": "dinamo_x",
        "position": 50,
        "speed": 128,
        "pump": true,
        "duration": 10
      }
    ]
  }
}
```

**PENTING - Run Now Mode:**
- `run_now: true` = Jalankan SEKARANG, abaikan waktu terjadwal
- `run_now: false` = Mode normal, ikuti `waktu_aktif_pertama` dan `waktu_aktif_kedua`
- `last_run_at` = Timestamp terakhir kali dijalankan (untuk tracking)
- `automation_flow` = Array berisi langkah-langkah otomatis yang harus dijalankan

Response jika tidak ada:
```json
{
  "message": "No active schedule",
  "data": null
}
```

### 2. Get Device Settings
**GET** `/api/device/{deviceId}`

Contoh: `/api/device/pompa_01`

Response:
```json
{
  "message": "Device retrieved",
  "data": {
    "id": 1,
    "device_id": "pompa_01",
    "mode": "auto",
    "manual_on": false,
    "last_heartbeat": "2026-04-29T12:00:00.000000Z"
  }
}
```

### 3. Update Heartbeat
**POST** `/api/device/{deviceId}/heartbeat`

Kirim setiap 5 menit untuk update status online.

Response:
```json
{
  "message": "Heartbeat updated",
  "data": {
    "id": 1,
    "device_id": "pompa_01",
    "last_heartbeat": "2026-04-29T12:05:00.000000Z"
  }
}
```

### 4. Update Device Status
**POST** `/api/device/{deviceId}/status`

Body (JSON):
```json
{
  "manual_on": true
}
```

Response:
```json
{
  "message": "Device status updated",
  "data": {
    "id": 1,
    "device_id": "pompa_01",
    "mode": "manual",
    "manual_on": true
  }
}
```

## Error Responses

### 401 Unauthorized (API Key salah/tidak ada)
```json
{
  "error": "Unauthorized",
  "message": "Invalid or missing API key"
}
```

### 404 Not Found (Device tidak ditemukan)
```json
{
  "error": "Device not found",
  "message": "Device with ID 'pompa_01' not found"
}
```

### 429 Too Many Requests (Rate limit exceeded)
```json
{
  "error": "Too Many Requests",
  "message": "Rate limit exceeded. Please try again later."
}
```

## ESP32 Arduino Example

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* apiUrl = "http://your-server.com/api";
const char* apiKey = "siram-iot-2026-secure-key-change-this-in-production";
const char* deviceId = "pompa_01";

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
}

void loop() {
  // 1. Get active schedule
  String scheduleJson = getActiveSchedule();
  
  // 2. Parse JSON
  StaticJsonDocument<1024> doc;
  deserializeJson(doc, scheduleJson);
  
  if (doc["data"].isNull()) {
    Serial.println("No active schedule");
    delay(60000); // Check again in 1 minute
    return;
  }
  
  // 3. Check run_now mode
  bool runNow = doc["data"]["run_now"];
  String waktuPertama = doc["data"]["waktu_aktif_pertama"];
  String waktuKedua = doc["data"]["waktu_aktif_kedua"];
  int lamaOperasi = doc["data"]["lama_operasi"];
  
  if (runNow) {
    // RUN NOW MODE: Execute immediately
    Serial.println("Run Now mode active - executing immediately");
    executeAutomationFlow(doc["data"]["automation_flow"]);
  } else {
    // SCHEDULED MODE: Check current time
    String currentTime = getCurrentTime(); // Format: "HH:MM:SS"
    
    if (currentTime == waktuPertama || currentTime == waktuKedua) {
      Serial.println("Scheduled time reached - executing");
      executeAutomationFlow(doc["data"]["automation_flow"]);
    }
  }
  
  // 4. Send heartbeat every 5 minutes
  sendHeartbeat();
  
  delay(60000); // Check every 1 minute
}

void executeAutomationFlow(JsonArray flow) {
  for (JsonObject step : flow) {
    String device = step["device"];
    
    if (device == "dinamo_x" || device == "dinamo_y") {
      int position = step["position"];
      int speed = step["speed"];
      bool pump = step["pump"];
      int duration = step["duration"];
      
      // Move motor to position with speed
      moveMotor(device, position, speed);
      // Control pump
      digitalWrite(PUMP_PIN, pump ? HIGH : LOW);
      // Wait duration
      delay(duration * 1000);
      
    } else if (device == "relay_pump") {
      bool relayState = step["relay_state"];
      int duration = step["duration"];
      
      digitalWrite(PUMP_PIN, relayState ? HIGH : LOW);
      delay(duration * 1000);
      
    } else if (device == "servo_nozzle") {
      int angle = step["servo_angle"];
      int duration = step["duration"];
      
      servoNozzle.write(angle);
      delay(duration * 1000);
    }
  }
}

String getDeviceSettings() {
  HTTPClient http;
  http.begin(String(apiUrl) + "/device/" + deviceId);
  http.addHeader("X-API-Key", apiKey);
  
  int httpCode = http.GET();
  String payload = http.getString();
  http.end();
  
  return payload;
}

String getActiveSchedule() {
  HTTPClient http;
  http.begin(String(apiUrl) + "/jadwal/active");
  http.addHeader("X-API-Key", apiKey);
  
  int httpCode = http.GET();
  String payload = http.getString();
  http.end();
  
  return payload;
}

void sendHeartbeat() {
  HTTPClient http;
  http.begin(String(apiUrl) + "/device/" + deviceId + "/heartbeat");
  http.addHeader("X-API-Key", apiKey);
  http.addHeader("Content-Type", "application/json");
  
  int httpCode = http.POST("{}");
  http.end();
}
```

## Testing dengan cURL

```bash
# Get active schedule
curl -X GET http://localhost:8000/api/jadwal/active \
  -H "X-API-Key: siram-iot-2026-secure-key-change-this-in-production"

# Get device settings
curl -X GET http://localhost:8000/api/device/pompa_01 \
  -H "X-API-Key: siram-iot-2026-secure-key-change-this-in-production"

# Send heartbeat
curl -X POST http://localhost:8000/api/device/pompa_01/heartbeat \
  -H "X-API-Key: siram-iot-2026-secure-key-change-this-in-production" \
  -H "Content-Type: application/json"

# Update status
curl -X POST http://localhost:8000/api/device/pompa_01/status \
  -H "X-API-Key: siram-iot-2026-secure-key-change-this-in-production" \
  -H "Content-Type: application/json" \
  -d '{"manual_on": true}'
```

## Rate Limiting

- **Limit:** 12 request per menit per IP
- **Rekomendasi:** 
  - Check schedule: setiap 1 menit (1 request)
  - Heartbeat: setiap 5 menit (1 request)
  - Total: ~6 request per 5 menit = aman

## Run Now Feature - User Experience

Fitur "Run Now" memberikan pengalaman baru untuk pengguna:

1. **Toggle Switch** - Switch di sidebar jadwal untuk mengaktifkan/nonaktifkan run now
2. **Visual Feedback** - Badge "Run Now" dengan ikon petir muncul saat aktif
3. **Last Run Tracking** - Menampilkan "Terakhir dijalankan: X menit yang lalu"
4. **Instant Execution** - ESP32 akan langsung menjalankan jadwal tanpa menunggu waktu terjadwal
5. **Flexible Control** - Bisa diaktifkan/nonaktifkan kapan saja dari web interface

### Logika ESP32 untuk Run Now:

```cpp
if (runNow) {
  // Jalankan SEKARANG, abaikan waktu
  executeAutomationFlow();
} else {
  // Cek apakah waktu sekarang = waktu terjadwal
  if (currentTime == scheduledTime) {
    executeAutomationFlow();
  }
}
```

## Security Best Practices

1. **Ganti API Key di production**
2. **Gunakan HTTPS** (bukan HTTP) di production
3. **Simpan API key di EEPROM/Flash ESP32**, jangan hardcode
4. **Validasi response** sebelum digunakan
5. **Handle error** dengan baik (retry logic)

