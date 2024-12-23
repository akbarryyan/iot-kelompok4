import paho.mqtt.client as mqtt
import mysql.connector

# Koneksi ke database MySQL
mydb = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="lampu_otomatis"
)

print("Koneksi ke database", mydb)

mycursor = mydb.cursor()

# Callback ketika terhubung ke broker MQTT
def on_connect(client, userdata, flags, rc):
    print("Connected with result code " + str(rc))
    # Subscribe ke topik yang sesuai dengan kode Wokwi
    client.subscribe("sensor/motion")

# Callback ketika pesan diterima
def on_message(client, userdata, msg):
    message = str(msg.payload, 'utf-8')  # Dekode payload menjadi string
    print(f"Pesan diterima di topik {msg.topic}: {message}")
    
    # Simpan pesan ke database hanya jika gerakan terdeteksi
    if "Gerakan terdeteksi" in message:
        sql = "INSERT INTO motion_data (motion_detected, timestamp) VALUES (%s, NOW())"
        val = (message,)
        mycursor.execute(sql, val)
        mydb.commit()
        print("Data disimpan ke database!")

# Inisialisasi client MQTT
client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

# Koneksi ke broker MQTT
client.connect("mqtt.my.id", 1883, 60)

# Jalankan loop MQTT
client.loop_forever()
