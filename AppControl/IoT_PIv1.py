################################################################
#% AppControl - IoTControl
#%
#%       Application - Interface.
#%       DanMartins
#%       Advisor - Prof. Dr. Alexandre Brincalepe Campo
#%       Implemented by Danilo Martins
#%       IoTControl reasearch project 
#%       São Paulo, 2017. 
#%
#%################################################################
#!/usr/bin/env python
# -*- coding: utf-8 -*-
import RPi.GPIO as GPIO
import time
import threading
import os
from PI import PI
import sys
from ctypes import *
import pyctree
from datetime import datetime
from array import *

if os.path.isfile('/tmp/inic-exec'):
  print('Dupla inicia, encerrando programa.')
  sys.exit(0)  

enable_pin = 23
to_L293D_IN1 = 25
to_L293D_IN2 = 24
encoderA = 16
encoderB = 20

counterA = 0.0
counterB = 0.0
contadorTempo = 0.0
velocidade = 0.0
velocidadeinst = 0.0
desejadavelocidade = 0.0

pulsosvelcalc = 10.0
tempovelcalc = 0.1
tempociclo = 0.1
velc = []#array('f', [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0])
interaveloc = 0
dir = 0

GPIO.setmode(GPIO.BCM)   
GPIO.setup(enable_pin, GPIO.OUT)
GPIO.setup(to_L293D_IN1, GPIO.OUT)
GPIO.setup(to_L293D_IN2, GPIO.OUT)
GPIO.setup(encoderA, GPIO.IN,  pull_up_down=GPIO.PUD_DOWN)
GPIO.setup(encoderB, GPIO.IN,  pull_up_down=GPIO.PUD_DOWN)

p = GPIO.PWM(enable_pin, 250)

Tant = time.time()

pwm = 0.0
pi = 0.0
KVelocidade = 7.2
fatorK = 1.0
fatorKi = 0.0
fatorKd = 0.0
motorzm = 0.0
pwmzm = 0.0

fatora = 0.0004
fatorb = - 0.1254
fatorc = 32.343

def calcVelocidade():
  global Tant
  global counterA
  global counterB
  global velocidade
  global velocidadeinst
  global pulsosvelcalc
  global tempovelcalc
  global velc
  global interaveloc
  global dir
  
  if interaveloc == 0:
    return
  
  Tnow=time.time()

  interval = Tnow-Tant

  if ((interval >= tempovelcalc) | ((counterA >= pulsosvelcalc) & (counterB >= pulsosvelcalc))):
    if counterA > 0.0:
      for i in range (interaveloc-1,0,-1):
        velc[i] = velc[i-1]      
      if counterA < counterB:
        velc[0] = counterA/(interval)
      else:
        velc[0] = counterB/(interval)
      if dir >= 10:
         velc[0] = velc[0] * (-1)
         
      velocidadeinst = velc[0]
      velocsum = 0.0
      for i in range (0,interaveloc):
        velocsum = velocsum + velc[i]
      velocidade = velocsum / interaveloc  
    else:
      for i in range (interaveloc-1,0,-1):
        velc[i] = velc[i-1]      
      velc[0] = 0.0
      velocidade = velc[0]
      
    Tant = Tnow
    counterA = 0.0
    counterB = 0.0
    #execControle()


def printSpeed():
  print (' ')
  print ('Velocidade p/s: %s' %velocidade)
  print ('desejadavelocidade p/s: %s' %desejadavelocidade)
  
def saidaPWM(w1, w2):
  if w2:
    GPIO.output(to_L293D_IN1, 1)
    GPIO.output(to_L293D_IN2, 0)
    p.ChangeDutyCycle(w1)
  else:
    GPIO.output(to_L293D_IN1, 0)
    GPIO.output(to_L293D_IN2, 1)    
    p.ChangeDutyCycle(w1)

def velPpwm(valpid):
  global desejadavelocidade
  global motorzm
  global pwmzm
  global KVelocidade
  global fatora
  global fatorb
  global fatorc
  
  if valpid < 0.0:
    valvel = valpid * (-1.0)
  else:
    valvel = valpid
    
  if valpid >= motorzm:
    calcpwm = fatora*(valvel*valvel) + fatorb*(valvel) + fatorc
  else:
    calcpwm = valpid / KVelocidade

  dvelocidade = desejadavelocidade
  
  if dvelocidade < 0.0:
    dvelocidade = dvelocidade * (-1.0)
    if calcpwm < 0.0:
      calcpwm = calcpwm * (-1.0)
  
  if dvelocidade <> 0.0:
    if calcpwm < pwmzm:
      calcpwm = pwmzm
  if calcpwm < 0.0:
    calcpwm = 0.0
     
  if calcpwm > 100.0:
    calcpwm = 100.0

  return calcpwm

def execControle():
  global velocidade
  global velocidadeinst
  global desejadavelocidade
  global controle
  global pi
  global pwm
  global KVelocidade
  
  controle.setPoint(desejadavelocidade)
  pi = controle.update(velocidade)
  pi = velocidade + pi
  pwm = velPpwm(pi)
  
  if desejadavelocidade >= 0.0:
    saidaPWM(pwm, 1)
  else:
    saidaPWM(pwm, 0)


def cb_EncoderA(channel):
  global  counterA
  global pulsosvelcalc
  global dir
  counterA += 1.0
  if GPIO.input(encoderB):
    if dir > 0:
      dir -= 1
  #if counterA >= pulsosvelcalc:
  #  calcVelocidade()


def cb_EncoderB(channel):
  global  counterB
  global  pulsosvelcalc
  global  dir
  counterB += 1.0
  if GPIO.input(encoderA):
    if dir < 20:
      dir += 1  
  #if counterB >= pulsosvelcalc:
  #  calcVelocidade()


def main(): 
  global velocidade
  global desejadavelocidade
  global controle
  global pi
  global fatorK
  global fatorKi
  global fatorKd
  global pulsosvelcalc
  global tempovelcalc
  global tempociclo
  global interaveloc
  global KVelocidade
  global pwm
  global motorzm
  global pwmzm
  global fatora
  global fatorb
  global fatorc


  iniciado = open("/tmp/inic-exec", 'w+')
  
  GPIO.output(to_L293D_IN1, 1)
  GPIO.output(to_L293D_IN2, 0)

  p.start(0)
  p.ChangeDutyCycle(0)
 

  controle=PI(fatorK,fatorKi)

  
  conn = pyctree.connect(user="admin", password="ADMIN", database="ctreeSQL", host="localhost",port="6597")

  csr = conn.cursor()

  resret = 0.0

  xstamp = datetime.now() #(2016,9,27,16,00,00)

  pwm = 0.0

  pulsosvelcalc = 10.0
  tempovelcalc = 0.1
  tempociclo = 0.1

  csr.execute("SELECT pulsosvelcalc, tempovelcalc, tempociclo, grafpts, intvelfil, pwmfreq, motorzm, pwmzm FROM configura")
  resstr = csr.fetchone()
  pulsosvelcalc = resstr[0]
  tempovelcalc = resstr[1]
  tempociclo = resstr[2]
  grafpts = resstr[3]
  interaveloc = resstr[4]
  pwmfreq = resstr[5]
  motorzm = resstr[6]
  pwmzm = resstr[7]

  p.ChangeFrequency(pwmfreq)

  for i in range(interaveloc):
    velc.append(0.0)


  print "Obrigado por experimentar IoTControl"
  print "Configuracoes aplicadas:"
  print ('pulsosvelcalc: %s' %pulsosvelcalc)
  print ('tempovelcalc: %s' %tempovelcalc)
  print ('tempociclo: %s' %tempociclo)
  print ('grafpts: %s' %grafpts)
  
  GPIO.add_event_detect(encoderA, GPIO.RISING, callback=cb_EncoderA)
  GPIO.add_event_detect(encoderB, GPIO.RISING, callback=cb_EncoderB)
  
  iniciado.close()
  
  while True:
    tempoprocesso = time.time()
    csr.execute("SELECT ajuste, valorK, valorKi, valorKd, fatora, fatorb, fatorc FROM controle")
    resstr = csr.fetchone()
    resret = float(resstr[0])
    #ajusta velocidade desejada
    desejadavelocidade = resret

    # lendo valores do banco
    fatorK = resstr[1]
    fatorKi = resstr[2]
    fatorKd = resstr[3]
    fatora = resstr[4]
    fatorb = resstr[5]
    fatorc = resstr[6]

    controle.setKp(fatorK)
    controle.setKi(fatorKi)

    #if desejadavelocidade != controle.getPoint:

    calcVelocidade()
    execControle()

    xstamp = datetime.now() #(2016,9,27,16,00,00)
    #print desejadavelocidade, velocidade,pwm
    csr.execute("INSERT INTO dados (ajuste, velocidade, erro, Kp, Ki, Kmotor, tempo) VALUES(?,?,?,?,?,?,?)",(desejadavelocidade, velocidade,pwm,fatorK,fatorKi, KVelocidade,xstamp))
    conn.commit()
    tempoprocesso = time.time() - tempoprocesso
    if tempociclo > tempoprocesso:
       tempoprocesso = tempociclo - tempoprocesso
       time.sleep(tempoprocesso)
       
    if os.path.isfile('/tmp/abortar-exec'):
     print('Abortar')
     os.remove('/tmp/abortar-exec')
     os.remove('/tmp/inic-exec')
     break
    

  GPIO.cleanup()
  csr.close()
  conn.close()
  print ('Obrigado por usar IoTControl!')

if __name__ == '__main__':
    main()


