#include <WiFi.h>
#include <HTTPClient.h>

const char* ssid = "UpDgo-2.4G";
const char* pass = "";
const char* serverName = "http://172.16.101.78/gasdetector2/apis/post-data.php";

const int pinMQ2 = 34; 
const int pinBuzzer = 23; 
const int ledVerde = 25; 
const int ledRojo = 26;

void setup() {
  Serial.begin(115200);
  pinMode(pinMQ2, INPUT);
  pinMode(pinBuzzer, OUTPUT);
  pinMode(ledVerde, OUTPUT);
  pinMode(ledRojo, OUTPUT);

  WiFi.begin(ssid, pass);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Conectado.");
}

// Sonido de alerta rápida para simular urgencia
void sonidoAlertaRápida() {
  for (int i = 0; i < 3; i++) {
    tone(pinBuzzer, 1000); // Tono agudo
    delay(100);
    tone(pinBuzzer, 1500); // Tono más agudo
    delay(100);
  }
  noTone(pinBuzzer);
}

void loop() {
  int lectura = analogRead(pinMQ2);
  
  if (lectura > 550) { 
    digitalWrite(ledRojo, HIGH);
    digitalWrite(ledVerde, LOW);
    sonidoAlertaRápida(); 
  } else {
    digitalWrite(ledRojo, LOW);
    digitalWrite(ledVerde, HIGH);
    noTone(pinBuzzer);
  }

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String httpRequestData = "valor=" + String(lectura);
    int httpResponseCode = http.POST(httpRequestData);

Serial.print("Código HTTP: ");
Serial.println(httpResponseCode);

if (httpResponseCode > 0) {
  String response = http.getString();
  Serial.println(response);
} else {
  Serial.println("Error en la petición");
}
    http.end();
  }
  delay(500); 
}