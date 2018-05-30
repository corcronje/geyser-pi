#!/usr/bin/env python

import RPi.GPIO as GPIO
import time
import mysql.connector
import subprocess
import sys
import os
import glob
import smtplib

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(26,GPIO.OUT)
GPIO.setup(20,GPIO.OUT)
GPIO.setup(21,GPIO.OUT)

GPIO.output(26,GPIO.HIGH)
GPIO.output(20,GPIO.HIGH)
GPIO.output(21,GPIO.HIGH)

dbHost = 'localhost'
dbUsername = 'pi'
dbPassword = 'raspberry'
dbDatabase = 'geyser_pi'


# GLOBAL VARS
maxGeyserTemp = 0
minGeyserTemp = 0
currentGeyserTemp = 0
maxReservoirTemp = 0
minReservoirTemp = 0
currentReservoirTemp = 0
elementIsAuto = 0
elementIsOn = 0
pumpIsAuto = 0
pumpIsOn = 0
holidayIsOn = 0
scheduledTemp = 0
deltaTemp = 7
autoTimeout = 0

# GLOBAL COUNTERS
smtpSleepCounter = 300 # 5 MINUTES
elementBackToAuto = 0
pumpBackToAuto = 0


# RETURN A DB CONNECTION
def dbConnect():
    return mysql.connector.connect(
         user = dbUsername,
         password = dbPassword,
         host = dbHost,
         database = dbDatabase)


# SWITCH THE ELEMENT ON, BUT DO NOT UPDATE THE DB
def elementOn():
    global holidayIsOn
    global elementIsOn
    if not holidayIsOn and geyserIsCold():
        GPIO.output(26,GPIO.LOW)        
        elementIsOn = 1
    return

# SWITCH THE ELEMENT ON, AND UPDATE THE DB
def turnElementOn():
    if not elementIsOn:
        conn = dbConnect()    
        cur = conn.cursor()    
        cur.execute('CALL setElementOn()')
        conn.commit()
        conn.close()
    elementOn()
    return

# SWITCH THE ELEMENT OFF, BUT DO NOT UPDATE THE DB
def elementOff():
    GPIO.output(26,GPIO.HIGH)
    global elementIsOn
    elementIsOn = 0
    return

# SWITCH THE ELEMENT OFF, AND UPDATE THE DB
def turnElementOff():
    if elementIsOn:
        conn = dbConnect()    
        cur = conn.cursor()    
        cur.execute('CALL setElementOff()')
        conn.commit()
        conn.close()
    elementOff()
    return

# SWITCH THE PUMP ON, BUT DO NOT UPDATE THE DB
def pumpOn():
    GPIO.output(20,GPIO.LOW)
    global pumpIsOn
    pumpIsOn = 1
    return

# SWITCH THE PUMP ON, AND UPDATE THE DB
def turnPumpOn():
    if not pumpIsOn:
        conn = dbConnect()    
        cur = conn.cursor()    
        cur.execute('CALL setPumpOn()')
        conn.commit()
        conn.close()            
    pumpOn()
    return

# SWITCH THE PUMP OFF, BUT DO NOT UPDATE THE DB
def pumpOff():
    GPIO.output(20,GPIO.HIGH)
    global pumpIsOn
    pumpIsOn = 0
    return

#SWITCH THE PUMP OFF, AND UPDATE THE DB
def turnPumpOff():
    if pumpIsOn:
        conn = dbConnect()    
        cur = conn.cursor()    
        cur.execute('CALL setPumpOff()')
        conn.commit()
        conn.close()            
    pumpOff()
    return

# CHECK IF THE GEYSER TEMP IS LOWER THAN THE THRESHOLDS
# ADDED 2 DEGREES UPWARDS VARIATION FOR SCHMIDT-TRIGGER
def geyserIsCold():
    global currentGeyserTemp
    global maxGeyserTemp
    return (((currentGeyserTemp + 2) < getScheduledTemp()) and (currentGeyserTemp < maxGeyserTemp))

# CHECK IF THE GEYSER TEMP IS ABOVE THE MAXIMUM
def geyserIsTooHot():
    global currentGeyserTemp
    global maxGeyserTemp
    return currentGeyserTemp > maxGeyserTemp

# CHECK IF THE GEYSER TEMP IS BELOW THE MINIMUM
def geyserIsTooCold():
    global currentGeyserTemp
    global minGeyserTemp
    return currentGeyserTemp < minGeyserTemp   

# IS THE RESERVOIR TEMP LOWER THAN THE GEYSER TEMP
def reservoirIsCold():
    global currentReservoirTemp
    global currentGeyserTemp
    global deltaTemp
    return (currentReservoirTemp < (currentGeyserTemp + deltaTemp))

# IS THE RESERVOIR TEMP HIGHER THAN THE GEYSER TEMP
def reservoirIsHot():
    return not reservoirIsCold()

# IS THE GEYSER TEMP LOWER THAN THE THRESHOLDS AND THE RESERVOIR TEMP IS LOWER THAN THE GEYSER TEMP
def canTurnElementOn():
    return (geyserIsCold() and reservoirIsCold())

# IS THE RESERVOIR TEMP HIGHER THAN THE GEYSER TEMP, AND THE GEYSER TEMP LOWER THAN THE THRESHOLDS
def canTurnPumpOn():
    return reservoirIsHot() and geyserIsCold()   

def setControls():

    global maxGeyserTemp
    global minGeyserTemp
    global maxReservoirTemp
    global minReservoirTemp
    global elementIsAuto
    global elementIsOn
    global pumpIsAuto
    global pumpIsOn
    global holidayIsOn
    global sheduledTemp
    global deltaTemp
    global autoTimeout
    global elementBackToAuto
    global pumpBackToAuto


    if holidayIsOn:
        turnElementOff()
    else:
        if elementIsAuto:
            if canTurnElementOn():
                turnElementOn()
            else:
                turnElementOff()
            elementBackToAuto = 0
        else:
            if elementIsOn:
                elementOn()
            else:
                elementOff()
                
            if elementBackToAuto > (autoTimeout * 60):
                elementIsAuto = 1
                elementBackToAuto = 0
                updateSystemStatus()
            else:
                elementBackToAuto = elementBackToAuto + 1

    if pumpIsAuto:
        if canTurnPumpOn():
            turnPumpOn()
        else:
            turnPumpOff()
        pumpBackToAuto = 0
    else:
        if pumpIsOn:
            pumpOn()
        else:
            pumpOff()
        
        if pumpBackToAuto > (autoTimeout * 60):
            pumpIsAuto = 1
            pumpBackToAuto = 0
            updateSystemStatus()
        else:
            pumpBackToAuto = pumpBackToAuto + 1
    return

def getProbeValues():
    global currentGeyserTemp
    global currentReservoirTemp
    geyserTemp = subprocess.check_output([sys.executable, "/home/pi/GeyserPi/Python/getGeyserTemp.py", "34"])
    reservoirTemp = subprocess.check_output([sys.executable, "/home/pi/GeyserPi/Python/getReservoirTemp.py", "34"])
    currentGeyserTemp = float(geyserTemp)
    currentReservoirTemp = float(reservoirTemp)
    return

def getScheduledTemp():
    conn = dbConnect()    
    cur = conn.cursor()    
    cur.execute('select temperature from temperature_schedule where `day` = dayofweek(now()) and `hour` = hour(now())')    
    conf = cur.fetchone()    
    conn.close()
    return conf[0]


def getSystemStatus():
      
    conn = dbConnect()    
    cur = conn.cursor()    
    cur.execute('select element_auto, element_on, pump_auto, pump_on, holiday_on, geyser_max_temp, reservoir_min_temp, delta_temp, auto_timeout, geyser_min_temp from system_config')    
    conf = cur.fetchone()    
    conn.close()
    
    global maxGeyserTemp
    global minGeyserTemp
    global maxReservoirTemp
    global minReservoirTemp
    global elementIsAuto
    global elementIsOn
    global pumpIsAuto
    global pumpIsOn
    global holidayIsOn
    global deltaTemp
    global autoTimeout
    
    maxGeyserTemp = conf[5]
    minGeyserTemp = conf[9]
    maxReservoirTemp = 0
    minReservoirTemp = conf[6]
    elementIsAuto = conf[0]
    elementIsOn = conf[1]
    pumpIsAuto = conf[2]
    pumpIsOn = conf[3]
    holidayIsOn = conf[4]
    deltaTemp = conf[7]
    autoTimeout = conf[8]
    
    return

def updateSystemStatus():
    global elementIsOn
    global pumpIsOn
    global elementIsAuto
    global pumpIsAuto
    conn = dbConnect()    
    cur = conn.cursor()    
    cur.execute('update system_config set element_on = ' + str(elementIsOn) + ', pump_on = ' + str(pumpIsOn) + ', element_auto = ' + str(elementIsAuto) + ', pump_auto = ' + str(pumpIsAuto) + ' where id = 0')
    conn.commit()
    conn.close()

def sendEmail(message):
    conn = dbConnect()    
    cur = conn.cursor()    
    cur.execute('select smtp_host, smtp_port, smtp_username, smtp_password, smtp_from_email, smtp_from_name, smtp_recipient_email from system_config')    
    conf = cur.fetchone()    
    conn.close()

    smtpHost = conf[0]
    smtpPort = conf[1]
    smtpUsername = conf[2]
    smtpPassword = conf[3]
    smtpFromEmail = conf[4]
    smtpFromName = conf[5]
    smtpRecipientEmail = conf[6]

    smtp = smtplib.SMTP(smtpHost + ':' + smtpPort)
    smtp.starttls()
    smtp.login(smtpUsername,smtpPassword)
    try:
        smtp.sendmail(smtpFromEmail, smtpRecipientEmail, message)
    except:
        print('SMTP Error')
    smtp.quit()

# SEND EMAIL NOTIFICATIONS, ONLY EVERY 5 MINUTES
def sendEmailNotifications():
    global smtpSleepCounter

    # WHEN THE GEYSER IS ABOVE THE MAXIMUM
    if geyserIsTooHot():
        if smtpSleepCounter >= 300:
            sendEmail('The geyser has overheated')
            print('hot', smtpSleepCounter)
        smtpSleepCounter = smtpSleepCounter + 1
    
    # WHEN THE GEYSER IS BELOW THE MINIMUM
    if geyserIsTooCold():
        print('cold')
        if smtpSleepCounter >= 300:
            sendEmail('The geyser is too cold')
            print('cold', smtpSleepCounter)
        smtpSleepCounter = smtpSleepCounter + 1

    if smtpSleepCounter > 300:
        smtpSleepCounter = 0

# INITIALLY WAIT 3 SECONDS AFTER STARTUP FOR CONNECTION TO ESTABLISH ETC.
time.sleep(10)

# SEND A NOTIFICATION WHEN THE SYSTEM STARTS
sendEmail('GeyserPi Started')

while True:
    getProbeValues()
    getSystemStatus()
    setControls()
    sendEmailNotifications()
    time.sleep(0)

