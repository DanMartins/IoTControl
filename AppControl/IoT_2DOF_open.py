#!/usr/bin/env python
# -*- coding: utf-8 -*-
######################################################
## {IoTControl - AppControl - IoTPID_P}             ##
######################################################
## { DanMartins/IoTControl is licensed under the    ##
##   GNU General Public License v3.0}               ##
######################################################
## Author: {DanMartins}                             ##
## Copyright: Copyright {2021}, {IoTControl}        ##
## Credits: [{https://domartins.wixsite.com/data}]  ##
## License: {GNU General Public License v3.0}       ##
## Version: {2021}.{12}.{11}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
######################################################
import RPi.GPIO as GPIO
import time
from datetime import datetime
import threading
import os
#from AppControl import C_2DOF
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
PWM1 = 22 # Yaw
PWM2 = 23 # Pitch
encoder1A = 16 #Yaw
encoder1B = 20 #Yaw
encoder2A = 24 #Pitch
encoder2B = 25 #Pitch

#CPU GPIO settings.
GPIO.setmode(GPIO.BCM)
GPIO.setup(PWM1, GPIO.OUT)
GPIO.setup(PWM2, GPIO.OUT)
#initialize control signal output
pwm1 = GPIO.PWM(PWM1, 500)
pwm1.start(0)
pwm1.ChangeDutyCycle(0.0)

pwm2 = GPIO.PWM(PWM2, 500)
pwm2.start(0)
pwm2.ChangeDutyCycle(0.0)

#set varibles initial values
pi2 = 2.0*3.141592653589793
offset_yaw = 0.0
offset_pitch = -0.785398
Motor_MIN_Duty = 50.0
Motor_MAX_Duty = 99.0
Motor_RATIO = 2.0
counterA = 0
counterB = 0
feedBack1 = 0.0
feedBack2 = 0.0
dControl_1 = 0.0
dControl_2 = 0.0
pulsosvelcalc = 10.0
tempoStorage = 0.1
tempociclo = 0.1
velc = 0.0
velocfilter = 0.0
pwm = 0.0
out_1 = 0.0
out_2 = 0.0
KGain = 0.0
fator_A1 = fator_B1 = fator_C1 = 1.0
fator_A2 = fator_B2 = fator_C2 = 1.0
motorzm = 0.0
pwmzm = 0.0
grafpts = 0
fatora = 0.0004
fatorb = -0.1254
fatorc = 32.343
#set variable of previous time as the initial time
tempoLoop = time.time()
tempoGrava = tempoLoop
tempoProcesso = tempoLoop
masterClock = tempoLoop
Tant2 = tempoLoop
Tant = tempoLoop
#controller class init
#controle=C_2DOF(fator_A1,fator_B1,fator_C1,fator_A2,fator_B2,fator_C2)

def feedbackChanged1(value):
  global counterA
  #counter A is the encoder
  counterA = value

def feedbackChanged2(value):
  global counterB
  #counter B is the encoder
  counterB = value

def calcControl(Tnow):
  global Tant2
  global out_1
  global out_2
  global dControl_1
  global dControl_2
  global pwm
  global controle
  global feedBack1
  global feedBack2
  global pwmzm
  global tempociclo

  #get interval
  Ts = Tnow-Tant2
  #Process mark previous time
  Tant2 = Tnow
  #control inputs Feedback, Ts
  if (Ts > 0.0):
    #out_1, out_2  = controle.update(feedBack1, feedBack2, tempociclo)
    #out_1 = 13.0
    #out_2 = 52.0
    out_1  = Motor_MIN_Duty+(out_1/Motor_RATIO)
    out_2  = Motor_MIN_Duty+(out_2/Motor_RATIO)

    saidaPWM(out_1, out_2)

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

def printOut():
  print (' ')
  print(('PWM: %s' %pwm))
  print(('OUT_1: %s' %out_1))
  print(('OUT_2: %s' %out_2))
  print(('Feedback_1: %s' %feedBack1))
  print(('Feedback_2: %s' %feedBack2))
  print(('Control_1: %s' %dControl_1))
  print(('Control_2: %s' %dControl_2))

def printIntro():
  print("Obrigado por experimentar IoTControl")
  print("Configuracoes aplicadas:")
  print(('pulsosvelcalc: %s' %pulsosvelcalc))
  print(('tempoStorage: %s' %tempoStorage))
  print(('tempociclo: %s' %tempociclo))
  print(('grafpts: %s' %grafpts))

def saidaPWM(w1, w2):
  if (w1 < Motor_MIN_Duty): pwm1.ChangeDutyCycle(Motor_MIN_Duty)
  elif (w1 > Motor_MAX_Duty): pwm1.ChangeDutyCycle(Motor_MAX_Duty)
  else:  pwm1.ChangeDutyCycle(w1)

  if (w2 < Motor_MIN_Duty): pwm2.ChangeDutyCycle(Motor_MIN_Duty)
  elif (w2 > Motor_MAX_Duty): pwm2.ChangeDutyCycle(Motor_MAX_Duty)
  else: pwm2.ChangeDutyCycle(w2)

def main():
  global feedBack1
  global feedBack2
  global dControl_1
  global dControl_2
  global controle
  global out_1
  global out_2
  global fator_A1, fator_B1, fator_C1
  global fator_A2, fator_B2, fator_C2
  global pulsosvelcalc
  global tempoStorage
  global tempociclo
  global velocfilter
  global KGain
  global pwm
  global motorzm
  global pwmzm
  global fatora
  global fatorb
  global fatorc
  global tempoProcesso
  global tempoGrava
  global tempoLoop
  global masterClock

  #write to file system temp - IoTControl process started.
  iniciado = open("/tmp/inic-exec", 'w+')

  #database connection
  conn = pyctree.connect(user="admin", password="ADMIN", database="ctreeSQL", host="localhost",port="6597")
  csr = conn.cursor()
  commitCheck = 0

  #initialize variables
  resret = 0.0
  xstamp = datetime.now() #(2016,9,27,16,00,00)
  pwm = 0.0
  out_1 = 0.0
  out_2 = 0.0
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
  feedback_sensor1 = Encoder(encoder1A, encoder1B, feedbackChanged1)
  feedback_sensor2 = Encoder(encoder2A, encoder2B, feedbackChanged2)
  #set control signal output frequency
  pwm1.ChangeFrequency(pwmfreq)
  pwm2.ChangeFrequency(pwmfreq)

  pwm1.ChangeDutyCycle(0.0)# arm with 0% when 500Hz
  pwm2.ChangeDutyCycle(0.0)# arm with 0% when 500Hz

  time.sleep(0.2) # wait for 0.2 seconds to start.

  pwm1.ChangeDutyCycle(50.0)# set to 50% but range is 50% min speed to 100% full when 500Hz
  pwm2.ChangeDutyCycle(50.0)# set to 50% but range is 50% min speed to 100% full when 500Hz

  time.sleep(2.0) # wait for 2 seconds to start.

  #pwm1.ChangeDutyCycle(0.0)# arm with 0% when 500Hz
  #pwm2.ChangeDutyCycle(0.0)# arm with 0% when 500Hz

  #time.sleep(1.0) # wait for 0.2 seconds to start.

  #write to file system temp - IoTControl process started.
  iniciado.close()

  #Print to console:
  if debug2console:
    printIntro()

  #Init Timers right before loop
  tempoLoop = time.time()
  tempoGrava = tempoLoop
  tempoProcesso = tempoLoop
  masterClock = tempoLoop
  Tant2 = tempoLoop
  Tant = tempoLoop

  #control loop
  try:
    while True:
      #master clock
      masterClock = time.time()

      if (tempociclo <= (masterClock - tempoLoop)):
        tempoLoop = masterClock
        #Print to console:
        if debug2console:
          printOut()

        #control
        #controle.setPoint(dControl_1/100.0, dControl_2/100.0)

        #update feedback
        if (pulsosvelcalc == 0):
          feedBack1 = 0.0
        else:
          feedBack1 = (float(counterA)*(-pi2/pulsosvelcalc)) + offset_yaw #radians
        if (pulsosvelcalc == 0):
          feedBack2 = 0.0
        else:
          feedBack2 = (float(counterB)*(-pi2/pulsosvelcalc)) + offset_pitch #radians

        #control
        #calcControl(masterClock)
        out_1=dControl_1
        out_2=dControl_2
        saidaPWM(out_1, out_2)

        #Adjust feedback to graph
        feedBack1 = feedBack1 * 100.0 #radians x 100
        feedBack2 = feedBack2 * 100.0 #radians x 100

        #Update data Storage
        if (tempoStorage <= (masterClock - tempoGrava)):
          tempoGrava = masterClock
          csr.execute('SELECT ajuste_0, ajuste_1 FROM kgainmatrix')
          resstr = csr.fetchone()
          resret = float(resstr[0])
          #ajusta dControl
          dControl_1 = resret
          dControl_2 = resstr[1]

          # input Kp Ki e Kd
          #controle.setParam(fator_A1,fator_B1,fator_C1, fator_A2,fator_B2,fator_C2)
          # input Windup saturation, deadbands and Filter N 3_20.
          #controle.setWindup(Motor_MAX_Duty, motorzm, pwmzm, 3.0)

          #INSERT
          xstamp = datetime.fromtimestamp(masterClock) #(2016,9,27,16,00,00)
          commitCheck += 1
          csr.execute("INSERT INTO dados_mimo (ajuste_1, ajuste_2, feedback_1, feedback_2, out_1, out_2, tempo) VALUES(?,?,?,?,?,?,?)",(dControl_1, dControl_2, feedBack1, feedBack2, out_1, out_2, xstamp))
      #commit
      if (commitCheck >= 100):
        conn.commit()
        commitCheck = 0
      #measure the time in the loop
      tempoprocesso = time.time() - masterClock

      #Break the loop if pushed to abort
      if os.path.isfile('/tmp/abortar-exec'):
        conn.commit()
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
