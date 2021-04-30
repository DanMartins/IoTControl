#!/usr/bin/env python
# -*- coding: utf-8 -*-
######################################################
## {IoTControl - AppControl - IoT_OpenLoop}         ##
######################################################
## { DanMartins/IoTControl is licensed under the    ##
##   GNU General Public License v3.0}               ##
######################################################
## Author: {DanMartins}                             ##
## Copyright: Copyright {2021}, {IoTControl}        ##
## Credits: [{https://domartins.wixsite.com/data}]  ##
## License: {GNU General Public License v3.0}       ##
## Version: {2021}.{04}.{25}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
######################################################
import RPi.GPIO as GPIO
import time
from datetime import datetime
import threading
import os
from encoder import Encoder
import sys
from ctypes import *
import pyctree
from array import *

#Set output to Print
debug2console = False

#avoid multiple initialization of control software
if os.path.isfile('/tmp/inic-exec'):
  if debug2console:
    print('Dupla inicia, encerrando programa.')
  sys.exit(0)  

#CPU config definition - hardware
enable_pin = 23
to_L293D_IN1 = 25
to_L293D_IN2 = 24
encoderA = 16
encoderB = 20
#CPU GPIO settings.
GPIO.setmode(GPIO.BCM)
GPIO.setup(enable_pin, GPIO.OUT)
GPIO.setup(to_L293D_IN1, GPIO.OUT)
GPIO.setup(to_L293D_IN2, GPIO.OUT)
GPIO.output(to_L293D_IN1, 1)
GPIO.output(to_L293D_IN2, 0)
#initialize control signal output
p = GPIO.PWM(enable_pin, 250)
p.start(0)
p.ChangeDutyCycle(0)  

#set varibles initial values
counterA = 0
counterB = 0
feedBack = 0.0
dControl = 0.0
pulsosvelcalc = 10.0
tempoStorage = 0.1
tempociclo = 0.1
velc = 0.0
velocfilter = 0.0
pwm = 0.0
KGain = 0.0
fatorK = 0.0
motorzm = 0.0
pwmzm = 0.0
grafpts = 0
#set variable of previous time as the initial time
Tant = time.time()
tempoLoop = tempoGrava = tempoProcesso = masterClock = Tant

def feedbackChanged(value):
  global counterA
  global counterB
  global velocfilter
  global pulsosvelcalc
  global masterClock
  #global feedBack

  #counter A is the delta encoder
  counterA = counterA + value - counterB
  #counter B is the previous encoder
  counterB = value
  
  #NOT OK YET - counter reach limit to calculate speed.
  #if (abs(counterA) >= pulsosvelcalc):
  #  feedBack = calcFeedback(velocfilter, masterClock)

def calcFeedback(tau, Tnow):
  global Tant
  global counterA
  global counterB
  global velc
  global tempociclo
  global pulsosvelcalc

  #get interval
  intervalo = Tnow-Tant

  #Process mark previous time
  Tant = Tnow
  if (counterA == 0):
    velc = 0.0
  else:
    #calcula velocidade em pulsos por segundo.
    dth0 = (float)(counterA)/(float)(intervalo)
    #Reset Counter
    counterA = 0
    #calcula velocidade em RPM, pulsos *60 sobre tempo * pulsos por volta
    dth0 = dth0 * 60.0 / pulsosvelcalc

    # Filtro (1/tau*s +1) nos derivadas tau
    dtau = (float)(intervalo + tau)
    velc = (float)(velc*(tau /(dtau))) + (float)(dth0*(intervalo/(dtau)))

  #return processed value
  return velc

def printSpeed():
  print (' ')
  print ('PWM: %s' %pwm)
  print ('Velocidade: %s' %feedBack)
  print ('Controle: %s' %dControl)

def printIntro():
  print "Obrigado por experimentar IoTControl"
  print "Configuracoes aplicadas:"
  print ('pulsosvelcalc: %s' %pulsosvelcalc)
  print ('tempoStorage: %s' %tempoStorage)
  print ('tempociclo: %s' %tempociclo)
  print ('grafpts: %s' %grafpts)

def saidaPWM(w1, w2):
  if w2:
    GPIO.output(to_L293D_IN1, 1)
    GPIO.output(to_L293D_IN2, 0)
    p.ChangeDutyCycle(w1)
  else:
    GPIO.output(to_L293D_IN1, 0)
    GPIO.output(to_L293D_IN2, 1)    
    p.ChangeDutyCycle(w1)

def main(): 
  global feedBack
  global dControl
  global pulsosvelcalc
  global tempoStorage
  global tempociclo
  global velocfilter
  global KGain
  global fatorK
  global pwm
  global motorzm
  global pwmzm
  global tempoProcesso
  global tempoGrava
  global tempoLoop
  global masterClock

  #write to file system temp - IoTControl process started.
  iniciado = open("/tmp/inic-exec", 'w+')

  #database connection
  conn = pyctree.connect(user="admin", password="ADMIN", database="ctreeSQL", host="localhost",port="6597")
  csr = conn.cursor()

  #initialize variables
  resret = 0.0
  xstamp = datetime.now() #(2016,9,27,16,00,00)
  pwm = 0.0
  #Load configuration from database only at init
  csr.execute("SELECT pulsosvelcalc, tempovelcalc, tempociclo, grafpts, intvelfil, pwmfreq, motorzm, pwmzm FROM configura")
  resstr = csr.fetchone()
  pulsosvelcalc = resstr[0]
  tempoStorage = resstr[1]
  tempociclo = resstr[2]
  grafpts = resstr[3]
  velocfilter = resstr[4]
  pwmfreq = resstr[5]
  motorzm = resstr[6]
  pwmzm = resstr[7]

  #CPU GPIO settings.
  GPIO.setmode(GPIO.BCM)
  #feedback class init - pulses encoder
  feedback_sensor = Encoder(encoderA, encoderB, feedbackChanged)
  #set control signal output frequency
  p.ChangeFrequency(pwmfreq)

  #write to file system temp - IoTControl process started.
  iniciado.close()

  #Print to console:
  if debug2console:
    printIntro()

  #control loop
  try:
    while True:
      #master clock
      masterClock = time.time()
      if (tempociclo <= (masterClock - tempoLoop)):
        tempoLoop = masterClock
        #feedback update speed calc - pulses encoder
        feedBack = calcFeedback(velocfilter, masterClock)
        #Print to console:
        if debug2console:
          printSpeed()

        #control - Open Loop
        pwm = dControl * fatorK
    
        #Output positive saturation
        if (pwm >= 100.0):  
          pwm = 100.0
        #Output negative saturation
        if (pwm <= -100.0):
          pwm = -100.0
    
        #positive output
        if pwm >= 0.0:
          saidaPWM(pwm, 1)
        #negative output
        else:
          saidaPWM((pwm*-1), 0)
        
        #Calculate the instantaneous Gain
        if pwm != 0.0:
          KGain = feedBack/pwm
        else:
          KGain = 0.0

        #Update data Storage
        if (tempoStorage <= (masterClock - tempoGrava)):
          tempoGrava = masterClock
          csr.execute("SELECT ajuste, valorK FROM controle")
          resstr = csr.fetchone()
          resret = float(resstr[0])
          #ajusta dControl
          dControl = resret
          fatorK = resstr[1]
          #INSERT
          xstamp = datetime.fromtimestamp(masterClock) #(2016,9,27,16,00,00)
          csr.execute("INSERT INTO dados (ajuste, velocidade, erro, Kmotor, tempo) VALUES(?,?,?,?,?)",(dControl, feedBack, pwm, KGain, xstamp))
          conn.commit()

      #measure the time in the loop
      tempoprocesso = time.time() - masterClock
      #if time in the loop is low then sleep
      if tempociclo > tempoprocesso:
        time.sleep(tempociclo - tempoprocesso)

      #Break the loop if pushed to abort
      if os.path.isfile('/tmp/abortar-exec'):
        if debug2console:
          print('Abortar')
        os.remove('/tmp/abortar-exec')
        os.remove('/tmp/inic-exec')
        break

  #exception raised in control loop
  except Exception:
    os.remove('/tmp/inic-exec')
    #write to file system temp - IoTControl process started.
    excepterror = open("/tmp/except-exec", 'w+')
    excepterror.close()
    if debug2console:
      print('Exception')
    pass

  #cleanup
  GPIO.cleanup()
  csr.close()
  conn.close()
  if debug2console:
    print ('Obrigado por usar IoTControl!')

if __name__ == '__main__':
    main()


