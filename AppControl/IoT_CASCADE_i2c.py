#!/usr/bin/env python
# -*- coding: utf-8 -*-
######################################################
## {IoTControl - AppControl - IoTAVA}               ##
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
from AppControl import PI_BRUNO
import sys
from ctypes import *
import pyctree
from array import *
import smbus

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
VT_Speed = 0.0
VP_Position = 0.0
Control_Output = 0.0
dControl = 0.0
pulsosvelcalc = 10.0
tempoStorage = 0.1
tempociclo = 0.1
velc = 0.0
velocfilter = 0.0
pwm = 0.0
pid = 0.0
KGain = 0.0
fatorKp = 1.0
fatorTi = 1.0
fatorK_pos = 1.0
motorzm = 0.0
pwmzm = 0.0
grafpts = 0
fatora = -5.0#0.0004
fatorb = 2.0#-0.1254
fatorc = 2.0#32.343

# I2C channel 1 is connected to the GPIO pins
channel = 1
# Signal conditioner settings
ADC_offset_A0 = -5.0
ADC_gain_A0 = 2.0
ADC_offset_A1 = -5.0
ADC_gain_A1 = 2.0
#  PCF8591 defaults to address 0x48
PCF8591 = False
address_PCF8591 = 0x48
PCF8591_VCC = 3.3
#  MCP4725 defaults to address 0x60
MCP4725 = True
address_MCP4725 = 0x60
MCP4725_VCC = 5.0
MCP4725_offset = 2048.0
MCP4725_gain = 2048.0/MCP4725_VCC
# Register addresses (with "normal mode" power-down bits)
reg_write_dac = 0x40
#  ADS1115 defaults to address 0x48
ADS1115 = True
address_ADS1115 = 0x48
#  ADS1115 settings
configA0_ADS1115 = 0xc1#0xc1 A0 AP / GND AN 0x85 default A0 AP / A1 AN
configA1_ADS1115 = 0xd1#0x85 default A0 AP / A1 AN
config_ADS1115 = 0x83#default
VPS = 6.144 / 32768.0 #volts per step
# Initialize I2C (SMBus)
bus = smbus.SMBus(channel)

#set variable of previous time as the initial time
Tant = time.time()
tempoLoop = tempoGrava = tempoProcesso = masterClock = Tant

def ReadPCF8591(config1,config2):
  try:
    bus.write_byte(config1,config2)
  except IOError, err:
    return self.errMsg()
  try:
    bus.read_byte(config1) # dummy read to start conversion
  except IOError, err:
    return self.errMsg()

  value = bus.read_byte(config1)
  calc_value = float(value)* PCF8591_VCC / 255.0
  return calc_value

def ReadADS1115(config1,config2):
  #set the configuration register
  datADS = [config1, config2]
  try:
    bus.write_i2c_block_data(address_ADS1115, 0x01, datADS)
  except IOError, err:
    return self.errMsg()
  #Write to Address Pointer register
  data = [0x0,0x0]
  try:
    bus.write_i2c_block_data(address_ADS1115, 0x00, data)
  except IOError, err:
    return self.errMsg()

  #Read Config Register
  data = ReadConfigADS1115()
  i = 1
  while i < 600:          
    if i == 599:
      print(data)
      break          
    i += 1   # wait for conversion complete
    # checking bit 15
    if (data >= 0x80):
      break#Pass
    else:
      #Read Config Register
      data = ReadConfigADS1115()

  # Read data back from 0x00(00), 2 bytes
  # raw_adc MSB, raw_adc LSB
  #delay = 1.0/128+0.0001
  #time.sleep(delay)
  try:
    data = bus.read_i2c_block_data(address_ADS1115, 0x00, 2)
  except IOError, err:
    return self.errMsg()
  # Convert the data
  raw_adc = data[0] * 256 + data[1]
  if raw_adc > 32767:#maximum value 15bits
    raw_adc = 32767
  Ax_adc = float(raw_adc)*VPS
  return Ax_adc

def ReadConfigADS1115():
  #Read Config Register
  try:
    data = bus.read_i2c_block_data(address_ADS1115, 0x01, 2)
  except IOError, err:
    return self.errMsg()
  # Convert the data
  val_adc = data[0] * 256 + data[1]
  return val_adc

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
  print ('PID: %s' %pid)
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
    if PCF8591:
      # 8-bit number relative voltage
      voltage = int(127+float((127.0*w1)/100.0))
      if voltage > 255:
        voltage = 255
      # 8 bits
      msg = (voltage & 0xff)
      try:
        bus.write_byte_data(address_PCF8591, reg_write_dac, msg)
      except IOError, err:
        return self.errMsg()
    if MCP4725:
      # 12-bit number relative voltage
      voltage = int(2048+float((2048.0*w1)/100.0))
      if voltage > 4095:
        voltage = 4095
      # Shift everything left by 4 bits and separate bytes
      msg = (voltage & 0xff0) >> 4
      msg = [msg, (msg & 0xf) << 4]
      try:
        bus.write_i2c_block_data(address_MCP4725, reg_write_dac, msg)
      except IOError, err:
        return self.errMsg()
  else:
    GPIO.output(to_L293D_IN1, 0)
    GPIO.output(to_L293D_IN2, 1)    
    p.ChangeDutyCycle(w1)
    if PCF8591:
      # 8-bit number relative voltage
      voltage = int(127-float((127.0*w1)/100.0))
      if voltage < 0:
        voltage = 0
      # 8 bits
      msg = (voltage & 0xff)
      try:
        bus.write_byte_data(address_PCF8591, reg_write_dac, msg)
      except IOError, err:
        return self.errMsg()
    if MCP4725:    # 12-bit number relative voltage
      voltage = int(2048-float((2048.0*w1)/100.0)) & 0xfff
      # Shift everything left by 4 bits and separate bytes
      msg = (voltage & 0xff0) >> 4
      msg = [msg, (msg & 0xf) << 4]
      try:
        bus.write_i2c_block_data(address_MCP4725, reg_write_dac, msg)
      except IOError, err:
        return self.errMsg()

def main(): 
  global feedBack
  global dControl
  global controle
  global pid
  global fatorKp
  global fatorTi
  global fatorK_pos
  global pulsosvelcalc
  global tempoStorage
  global tempociclo
  global velocfilter
  global KGain
  global VT_Speed
  global VP_Position
  global Control_Output
  global pwm
  global motorzm
  global pwmzm
  global fatora
  global fatorb
  global fatorc
  global ADC_offset_A0
  global ADC_offset_A1
  global ADC_gain_A0
  global ADC_gain_A1
  global MCP4725_offset
  global MCP4725_gain
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
  pid = 0.0
  Control_Output = 0.0
  
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
  #set control signal output frequency
  p.ChangeFrequency(pwmfreq)

  #controller class init
  controle=PI_BRUNO(fatorKp,fatorTi,fatorK_pos)

  #write to file system temp - IoTControl process started.
  iniciado.close()

  #Print to console:
  if debug2console:
    printIntro()

  #Write to Lo_thresh register
  #data = [0x80,0x0]
  #bus.write_i2c_block_data(address_ADS1115, 0x02, data)
  #Write to Hi_thresh register
  #data = [0x7f,0xff]
  #bus.write_i2c_block_data(address_ADS1115, 0x03, data)
  
  #control loop
  try:
    while True:
      #master clock
      masterClock = time.time()

      if (tempociclo <= (masterClock - tempoLoop)):
        tempoLoop = masterClock

        #Print to console:
        if debug2console:
          printSpeed()

        #i2c Readings/Operations
        if PCF8591:
          # A1
          A0_adc = ReadPCF8591(address_PCF8591, 0x41)#A1 PCF8591
          VT_Speed = ADC_offset_A0 + (ADC_gain_A0*A0_adc)#Signal conditioner
          if debug2console:
            # Output data to screen
            print "Digital Value of PCF8591 Analog Input 0 : %s" %A0_adc
          # A2
          A1_adc = ReadPCF8591(address_PCF8591, 0x42)#A2 PCF8591
          VP_Position = ADC_offset_A1 + (ADC_gain_A1*A1_adc)#Signal conditioner
          if debug2console:
            # Output data to screen
            print "Digital Value of PCF8591 Analog Input 1 : %s" %A1_adc

        if ADS1115:
          #ADS1115 A0
          A0_adc = ReadADS1115(configA0_ADS1115, config_ADS1115)
          VT_Speed = ADC_offset_A0 + (ADC_gain_A0*A0_adc)#Signal conditioner
          if debug2console:
            # Output data to screen
            print "Digital Value of Analog Input 0 : %s" %A0_adc
          #Read Config Register
          raw_adc = ReadConfigADS1115()
          if debug2console:
            # Output data to screen
            print "ADS1115 Config Register : %d" %raw_adc
          #ADS1115 A1
          A1_adc = ReadADS1115(configA1_ADS1115,config_ADS1115)
          VP_Position = ADC_offset_A1 + (ADC_gain_A1*A1_adc)#Signal conditioner
          if debug2console:
            # Output data to screen
            print "Digital Value of Analog Input 1 : %s" %A1_adc
          #Read Config Register
          raw_adc = ReadConfigADS1115()
          if debug2console:
            # Output data to screen
            print "ADS1115 Config Register : %d" %raw_adc

        #control
        dSetpoint = dControl*0.01#pos
        dSetpointVel = fatorK_pos * (dSetpoint - VP_Position)#vel
        controle.setPoint(dSetpointVel)#vel
        #control inputs Feedback, Ts
        Control_Output = controle.update(VT_Speed, tempociclo)
        if PCF8591: 
          AO1 = Control_Output*PCF8591_VCC/100.0
        else:
          AO1 = Control_Output

        if MCP4725:
          # 12-bit number relative voltage
          f_voltage = MCP4725_offset +(MCP4725_gain*Control_Output)
          if f_voltage < 0.0:
            f_voltage = 0.0
          elif f_voltage > 4095.0:
            f_voltage = 4095.0
          voltage = int(f_voltage)
          # Shift everything left by 4 bits and separate bytes
          msg = (voltage & 0xff0) >> 4
          msg = [msg, (msg & 0xf) << 4]
          try:
            bus.write_i2c_block_data(address_MCP4725, reg_write_dac, msg)
          except IOError, err:
            return self.errMsg()

        else:
          #positive output
          if pwm >= 0.0:
            saidaPWM(pwm, 1)
          #negative output
          else:
            saidaPWM((pwm*-1), 0)
        
        #Calculate the instantaneous Gain
        if not (ADS1115 or PCF8591):
          if pwm != 0.0:
            KGain = feedBack/pwm
          else:
            KGain = 0.0

        #Update data Storage
        if (tempoStorage <= (masterClock - tempoGrava)):
          tempoGrava = masterClock
          csr.execute("SELECT ajuste, valorK, valorKi, valorKd, fatora, fatorb, fatorc FROM controle")
          resstr = csr.fetchone()
          resret = float(resstr[0])
          #ajusta dControl
          dControl = resret

          # lendo valores do banco
          fatorKp = resstr[1]
          fatorTi = resstr[2]
          fatorK_pos = resstr[3]
          fatora = resstr[4]
          fatorb = resstr[5]
          fatorc = resstr[6]
          
          #Sinal conditioner
          ADC_offset_A0 = ADC_offset_A1 = fatora
          MCP4725_offset = fatorb
          MCP4725_gain = fatorc

          # input Param
          controle.setParam(fatorKp,fatorTi,fatorK_pos)
          # input Windup saturation, deadbands and Filter N 3_20.
          controle.setWindup(5.0, motorzm, pwmzm, 3.0)

          #INSERT
          xstamp = datetime.fromtimestamp(masterClock) #(2016,9,27,16,00,00)
          commitCheck += 1
          if MCP4725 or PCF8591:
            csr.execute("INSERT INTO dados (ajuste, velocidade, erro, Kp, Ki, Kd, Kmotor, tempo) VALUES(?,?,?,?,?,?,?,?)",(dSetpoint, VT_Speed, AO1,fatorKp,fatorTi,fatorK_pos, VP_Position,xstamp))
          else:
            csr.execute("INSERT INTO dados (ajuste, velocidade, erro, Kp, Ki, Kd, Kmotor, tempo) VALUES(?,?,?,?,?,?,?,?)",(dControl, feedBack, pwm,fatorKp,fatorTi,fatorK_pos, KGain,xstamp))
  
      #commit
      if (commitCheck >= 100): 
        conn.commit()
        commitCheck = 0
      #measure the time in the loop
      tempoprocesso = time.time() - masterClock
      #if time in the loop is low then sleep
      #if tempociclo > tempoprocesso:
      #  time.sleep(tempociclo - tempoprocesso)

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


