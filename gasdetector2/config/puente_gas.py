import serial
import requests
import time

try:
    arduino = serial.Serial('COM8', 9600, timeout=1)
    print("Conectado exitosamente al puerto COM8")
except Exception as e:
    print(f"Error: No se pudo abrir el puerto COM8. Detalle: {e}")
    print("Verifica que el ESP32 esté conectado y el puerto sea el correcto.")
    exit(1) 

URL_API = "http://localhost/Gas%20Detector/apis/post-data.php"

while True:
    try:
        # Leer línea del puerto serial
        linea = arduino.readline().decode('utf-8', errors='ignore').strip()

        if linea:
            if "Analog output:" in linea:
                valor_gas = linea.split(":")[1].strip()

                # Preparamos los datos para PHP
                datos = {
                    "valor": valor_gas
                }

                # Enviamos el POST a XAMPP
                respuesta = requests.post(URL_API, data=datos)
                
                print(f"Dato enviado: {valor_gas} | Respuesta Servidor: {respuesta.text}")

    except Exception as e:
        print(f"Error en la transmisión: {e}")
    
    time.sleep(1)