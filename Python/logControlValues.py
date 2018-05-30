import RPi.GPIO as GPIO
import time
import mysql.connector
import subprocess
import sys

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(26,GPIO.OUT)
GPIO.setup(20,GPIO.OUT)
GPIO.setup(21,GPIO.IN)

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

def logControlValues():
        
    if GPIO.input(26) == 0: #if pin is low
        conn = dbConnect()    
        cur = conn.cursor()
        cur.execute('CALL incrementElementRuntime()')
        conn.commit()
        conn.close()

    if GPIO.input(20) == 0: #if pin is low
        conn = dbConnect()
        cur = conn.cursor()
        cur.execute('CALL incrementPumpRuntime()')
        conn.commit()
        conn.close()
    
while True:
    logControlValues()
    time.sleep(1)


        