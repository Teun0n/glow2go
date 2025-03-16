from time import sleep
from Controller import Controller
from env_sim import env_sim, ZigbeeDevice


if __name__ == "__main__":

    #ip address of machine running php web-server is passed
    ip = input("Enter ip address of php server: ")
    #ip is passed to env_sim such that we can access the API
    devices_model = env_sim(ip)
    
    #device_model is created with all LEDs and motion sensor devices
    devices_model.add([ZigbeeDevice("0x00158d00057b4cd8", "pir"),
                       ZigbeeDevice("0x54ef441000947eee", "pir"),
                       ZigbeeDevice("0x00158d0007e3d31f", "pir"),
                       ZigbeeDevice("0x54ef44100094966a", "pir"),
                       ZigbeeDevice("0xbc33acfffe8b8e93", "led"),
                       ZigbeeDevice("0x84fd27fffec8a676", "led"),
                       ZigbeeDevice("0x680ae2fffec0cc92", "led"),
                       ZigbeeDevice("0xcc86ecfffebfafe1", "led")
                       ])

    # Create a controller and give it the data model that was instantiated.
    controller = Controller(devices_model)
    controller.start()

    #methods that get activate in the absense of a mqtt message run in this loop
    #because the controller only reacts to mqqt messages not a lack of them.
    while True:
        sleep(1)
        controller.bedroom_lights()
        controller.alarmed()
        #l.log_web()