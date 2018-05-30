import RPi.GPIO as GPIO
import time
import mysql.connector

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

# LOG THE TOTAL RUNTIMES
def logRuntimeValues():
    conn = dbConnect()    
    cur = conn.cursor()    
    cur.execute('CALL logRuntimeValues()')
    conn.commit()
    conn.close()

while True:
    logRuntimeValues()
    time.sleep(1500)