## Dynamix v2.0.1

Dynamix webGui is a dynamic webGui for unRAID systems with enhanced features and optional add-ons.

Dynamix webGui is the next step in the evolution of the original SimpleFeatures enhanced webGui for unRAID systems.
Its goal is to have pages dynamically updated and watch the operation of your unRAID server in real-time.

Dynamix webGui offers a number of improvements not available before:

- Real-time page updates, the status view of your array is always up-to-date.
- Tabbed viewing, more efficient use of the available screen area, scrolling is hardly required.
- Improved visibility, better readibility and consistency of the sans-serif and monospace fonts in different browsers.
- Improved operability, no more accidental cancellations or wrong button clicking.
- Fully compatible with unRAID OS v5.0.

#### Introduction

Dynamix webGui completely replaces the stock unRAID GUI which comes with version 5.0, do **not** use Dynamix on any prior versions - including
the earlier RC candidates of unRAID.

Do **not** mix Dynamix and SimpleFeatures plugins, these are not compatible.

Dynamix webGui requires an up-to-date browser version. It has been tested with the latest versions of Internet Explorer, Chrome and Firefox.

#### Installation

Dynamix is considered a system plugin and is installed in the folder `/boot/plugins`.
This ensures it gets installed first, before any other plugins, and the correct environment is created upfront.

- Login to the command line on your server, e.g., at the console or a telnet session.
- Create the directory `/boot/plugins` directory, if it is not existing.
- Type the below:

```
cd /boot/plugins
wget --no-check-certificate https://raw.github.com/bergware/dynamix/master/plugins/dynamix.webGui-2.0.1-noarch-bergware.plg
```

- Alternatively: [![](/download/dynamix.webGui.png)](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.webGui-2.0.1-noarch-bergware.plg) (right-click and select "save link as")

- Reboot your server to start with a *clean* webGui environment.
- You may need to clear your Browser's cache for correct viewing.

#### Screenshots

The main screen shows the different devices in separate tabs. The current screen is updated in real-time. Click on the tab header to move between screens.

![](/screenshots/main-array.png)

The operation of the array (e.g. parity-check) can be viewed in real-time as well. When a parity-check is ongoing the statusbar at the bottom shows the current status in any screen. Note also the disabling of buttons to avoid accidental mis-clicking!

![](/screenshots/main-paritycheck.png)

Settings are logically grouped. This not only removes the clutter of a busy screen, but makes it also easier to find your way.

![](/screenshots/settings.png)

Monospace font is made more readable, both for main screen and popup screens.

![](/screenshots/system-log.png)

The Browser function has been rewritten for improved speed and visibility of duplicate files. These are clearly indicated by displaying them in orange.

![](/screenshots/duplicates.png)


#### Add-ons (optional plugins)

The following plugins can be used to further enhance the functionality of Dynamix. They all require the Dynamix webGui to be installed (installation will be aborted when the webGui isn't detected). Right-click the links below and select "save link as". These plugins need to be placed in the directory `/boot/config/plugins`.

[![](/download/dynamix.active.streams.png) Active Streams](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.active.streams-2.0.1-noarch-bergware.plg) - View in real-time which streams are currently open

[![](/download/dynamix.cache.dirs.png) Cache Dirs](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.cache.dirs-2.0.1-noarch-bergware.plg) - Keep directories in RAM to prevent unnecessary disk spin-up

[![](/download/dynamix.disk.health.png) Disk Health](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.disk.health-2.0.1-noarch-bergware.plg) - Display SMART information of your hard drives

[![](/download/dynamix.dns.server.png) DNS Server](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.dns.server-2.0.1-noarch-bergware.plg) - Run your own DNS server to maintain your local domain

[![](/download/dynamix.email.notify.png) Email Notify](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.email.notify-2.0.1-noarch-bergware.plg) - Receive periodic status updates by mail

[![](/download/dynamix.s3.sleep.png) S3 Sleep](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.s3.sleep-2.0.1-noarch-bergware.plg) - Put your system to sleep on predefined conditions

[![](/download/dynamix.system.info.png) System Info](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.info-2.0.1-noarch-bergware.plg) - Get detailed hardware information about your system

[![](/download/dynamix.system.stats.png) System Stats](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.stats-2.0.1-noarch-bergware.plg) - Maintain statistics about your drives and system

[![](/download/dynamix.system.temp.png) System Temp](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.temp-2.0.1-noarch-bergware.plg) - View in real-time CPU and motherboard temperatures

[![](/download/dynamix.web.server.png) Web Server](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.web.server-2.0.1-noarch-bergware.plg) - Run your own web server, including PHP
