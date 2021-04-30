######################################################
## {IoTControl - AppControl - Control class}        ##
######################################################
## { DanMartins/IoTControl is licensed under the    ##
##   GNU General Public License v3.0}               ##
######################################################
## Author: {DanMartins}                             ##
## Copyright: Copyright {2021}, {IoTControl}        ##
## Credits: [{https://domartins.wixsite.com/data}]  ##
## License: {GNU General Public License v3.0}       ##
## Version: {2021}.{04}.{22}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
######################################################

class CAMPO:
	"""
	PI Discretizado
	"""

	def __init__(self, P=2.0, I=1.0):

		self.Kp=P
		self.Ki=I

		self.SetPoint=0.0
		self.error=0.0
		self.calc=0.0
		self.calc_ant=0.0
	        #filter coefficients
	        self.N = 20.0         
	        # Windup 
	        self.windup = 100.0
	        self.deadband_in = 0.0
	        self.deadband_out = 0.0

	def update(self,feedback_value, sample_time):
		"""
		Calcula
		"""
		if (self.Ki <= 0.0):
		  return 0.0

                T_ciclo =  sample_time

                erro_anterior = self.error
		self.error = self.SetPoint - feedback_value                
                parte_1 = 0.0
		parte_1 = self.Kp * ((float(T_ciclo)/(2*self.Ki)) + 1)* self.error
                parte_2 = 0.0
		parte_2 = self.Kp * ((float(T_ciclo)/(2*self.Ki)) - 1)* erro_anterior
		
		parte_3 = self.calc_ant

		self.calc = parte_1 + parte_2 + parte_3
		
		#Control positive saturation
		if (self.calc > self.windup):
		  self.calc = self.windup
		#Control negative saturation
		elif (self.calc < -self.windup):
		  self.calc = -self.windup

		#Control brake saturation stop positive inversion protection:
		if ((self.calc < 0.0) & (feedback_value > 0.0)):
		  self.calc = 0.0
		#Control brake saturation stop negative inversion protection:
		elif ((self.calc > 0.0) & (feedback_value < 0.0)):
		  self.calc = 0.0

		#Control dead zone means the actuator did not respond to the output value (too low)
		if ((self.SetPoint < self.deadband_in)&(self.SetPoint > -self.deadband_in)):
		  if((self.calc < self.deadband_out)&(self.calc > -self.deadband_out)):
		    self.calc = 0.0
		  elif (self.error == 0.0):
		    self.calc = 0.0

		self.calc_ant = self.calc
		return self.calc

	def setWindup(self, windup, deadband_in, deadband_out, NFilter):
		"""windup
		"""
		self.windup = windup
		self.deadband_in = deadband_in
		self.deadband_out = deadband_out
		self.N = NFilter

	def setPoint(self,set_point):
		"""
		Initilize the setpoint of PI
		"""
		self.SetPoint = set_point
		self.Integrator=0

	def setIntegrator(self, Integrator):
		self.Integrator = Integrator

	def setKp(self,P):
		self.Kp=P

	def setKi(self,I):
		self.Ki=I

	def getPoint(self):
		return self.SetPoint

	def getError(self):
		return self.error

	def getIntegrator(self):
		return self.Integrator

class PI:
	"""
	PI Discretizado
	"""

	def __init__(self, P=2.0, I=1.0):

		self.Kp=P
		self.Ki=I

		self.SetPoint=0.0
		self.error=0.0
		self.calc=0.0
		self.calc_ant=0.0
	        #filter coefficients
	        self.N = 20.0         
	        # Windup
	        self.windup = 100.0
	        self.deadband_in = 0.0
	        self.deadband_out = 0.0

	def update(self,feedback_value, sample_time):
		"""
		Calcula
		"""
		if (self.Ki <= 0.0):
		  return 0.0

                T_ciclo =  sample_time
                
                erro_anterior = self.error
		self.error = self.SetPoint - feedback_value                
                parte_1 = 0.0
		parte_1 = self.Kp * ((float(T_ciclo)/(2*self.Ki)) + 1)* self.error
                parte_2 = 0.0
		parte_2 = self.Kp * ((float(T_ciclo)/(2*self.Ki)) - 1)* erro_anterior
		
		parte_3 = self.calc_ant

		self.calc = parte_1 + parte_2 + parte_3
		
		#PI positive saturation
		if (self.calc > self.windup):
		  self.calc = self.windup
		#PI negative saturation
		elif (self.calc < -self.windup):
		  self.calc = -self.windup

		#PI brake saturation stop positive inversion protection:
		if ((self.calc < 0.0) & (feedback_value > 0.0)):
		  self.calc = 0.0
		#PI brake saturation stop negative inversion protection:
		elif ((self.calc > 0.0) & (feedback_value < 0.0)):
		  self.calc = 0.0

		#PI dead zone means the actuator did not respond to the output value (too low)
		if ((self.SetPoint < self.deadband_in)&(self.SetPoint > -self.deadband_in)):
		  if((self.calc < self.deadband_out)&(self.calc > -self.deadband_out)):
		    self.calc = 0.0
		  elif (self.error == 0.0):
		    self.calc = 0.0

		self.calc_ant = self.calc
		return self.calc

	def setWindup(self, windup, deadband_in, deadband_out, NFilter):
		"""windup
		"""
		self.windup = windup
		self.deadband_in = deadband_in
		self.deadband_out = deadband_out
		self.N = NFilter

	def setPoint(self,set_point):
		"""
		Initilize the setpoint of PI
		"""
		self.SetPoint = set_point
		self.Integrator=0

	def setIntegrator(self, Integrator):
		self.Integrator = Integrator

	def setKp(self,P):
		self.Kp=P

	def setKi(self,I):
		self.Ki=I

	def getPoint(self):
		return self.SetPoint

	def getError(self):
		return self.error

	def getIntegrator(self):
		return self.Integrator

class PID:
    """PID Controller
    """
    def __init__(self, P=0.2, I=0.0001, D=0.0001):
        self.Kp = P
        self.Ki = I
        if (I != 0):
          self.Ti = P/I
	else:
	  self.Ti = 0.0
        self.Kd = D
	if (P != 0):
          self.Td = D/P
        else:
          self.Td = 0.0
        self.Ts = 0.1
        self.error = [0.0, 0.0]
        self.u_I = [0.0, 0.0]
        self.u_D = [0.0, 0.0]
        self.calc = [0.0, 0.0]
        self.feedback = [0.0, 0.0, 0.0]
        #filter coefficients
        self.N = 20.0         
        # Windup
        self.windup = 100.0
        self.deadband_in = 0.0
        self.deadband_out = 0.0
        self.clear()

    def clear(self):
        """Clears PID"""
        self.SetPoint = 0.0

    def update(self, feedback_value, sample_time):
        """Calculates PID value for given reference feedback
        """
        self.error[1] = self.error[0]
        self.error[0] = self.SetPoint - feedback_value
        #store previous value
        self.feedback[2] = self.feedback[1]
        self.feedback[1] = self.feedback[0]
        self.feedback[0] = feedback_value
        self.Ts = sample_time

        #eq. dif - met Tustin PI
        self.u_I[1] = self.u_I[0]
        if (self.Ti != 0.0):
          self.u_I[0] = ((self.Kp*self.Ts)/2*self.Ti) * (self.error[0]+self.error[1])
        else:
          self.u_I[0] = 0.0
          
        #eq. dif - met Backward Euler PD with filter N
        self.u_D[1] = self.u_D[0]
        if (self.Td != 0.0):
          ud1 = self.u_D[1]*self.Td/(self.Td+self.N*self.Ts)
          ud2 = self.Kp*self.N*self.Td/(self.Td+self.N*self.Ts)
          self.u_D[0] = ud1 - (ud2* (self.feedback[0] - 2*self.feedback[1] + self.feedback[2]))
        else:
          self.u_D[0] = 0.0
        
        #Processing - PID de velocidade
        self.calc[1] = self.calc[0]
        self.calc[0] = self.calc[1] + self.Kp*(self.error[0] - self.error[1]) + (self.u_I[0]) + (self.u_D[0])

        #PID positive saturation
        if (self.calc[0] > self.windup):
          self.calc[0] = self.windup
        #PID negative saturation
        elif (self.calc[0] < -self.windup):
          self.calc[0] = -self.windup

        #PID brake saturation stop positive inversion protection:
        if ((self.calc[0] < 0.0) & (feedback_value > 0.0)):
          self.calc[0] = 0.0
        #PID brake saturation stop negative inversion protection:
        elif ((self.calc[0] > 0.0) & (feedback_value < 0.0)):
          self.calc[0] = 0.0

        #PID dead zone means the actuator did not respond to the output value (too low)
        if ((self.SetPoint < self.deadband_in)&(self.SetPoint > -self.deadband_in)):
          if((self.calc[0] < self.deadband_out)&(self.calc[0] > -self.deadband_out)):
            self.calc[0] = 0.0
          elif (self.error[0] == 0.0):
            self.calc[0] = 0.0
        
        return self.calc[0]

    def setWindup(self, windup, deadband_in, deadband_out, NFilter):
        """windup
        """
        self.windup = windup
        self.deadband_in = deadband_in
        self.deadband_out = deadband_out
        self.N = NFilter

    def setPoint(self,set_point):
        """setpoint of PID
        """
        self.SetPoint = set_point

    def setKpKiKd(self,P, I, D):
        self.Kp=P
        self.Ki=I
        self.Kd=D
        if (I != 0):
          self.Ti = P/I
	else:
	  self.Ti = 0.0
	if (P != 0):
          self.Td = D/P
        else:
          self.Td = 0.0

class FUZZY:
    """Fuzzy Controller
    """
    def __init__(self):
        self.error = [0.0, 0.0]
        self.calc = [0.0, 0.0]
        self.feedback = [0.0, 0.0, 0.0]
        self.fuz = [-2.0, -1.0, -0.5, 0.0, 0.5, 1.0, 2.0]
        self.defuz = [2.0, 1.0, 0.5, 0.0, -0.5, -1.0, -2.0]
        #filter coefficients
        self.N = 20.0         
        # Windup
        self.windup = 100.0
        self.deadband_in = 0.0
        self.deadband_out = 0.0
        self.clear()

    def clear(self):
        """Clears SetPoint"""
        self.SetPoint = 0.0

    def update(self, feedback_value, sample_time):
        """Calculates Fuzzy value for given reference feedback
        """
        self.error[1] = self.error[0]
        self.error[0] = self.SetPoint - feedback_value
        #store previous value
        self.feedback[2] = self.feedback[1]
        self.feedback[1] = self.feedback[0]
        self.feedback[0] = feedback_value

	valorsaida = 0.0
	pertinencia = 0.0
	erro = self.error[0]

	#Fuzzification parameters
	fuzN3 = self.fuz[0]
	fuzN2 = self.fuz[1]
	fuzN1 = self.fuz[2]
	fuzZ = self.fuz[3]
	fuzP1 = self.fuz[4]
	fuzP2 = self.fuz[5]
	fuzP3 = self.fuz[6]
	#Defuzzification parameters
	defuzN3 = self.defuz[0]
	defuzN2 = self.defuz[1]
	defuzN1 = self.defuz[2]
	defuzZ = self.defuz[3]
	defuzP1 = self.defuz[4]
	defuzP2 = self.defuz[5]
	defuzP3 = self.defuz[6]
	
	#IF-THEN Rules on Fuzzification / Defuzzification Membership
	if erro < fuzN3:
	  pertinencia = 1.0 
	  valorsaida = defuzN3
	elif (erro >= fuzN3) & (erro < fuzN2):
	  pertinencia = (fuzN3 + fuzN2)
	  if pertinencia != 0.0:
	    pertinencia = erro/pertinencia 
	  valorsaida = (pertinencia*defuzN3) + (pertinencia*defuzN2)
	elif (erro >= fuzN2) & (erro < fuzN1):
	  pertinencia = (fuzN2 + fuzN1)
	  if pertinencia != 0.0:
	    pertinencia = erro/pertinencia
	  valorsaida = (pertinencia*defuzN2) + (pertinencia*defuzN1)
	elif (erro >= fuzN1) & (erro < fuzZ):
	  pertinencia = (fuzN1 + fuzZ)
	  if pertinencia != 0.0:
	    pertinencia = erro/pertinencia
	  valorsaida = (pertinencia*defuzN1) + (pertinencia*defuzZ)
	elif erro == fuzN1:
	  pertinencia = 1.0
	  valorsaida = defuzZ
	elif (erro > fuzZ) & (erro <= fuzP1):
	  pertinencia = (fuzZ + fuzP1)
	  if pertinencia != 0.0:
	    pertinencia = erro/pertinencia
	  valorsaida = (pertinencia*defuzZ) + (pertinencia*defuzP1)
	elif (erro >= fuzP1) & (erro <= fuzP2):
	  pertinencia = (fuzP1 + fuzP2) 
	  if pertinencia != 0.0:
	    pertinencia = erro/pertinencia
	  valorsaida = (pertinencia*defuzP1) + (pertinencia*defuzP2)
	elif (erro >= fuzP2) & (erro <= fuzP3):
	  pertinencia = (fuzP2 + fuzP3)
	  if pertinencia != 0.0:
	    pertinencia = erro/pertinencia
	  valorsaida = (pertinencia*defuzP2) + (pertinencia*defuzP3)
	elif erro > fuzP3:
	  pertinencia = 1.0 
	  valorsaida = defuzP3

	#Proportional Integrative Fuzzy
        self.calc[1] = self.calc[0]
        self.calc[0] = self.calc[1] + (abs(valorsaida)*(self.error[0] - self.error[1]))
        self.calc[0] = self.calc[0] + (((abs(valorsaida)*sample_time)/2) * (self.error[0]+self.error[1]))
	
        #Fuzzy positive saturation
        if (self.calc[0] > self.windup):
          self.calc[0] = self.windup
        #Fuzzy negative saturation
        elif (self.calc[0] < -self.windup):
          self.calc[0] = -self.windup

        #Fuzzy brake saturation stop positive inversion protection:
        if ((self.calc[0] < 0.0) & (feedback_value > 0.0)):
          self.calc[0] = 0.0
        #Fuzzy brake saturation stop negative inversion protection:
        elif ((self.calc[0] > 0.0) & (feedback_value < 0.0)):
          self.calc[0] = 0.0

        #Fuzzy dead zone means the actuator did not respond to the output value (too low)
        if ((self.SetPoint < self.deadband_in)&(self.SetPoint > -self.deadband_in)):
          if((self.calc[0] < self.deadband_out)&(self.calc[0] > -self.deadband_out)):
            self.calc[0] = 0.0
          elif (self.error[0] == 0.0):
            self.calc[0] = 0.0
       
        return self.calc[0]

    def setWindup(self, windup, deadband_in, deadband_out, NFilter):
        """windup
        """
        self.windup = windup
        self.deadband_in = deadband_in
        self.deadband_out = deadband_out
        self.N = NFilter

    def setPoint(self,set_point):
        """setpoint of Fuzzy
        """
        self.SetPoint = set_point

    def setFuzzy(self,Fuz):
        self.fuz = Fuz

    def setDefuzzy(self,Defuz):
        self.defuz = Defuz
