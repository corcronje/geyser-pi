import RPi.GPIO as GPIO
import time

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(26,GPIO.OUT)
GPIO.setup(20,GPIO.OUT)
GPIO.setup(21,GPIO.OUT)


def elementOn():
    GPIO.output(26,GPIO.LOW)
    return

def elementOff():
    GPIO.output(26,GPIO.HIGH)

while True:
    elementOn()
    time.sleep(1)
    elementOff()

