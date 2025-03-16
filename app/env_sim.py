from dataclasses import dataclass
from typing import List, Optional, Union
from logger import logger

@dataclass
class ZigbeeDevice:
    """ This class represents a Zigbee device. It has an ID and type, both strings that the user can
    assign at its will. Since this is used as a companion class of the zigbee2mqtt client, the id_
    can be the device address (or friendly name) and the type_ can be user custom.
    """

    # A note on the name of these class attributes: id and type are names of Python built-in
    # functions. If an attribute called id is declared by the user, this will hide the built-in
    # id() function. This might break code that uses id() or type(), therefore it is advisable to
    # avoid these keywords for naming variables and functions. Thus the name id_ and type_ are used.
    # For more information check:
    # https://stackoverflow.com/questions/77552/id-is-a-bad-variable-name-in-python

    id_: str
    type_: str

@dataclass
class room:
    """ This class represents a room in the system which has two zigbeedevices in it a motion sensor
    and LED strip and a int keeping track of which room it is in the system.
    """

    sensor: ZigbeeDevice
    led: ZigbeeDevice
    room_nr: int


class env_sim:
    """ The env_sim class is responsible for representing and managing access to data. In this case,
    the class is a basic dictionary that uses the devices's ID as key to reference the device
    object. This class also interfaces with the logger class such that it can send trip data to the website.
    """

    def __init__(self,ip_address:str):
        self.__ip_address = ip_address #passes this ip address to the controller and the logger
        self.logger = logger(ip_address) #creates the logger that the env_sim logs through
        self.__devices = {}

    @property
    def actuators_list(self) -> List[ZigbeeDevice]:
        return list(filter(lambda s: s.type_ in {"led"},
                           self.__devices.values()))

    @property
    def devices_list(self) -> List[ZigbeeDevice]:
        return list(self.__devices.values())

    @property
    def sensors_list(self) -> List[ZigbeeDevice]:
        return list(filter(lambda s: s.type_ in {"pir"},
                           self.__devices.values()))

    @property
    def room_list(self) -> List[room]:
        """Creates a list of all the rooms in the system in order of how they were added in the Glow2Go.py file
        """
        ac_list = self.actuators_list
        sen_list = self.sensors_list
        
        room_list = [room(ZigbeeDevice("", ""), ZigbeeDevice("", ""),int()) for _ in range(len(ac_list))]
        for i in range(len(ac_list)):
            room_list[i].led = ac_list[i]
            room_list[i].sensor = sen_list[i]
            room_list[i].room_nr=i
        return room_list

    def get_ip(self) -> str:
        #method used to get the ip in the controller class
        return self.__ip_address


    #interface methods for logger to be used in the controller
    def web_log(self) -> None:
        self.logger.log_web()
    
    def local_log(self,event) -> None:
        self.logger.local_log(event)


    def add(self, device: Union[ZigbeeDevice, List[ZigbeeDevice]]) -> None:
        """ Add a new devices to the database.

        Args:
            device (Union[ZigbeeDevice, List[ZigbeeDevice]]): a device object, or a list of
            device objects to store.
        """
        # If the value given as argument is a ZigbeeDevice, then create a list with it so that
        # later only a list of objects has to be inserted.
        list_devices = [device] if isinstance(device, ZigbeeDevice)\
            else device

        # Insert list of devices, where the device ID is the key of the dictionary.
        for s in list_devices:
            self.__devices[s.id_] = s

    def find(self, device_id: str) -> Optional[ZigbeeDevice]:
        """ Retrieve a device from the database by its ID.

        Args:
            device_id (str): ID of the device to retrieve.

        Returns:
            Optional[ZigbeeDevice]: a device. If the device is not stored, then None is returned
        """
        # Use the bult-in function filter to get the device. The output of filter is a filter object
        # that is then casted to a list. The, the first result, if any, is returned; otherwise None.
        # Instead of None, am exception can also be raised.
        devices = list(filter(lambda kv: kv[0] == device_id,
                              self.__devices.items()))

        return devices[0][1] if len(devices) >= 1 else None