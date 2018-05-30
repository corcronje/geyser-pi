import time
import mysql.connector
import subprocess
import sys

dbHost = 'localhost'
dbUsername = 'pi'
dbPassword = 'raspberry'
dbDatabase = 'geyser_pi'

# RETURN A DB CONNECTION
def dbConnect():
    return mysql.connector.connect(
         user = dbUsername,
         password = dbPassword,
         host = dbHost,
         database = dbDatabase)

# GET THE VALUES FROM THE TEMPERATURE PROBES, AND LOG IT TO THE DB
def getProbeValues():
    
    geyserTemp = subprocess.check_output([sys.executable, "/home/pi/GeyserPi/Python/getGeyserTemp.py", "34"])
    reservoirTemp = subprocess.check_output([sys.executable, "/home/pi/GeyserPi/Python/getReservoirTemp.py", "34"])
    
    currentGeyserTemp = float(geyserTemp)
    currentReservoirTemp = float(reservoirTemp)
    
    conn = dbConnect()    
    cur = conn.cursor()    
    cur.execute('insert into temperature_values (created_at, geyser_temp, reservoir_temp) values (date_format(now(), "%Y-%m-%d %H:%i:%s"), ' + str(currentGeyserTemp) + ', ' + str(currentReservoirTemp) + ')')
    conn.commit()
    conn.close()
    return

# REPEAT EVERY 5 MINUTES
while True:
    getProbeValues()
    time.sleep(900)
