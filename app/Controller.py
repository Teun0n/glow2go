from env_sim import env_sim
from WebClient import WebClient, WebDeviceEvent
from Zigbee2mqttClient import (Zigbee2mqttClient,
                                   Zigbee2mqttMessage, Zigbee2mqttMessageType)
import time
from datetime import datetime
import requests



class Controller:
    HTTP_HOST = "http://localhost:8080"
    MQTT_BROKER_HOST = "localhost"
    MQTT_BROKER_PORT = 1883

    """ The controller is responsible for managing events received from zigbee2mqtt and handle them.
    By handle them it can be process, store and communicate with other parts of the system. In this
    case, the class listens for zigbee2mqtt events, processes them (turn on another Zigbee device)
    and send an event to a remote HTTP server.
    """
    #different intial values set here used in the control logic.

    moving = False #used to check if UC1 is complete

    bedroom_movement = False #used to see if resident has been moving in the bedroom
    light_timestamp=-1 #used to check wether or not 5 minutes has passed since movement in the bedroom
    safe_guard=False #used to prevent edge case when entering the bedroom after a trip.
    LR_bathroom =False #set to true when resident enters bathroom.
    current_room = 0
    alarmed_timestamp=time.time() # used to measure when 2 hours of no movement has occured.
    is_alarmed=False #is set to true after system is alarmed prevents continous alarmed logs

    def __init__(self, devices_model: env_sim) -> None:
        """ Class initializer. The actuator and monitor devices are loaded (filtered) only when the
        class is instantiated. If the database changes, this is not reflected.

        Args:
            devices_model (env_sim): the model that represents the data of this application
        """

        self.__devices_model = devices_model

        self.__z2m_client = Zigbee2mqttClient(host=self.MQTT_BROKER_HOST,
                                                  port=self.MQTT_BROKER_PORT,
                                                  on_message_clbk=self.__zigbee2mqtt_event_received)

    def start(self) -> None:
        """ Start listening for zigbee2mqtt events.
        """
        self.__z2m_client.connect()
        print(f"Zigbee2Mqtt is {self.__z2m_client.check_health()}")


    def stop(self) -> None:
        """ Stop listening for zigbee2mqtt events.
        """
        self.__z2m_client.disconnect()
    def active_hours(self) -> bool:
        #method to check wether we're in the active hours or not.
        # if the web api is unreachable active hours is set to 22:00 -> 09:00 
        try:
            response=requests.get(f"http://{self.__devices_model.get_ip()}/Glow2Go/api.php?resource=active_hours&ID=1234")
            active_hours_json=response.json()
            print(active_hours_json)
            startTime = active_hours_json['startTime']
            endTime = active_hours_json['endTime']
            start_hr=int(startTime[0:2])
            start_min=int(startTime[3:5])
            end_hr=int(endTime[0:2])
            end_min=int(endTime[3:5])
        except: #if api isn't working
            start_hr = 22
            start_min = 0
            end_hr = 9
            end_min = 0
        t = datetime.now()
        start=datetime(year=t.year,month=t.month,day=t.day,hour=start_hr,minute=start_min)
        end=datetime(year=t.year,month=t.month,day=t.day,hour=end_hr,minute=end_min)
        if(t<=end or start<=t):
            return True
        else:
            return False

    def bedroom_lights(self) -> None:
        #method for turning off lights when user returns to bedroom or hasn't left bathroom.
        if(self.light_timestamp == -1 or self.current_room!=0):
            return
        t=time.time()-5*60 # checks if 5 minutes have passed
        if(t>=self.light_timestamp):
            self.light_timestamp=-1
            self.__z2m_client.change_state(self.__devices_model.room_list[0].led.id_, "OFF")
            self.__z2m_client.change_state(self.__devices_model.room_list[1].led.id_, "OFF")
    def alarmed(self) -> None:
        #method for logging to the database as alarmed
        if(not self.moving or not self.is_alarmed):
            return
        if(time.time()-60*60*2>=self.alarmed_timestamp):# checks if there hasn't been any movement in 2 hours.
            self.__devices_model.local_log(5)
            self.__devices_model.web_log(5)
            self.is_alarmed=True
            print("Alarmed")
            return

            

    def set_light_state(self, room_nr) -> None:
    # Turns off ligt two rooms from current room, and turns on light in current and adjacent rooms.

        if(room_nr+2<=len(self.__devices_model.room_list)-1):
            self.__z2m_client.change_state(self.__devices_model.room_list[room_nr+2].led.id_, "OFF")
        if(room_nr-2>=0):
            self.__z2m_client.change_state(self.__devices_model.room_list[room_nr-2].led.id_, "OFF")
        self.__z2m_client.change_state(self.__devices_model.room_list[room_nr+1].led.id_, "ON")
        self.__z2m_client.change_state(self.__devices_model.room_list[room_nr-1].led.id_, "ON")
        return

    def __zigbee2mqtt_event_received(self, message: Zigbee2mqttMessage) -> None:
        """ Process an event received from zigbee2mqtt. This function given as callback to
        Zigbee2mqttClient, which is then called when a message from zigbee2mqtt is received.

        Args:
            message (Zigbee2mqttMessage): an object with the message received from zigbee2mqtt
        """
        print("last room was " + str(self.current_room)+" at epoch time" + str(self.light_timestamp))            

        if not self.moving and not self.active_hours():
            print("not in active hours")
            return
        
        # If message is None (it wasn't parsed), then don't do anything.
        if not message:
            return

        print(
            f"zigbee2mqtt event received on topic {message.topic}: {message.data}")

        # If the message is not a device event, then don't do anything.
        if message.type_ != Zigbee2mqttMessageType.DEVICE_EVENT:
            return

        # Parse the topic to retreive the device ID. If the topic only has one level, don't do
        # anything.
        tokens = message.topic.split("/")
        if len(tokens) <= 1:
            return

        # Retrieve the device ID from the topic.
        device_id = tokens[1]

        # If the device ID is known, then process the device event and send a message to the remote
        # web server.
        device = self.__devices_model.find(device_id)

        self.safe_guard=False

        if device:
            try:
                occupancy = message.event["occupancy"]
            except KeyError:
                pass
            else:
                # Iterates through all the rooms in the room_list
                for curr in self.__devices_model.room_list:
                    if(occupancy):
                        print(f"safe guard {self.safe_guard}")
                        self.alarmed_timestamp=time.time()
                        #finds room that movement was detected in
                        if(curr.sensor.id_ == device_id):

                            self.current_room = curr.room_nr
                            #UC5  when user returns to room
                            if(curr.room_nr==0 and self.moving):
                                
                                self.__devices_model.local_log(3) #log time to bedroom
                                self.__devices_model.local_log(4) #log total time
                                self.__devices_model.web_log()#post to database through api

                                self.light_timestamp=time.time()#used to see when 5 minutes have passed

                                self.__z2m_client.change_state(self.__devices_model.room_list[curr.room_nr+2].led.id_, "OFF")
                                #reset system
                                self.moving=False
                                self.safe_guard = True
                                

                            #UC1 when user hasn't left bedroom
                            elif(curr.room_nr==0 and (not self.moving) and not self.safe_guard):
                                    self.light_timestamp=time.time()
                                    self.__z2m_client.change_state(curr.led.id_, "ON")
                                    self.__z2m_client.change_state(self.__devices_model.room_list[curr.room_nr+1].led.id_, "ON")
                                    self.bedroom_movement = True

                            #UC1 
                            #end of main flow for UC1 when user enters next room
                            elif(curr.room_nr==1 and (not self.moving) and self.bedroom_movement):
                                self.__devices_model.local_log(0)
                                print("UC1 complete")
                                self.moving = True
                                self.__z2m_client.change_state(self.__devices_model.room_list[curr.room_nr+1].led.id_, "ON")
                                self.safe_guard=False
                                self.light_timestamp=-1

                            #UC3 resident enters bathroom and timestamp is recorded
                            elif(curr.room_nr == len(self.__devices_model.room_list)-1 and self.moving):
                                if(not self.LR_bathroom):
                                    self.__devices_model.local_log(1)

                                self.LR_bathroom=True

                                print("movement in bathroom")
                                self.__z2m_client.change_state(self.__devices_model.room_list[curr.room_nr-2].led.id_, "OFF")
                            
                            #UC4 resident leaves bathroom and time in bathroom is recorded
                            elif(self.LR_bathroom and curr.room_nr == len(self.__devices_model.room_list)-2 and self.moving):
                                # log time in bathroom to json
                                self.__devices_model.local_log(2)
                                self.LR_bathroom=False
                                self.__z2m_client.change_state(self.__devices_model.room_list[curr.room_nr-1].led.id_, "ON")
                            #UC2
                            elif(self.moving):
                                self.set_light_state(curr.room_nr)
                            occupancy=False
                            break
                
                # Register event in the remote web server.
                web_event = WebDeviceEvent(device_id=device.id_,
                                               device_type=device.type_,
                                               measurement=occupancy)

                client = WebClient(self.HTTP_HOST)
                try:
                    client.send_event(web_event.to_json())
                except ConnectionError as ex:
                    print(f"{ex}")