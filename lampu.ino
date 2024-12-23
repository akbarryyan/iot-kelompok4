#include <WiFi.h>
#include <PubSubClient.h>

// Koneksi WiFi
#define WIFI_SSID "Ghinaa"
#define WIFI_PASSWORD "bolehaja"

// MQTT Broker
#define MQTT_SERVER "mqtt.my.id"
#define MQTT_PORT 1883
#define MQTT_TOPIC "sensor/motion"

// Pin
const int pirPin = 13;   // Sensor PIR
const int ledPin = 14;   // LED

WiFiClient espClient;
PubSubClient client(espClient);

void setup() {
  Serial.begin(115200);

  // Pin Mode
  pinMode(pirPin, INPUT);
  pinMode(ledPin, OUTPUT);

  // Inisialisasi awal
  digitalWrite(ledPin, LOW); // Matikan LED

  // Koneksi WiFi
  connectWiFi();

  // Koneksi ke MQTT Broker
  client.setServer(MQTT_SERVER, MQTT_PORT);
  reconnectMQTT();
}

void loop() {
  // Pastikan koneksi MQTT
  if (!client.connected()) {
    reconnectMQTT();
  }
  client.loop();

  // Membaca nilai dari sensor PIR
  int motionDetected = digitalRead(pirPin);

  if (motionDetected == HIGH) {
    digitalWrite(ledPin, HIGH); // Nyalakan LED
    Serial.println("Gerakan terdeteksi! LED menyala.");
    client.publish(MQTT_TOPIC, "Gerakan terdeteksi, Lampu Menyala!");
    delay(5000); // Menjaga LED tetap menyala selama 5 detik
    digitalWrite(ledPin, LOW); // Matikan LED setelah 5 detik
    Serial.println("LED dimatikan.");
  } else {
    Serial.println("Tidak ada gerakan. LED tetap mati.");
  }

  delay(1000); // Interval pembacaan
}

// Fungsi untuk koneksi ke WiFi
void connectWiFi() {
  Serial.print("Menghubungkan ke WiFi");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nTerhubung ke WiFi!");
}

// Fungsi untuk koneksi ke MQTT Broker
void reconnectMQTT() {
  while (!client.connected()) {
    Serial.print("Mencoba terhubung ke MQTT...");
    if (client.connect("ESP32Client")) {
      Serial.println("Terhubung ke broker MQTT!");
    } else {
      Serial.print("Gagal terhubung, rc=");
      Serial.print(client.state());
      Serial.println(". Coba lagi dalam 5 detik.");
      delay(5000);
    }
  }
}
