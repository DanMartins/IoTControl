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
## Version: {2022}.{01}.{07}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
######################################################
import math

class C_2DOF_LQR:
    """2DOF Controller
    """
    def __init__(self, K0_0=0.1,K0_1=0.1,K0_2=0.1,K0_3=0.1,K0_4=0.1,K0_5=0.1, K1_0=0.1,K1_1=0.1,K1_2=0.1,K1_3=0.1,K1_4=0.1,K1_5=0.1):
        self.K0_0 = K0_0
        self.K0_1 = K0_1
        self.K0_2 = K0_2
        self.K0_3 = K0_3
        self.K0_4 = K0_4
        self.K0_5 = K0_5
        self.K1_0 = K1_0
        self.K1_1 = K1_1
        self.K1_2 = K1_2
        self.K1_3 = K1_3
        self.K1_4 = K1_4
        self.K1_5 = K1_5
        self.Ts = 0.1

        self.derivadaYAW = 0.0 #derivadas dos angulos
        self.derivadaPitch = 0.0 #derivadas dos angulos
        self.anguloRealYAW_1 = 0.0
        self.anguloRealYAW_2 = 0.0
        self.anguloRealYAW_3 = 0.0
        self.anguloRealYAW_4 = 0.0
        self.anguloRealPitch_1 = 0.0
        self.anguloRealPitch_2 = 0.0
        self.anguloRealPitch_3 = 0.0
        self.anguloRealPitch_4 = 0.0
        self.anguloRealYAW = 0.0
        self.anguloRealPitch = 0.0
        self.up = 0.0
        self.uy = 0.0
        self.vp = 0.0
        self.vy = 0.0

        self.feedback_1 = [0.0, 0.0, 0.0]
        self.feedback_2 = [0.0, 0.0, 0.0]
        #filter coefficients
        self.N = 20.0
        # limit
        self.limit = 100.0
        self.clear()

    def clear(self):
        """Clears PI"""
        self.SetPoint_1 = 0.0
        self.SetPoint_2 = 0.0

    def update(self, feedback_1, feedback_2, sample_time):
        """Calculates PI value for given reference feedback
        """
        #convert to name code
        self.anguloRealYAW = feedback_1
        self.anguloRealPitch = feedback_2

        #self.error1[1] = self.error1[0]
        #self.error1[0] = self.SetPoint_1 - self.anguloRealYAW

        #self.error2[1] = self.error2[0]
        #self.error2[0] = self.SetPoint_2 - self.anguloRealPitch

        #store previous value
        self.feedback_1[2] = self.feedback_1[1]
        self.feedback_1[1] = self.feedback_1[0]
        self.feedback_1[0] = feedback_1

        self.feedback_2[2] = self.feedback_2[1]
        self.feedback_2[1] = self.feedback_2[0]
        self.feedback_2[0] = feedback_2

        self.Ts = sample_time

        #Processing - Control
        self.derivadaYAW = 0.1*self.derivadaYAW + (0.9*(self.anguloRealYAW - self.anguloRealYAW_1)/self.Ts)
        self.derivadaPitch = 0.1*self.derivadaPitch + (0.9*(self.anguloRealPitch - self.anguloRealPitch_1)/self.Ts)

        self.anguloRealPitch_4 = self.anguloRealPitch_3
        self.anguloRealPitch_3 = self.anguloRealPitch_2
        self.anguloRealPitch_2 = self.anguloRealPitch_1
        self.anguloRealPitch_1 = self.anguloRealPitch
        self.anguloRealYAW_4 = self.anguloRealYAW_3
        self.anguloRealYAW_3 = self.anguloRealYAW_2
        self.anguloRealYAW_2 = self.anguloRealYAW_1
        self.anguloRealYAW_1 = self.anguloRealYAW

        self.up = -(self.K0_4*self.vp + self.K0_5*self.vy) - (self.K0_0*self.anguloRealPitch + self.K0_1*self.anguloRealYAW + self.K0_2*self.derivadaPitch + self.K0_3*self.derivadaYAW)
        self.uy = -(self.K1_4*self.vp + self.K1_5*self.vy) - (self.K1_0*self.anguloRealPitch + self.K1_1*self.anguloRealYAW + self.K1_2*self.derivadaPitch + self.K1_3*self.derivadaYAW)

        self.up += 52.0
        self.uy += 13.0

        # Verificacao Pitch
        if ((self.up >= self.limit) or (self.up <= 0.0)):
          self.vp = self.vp
        else:
          self.vp = self.vp + (self.SetPoint_2 - self.anguloRealPitch)#(feedback_2 - self.anguloRealPitch)

        # Verificacao Yaw
        if ((self.uy >= self.limit) or (self.uy <= 0.0)):
          self.vy = self.vy
        else:
          self.vy = self.vy + (self.SetPoint_1  - self.anguloRealYAW)#(feedback_1 - self.anguloRealYAW)

        #saturation
        if (self.up > self.limit):
          self.up = self.limit
        if (self.up < 0.0):
          self.up = 0.0
        if (self.uy > self.limit):
          self.uy = self.limit
        if (self.uy < 0.0):
          self.uy = 0.0

        return (self.uy, self.up)

    def setLimit(self, limit):
        """limit
        """
        self.limit = limit

    def setPoint(self,set_point1, set_point2):
        """setpoint of PID
        """
        self.SetPoint_1 = set_point1
        self.SetPoint_2 = set_point2

    def setParam(self,K0_0,K0_1,K0_2,K0_3,K0_4,K0_5, K1_0,K1_1,K1_2,K1_3,K1_4,K1_5):
        self.K0_0 = K0_0
        self.K0_1 = K0_1
        self.K0_2 = K0_2
        self.K0_3 = K0_3
        self.K0_4 = K0_4
        self.K0_5 = K0_5
        self.K1_0 = K1_0
        self.K1_1 = K1_1
        self.K1_2 = K1_2
        self.K1_3 = K1_3
        self.K1_4 = K1_4
        self.K1_5 = K1_5

class AVANCO:
    """
    [17:25, 27/10/2021] Bruno Angelico USP Avanco de fase:
    [17:26, 27/10/2021] Bruno Angelico USP: Kc = 1.9020  alpha = 0.9303  beta = 0.7421
    [17:27, 27/10/2021] Bruno Angelico USP: Eq. de diferencas:
    [17:29, 27/10/2021] Bruno Angelico USP: u[n] = beta*u[n-1] + Kc*(e[n] - alpha*e[n-1])
    % C(s) = Kc*(s+a)/(s+b)  % b>a, avanco de fase
    % Colocar a formula acima na interface grafica e os campos
    % Kc, a e b como entradas da interface grafica
    % Discretizacao: C_D(z) = Kc_D * (z-alpha)/(z-beta)
    %
    % No controlador, implemente:
    %Ts = 1/50
    %alpha = exp(-a*Ts) % 0.9286
    %beta =  exp(-b*Ts) % 0.7421
    %Kc_D = Kc*(a/b)*(1-beta)/(1-alpha) %1.9583
    %
    % u[n] = beta*u[n-1] + Kc_D*(e[n]-alpha*e[n-1])
    """
    def __init__(self, Kc=1.9020, a=0.9303, b=0.7421):
        self.Kc = Kc
        self.Ts = 0.1
        self.alpha=math.exp(-a*self.Ts)
        self.beta=math.exp(-b*self.Ts)
        self.Kc_D=self.Kc*(a/b)*(1-self.beta)/(1-self.alpha)
        self.error = [0.0, 0.0]
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

        #eq. dif - Avanco de fase
        #u[n] = beta*u[n-1] + Kc_D*(e[n]-alpha*e[n-1])
        self.calc[1] = self.calc[0]
        self.calc[0] = self.beta*self.calc[1] + self.Kc_D*(self.error[0] - self.alpha*self.error[1])

        #positive saturation
        if (self.calc[0] > self.windup):
          self.calc[0] = self.windup
        #PID negative saturation
        elif (self.calc[0] < -self.windup):
          self.calc[0] = -self.windup

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

    def setParam(self,Kc, a, b):
        """
        %Ts = 1/50
        %alpha = exp(-a*Ts) % 0.9286
        %beta =  exp(-b*Ts) % 0.7421
        %Kc_D = Kc*(a/b)*(1-beta)/(1-alpha) %1.9583
        %
        % u[n] = beta*u[n-1] + Kc_D*(e[n]-alpha*e[n-1])
        """
        self.Kc=Kc
        self.alpha=math.exp(-a*self.Ts)
        self.beta=math.exp(-b*self.Ts)
        self.Kc_D=self.Kc*(a/b)*(1-self.beta)/(1-self.alpha)

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
                parte_1 = self.Kp * ((float(T_ciclo)/(2.0*self.Ki)) + 1.0)* self.error
                parte_2 = 0.0
                parte_2 = self.Kp * ((float(T_ciclo)/(2.0*self.Ki)) - 1.0)* erro_anterior

                parte_3 = self.calc_ant

                self.calc = parte_1 + parte_2 + parte_3

                #PI positive saturation
                if (self.calc > self.windup):
                  self.calc = self.windup
                #PI negative saturation
                elif (self.calc < -self.windup):
                  self.calc = -self.windup

                #PI brake saturation stop positive inversion protection:
                if ((self.calc < 0.0) and (feedback_value > 0.0)):
                  self.calc = 0.0
                #PI brake saturation stop negative inversion protection:
                elif ((self.calc > 0.0) and (feedback_value < 0.0)):
                  self.calc = 0.0

                #PI dead zone means the actuator did not respond to the output value (too low)
                if ((self.SetPoint < self.deadband_in)and(self.SetPoint > -self.deadband_in)):
                  if((self.calc < self.deadband_out)and(self.calc > -self.deadband_out)):
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
        if (I != 0.0):
          self.Ti = P/I
        else:
          self.Ti = 0.0
        self.Kd = D
        if (P != 0.0):
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
          self.u_I[0] = ((self.Kp*self.Ts)/2.0*self.Ti) * (self.error[0]+self.error[1])
        else:
          self.u_I[0] = 0.0

        #eq. dif - met Backward Euler PD with filter N
        self.u_D[1] = self.u_D[0]
        if (self.Td != 0.0):
          ud1 = self.u_D[1]*self.Td/(self.Td+self.N*self.Ts)
          ud2 = self.Kp*self.N*self.Td/(self.Td+self.N*self.Ts)
          self.u_D[0] = ud1 - (ud2* (self.feedback[0] - 2.0*self.feedback[1] + self.feedback[2]))
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
        #if ((self.calc[0] < 0.0) and (feedback_value > 0.0)):
        #  self.calc[0] = 0.0
        #PID brake saturation stop negative inversion protection:
        #elif ((self.calc[0] > 0.0) and (feedback_value < 0.0)):
        #  self.calc[0] = 0.0

        #PID dead zone means the actuator did not respond to the output value (too low)
        if ((self.SetPoint < self.deadband_in)and(self.SetPoint > -self.deadband_in)):
          if((self.calc[0] < self.deadband_out)and(self.calc[0] > -self.deadband_out)):
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
        if (I != 0.0):
          self.Ti = P/I
        else:
          self.Ti = 0.0
        if (P != 0.0):
          self.Td = D/P
        else:
          self.Td = 0.0
