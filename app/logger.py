import json
import requests
from dataclasses import dataclass
from datetime import datetime
import logging
from time import localtime,strftime,time,gmtime

#how the data is formatted for the web api
@dataclass
class formatted_data:
    eventId: int = 0
    startTime: str = "1971-01-01 00:00:01.000000"
    TimeToBathroom: str = "1971-01-01 00:00:01.000000"
    timeInBathroom: str = "00:00:00.000000"
    TimetoBedroom: str = "1971-01-01 00:00:01.000000"
    totalTime: str = "00:00:00.000000"
    alarmed: int = 0
    residentId: int = 1234

#class used to log events to the database through the web api
class logger:
    _eventID=0
    _startTime=0
    _TimeToBathroom=0
    _timeInBathroom=0
    _Time_to_Bedroom=0
    _totalTime=0
    _alarmed=0
    _first=0
    _second=0

    post_data = {}
    headers = {
    'Content-Type': 'application/json'
    }

    def __init__(self,ip_address:str):
        self.local_data=formatted_data()
        self.ip_address=ip_address
        self.__api_url = f'http://{ip_address}/Glow2Go/api.php'

    #formats data such that it is ready to be logged
    def local_log(self,event)->None:
        print(f"logging event: {event}")
        if(event==0):#save startTime
            self._startTime=localtime()
            self._second = time()
            self.local_data.startTime=strftime("%Y-%m-%d %H:%M:%S.000000",self._startTime)
        elif(event==1):#save timeToBathroom
            self._TimeToBathroom = localtime()
            self._first = time()
            self.local_data.TimeToBathroom=strftime("%Y-%m-%d %H:%M:%S.000000",self._TimeToBathroom)
        elif(event==2):#save timeInBathroom
            self._timeInBathroom = time()- self._first
            self.local_data.timeInBathroom=strftime("%H:%M:%S.000000",(gmtime(self._timeInBathroom)))
        elif(event==3):#save timeToBedroom
            self._Time_to_Bedroom = localtime()
            self.local_data.TimetoBedroom=strftime("%Y-%m-%d %H:%M:%S.000000",self._Time_to_Bedroom)
        elif(event==4):#save totalTime
            self._totalTime = time()-self._second
            self.local_data.totalTime=strftime("%H:%M:%S.000000",gmtime(self._totalTime))
        elif(event==5):#set alarmed
            self.local_data.alarmed=1
        else:
            return
    #posts logged data to the web api.
    def log_web(self):
        print(self.__api_url)
        """ Create a JSON representation of the event.
        
        Returns:
            str: a JSON string.
        """

        try:
            response=requests.get(f"{self.__api_url}?resource=get_latest_event&ID=1234")
            latest_event=response.json()
            print(latest_event[0]['eventId'])
            self.local_data.eventId = int(latest_event[0]['eventId'])+1
            self.post_data = { 
            'resource': 'event',
            'data': {
                'eventId' : self.local_data.eventId,
                'startTime' : self.local_data.startTime,
                'TimeToBathroom' : self.local_data.TimeToBathroom,
                'timeInBathroom' : self.local_data.timeInBathroom,
                'TimetoBedroom' : self.local_data.TimetoBedroom,
                'totalTime' : self.local_data.totalTime,
                'alarmed' : self.local_data.alarmed,
                'residentId' : self.local_data.residentId
                }
            }
            print(self.post_data)
            #reset times:
            self.local_data.startTime = "1971-01-01 00:00:01.000000"
            self.local_data.TimeToBathroom = "1971-01-01 00:00:01.000000"
            self.local_data.timeInBathroom = "00:00:00.000000"
            self.local_data.TimetoBedroom = "1971-01-01 00:00:01.000000"
            self.local_data.totalTime = "00:00:00.000000"
            self.local_data.alarmed = 0
            # Send POST request to API endpoint
            response = requests.post(self.__api_url, data=json.dumps(self.post_data), headers=self.headers)
            response.raise_for_status()
            print("logging was succesfull")
        except requests.exceptions.RequestException as e:
            logging.error(f"API error occurred: {e}. Unable to log to web app.")