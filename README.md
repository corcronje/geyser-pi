# GeyserPi

A geyser utility management system based upon the Raspberry Pi.

## Introduction

Reduce electrical power consumption through setting a predefined heating schedule for an electrical geyser, additionally cycling pre-heated water from a solar geyser into the system.

## Details

A Raspberry Pi single board computer (SBC) is to be employed for monitoring two DS18B20 temperature probes, one installed in an electrically heated residential geyser, the other in a solar geyser. The system’s primary function is to reduce electrical power consumption through, maintaining a regulated water temperature in the electrical geyser as per a predefined temperature schedule, and by means of toggling electrical power to the geyser’s element through a relay. 

The system monitor the temperature difference between the solar geyser and the electrical geyser, and circulate warmer solar heated water into the electrical geyser trough activating an electrical pump, thus potentially reducing electrical power consumption even further.

The temperature values and cumulative element and pump active times are logged and a web interface used for displaying the data in user friendly charts. The web interface further allows for all configurable parameters and the temperature schedule to be set by the user.


![System Diagram](https://github.com/CorCronje/GeyserPi/blob/main/Interface/system.png?raw=true)

## Hardware Requirements

1. Raspberry Pi, with Python and a LAMP or LEMP stack.
2. Two DS18B20 sealed temperature probes.
3. Relay breakout board with two relays.

## The Overview Screen

The overview screen displays a line chart with the last 24 hours temperature data for both the solar reservoir and electrical geyser. A bar chart represent the total runtime per hour for the electrical element and circulation pump. The system status panel is used for turning the heating element or circulation element on or off or enabling auto modes. The holiday mode is used for switching the system to solar only mode.

![Overview Screen](https://github.com/CorCronje/GeyserPi/blob/main/Interface/overview.PNG?raw=true)

## The Setup Screen

The setup screen allows the user to configure various parameters and specify an email address and SMTP server that is to be used for automated alerts. The switching temperature represent the temperature difference between the solar reservoir and the electrical geyser, if the temperature in the solar reservoir is greater than that of the electrical geyser and heat energy is required to reach the defined temperature as per the heating schedule, the circulation pump would be activated until the required temperature in the electrical geyser is achieved or a state of equilibrium is attained.

![Setup Screen](https://github.com/CorCronje/GeyserPi/blob/main/Interface/setup.PNG?raw=true)

## The Schedule Screen

The schedule screen allows the user to set a pre-defined heating schedule.

![Schedule Screen](https://github.com/CorCronje/GeyserPi/blob/main/Interface/shedule.PNG?raw=true)

## The History Screen

The history screen allows for easily viewing historical temperature and runtime information for both the reservoirs and the electrical element and circulation pump.

![History Screen](https://github.com/CorCronje/GeyserPi/blob/main/Interface/history.PNG?raw=true)

## Team Members

* [Dr. Henk van Rooyen](http://www.vanrooyen.co.za)
* [Cor Cronje](http://www.phpweb.co.za)