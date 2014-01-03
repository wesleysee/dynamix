## Dynamix v2.0.6

#### Update

*Dynamix webGui has been updated to version 2.0.6. Thanks to the support of the unRAID community a number of fixes and improvements are introduced. See the folder "changes" for a list of what has been changed, including changes in the optional plugins. All users of earlier versions are encouraged to upgrade to this version.*


#### Introduction

Dynamix webGui is a dynamic webGui for unRAID systems with enhanced features and optional add-ons.

Dynamix webGui is the next step in the evolution of the original SimpleFeatures enhanced webGui for unRAID systems.
Its goal is to have pages dynamically updated and watch the operation of your unRAID server in real-time.

Dynamix webGui offers a number of improvements not available before:

- Real-time page updates, the status view of your array is always up-to-date.
- Tabbed viewing, more efficient use of the available screen area, scrolling is hardly required.
- Improved visibility, better readibility and consistency of the sans-serif and monospace fonts in different browsers.
- Improved operability, no more accidental cancellations or wrong button clicking.
- Fully compatible with unRAID OS v5.0.

Dynamix webGui completely replaces the stock unRAID GUI which comes with version 5.0, do **not** use Dynamix webGui on any earlier versions - including the earlier RC candidates of unRAID.

Do **not** mix Dynamix and SimpleFeatures plugins, these are not compatible.

Dynamix webGui requires an up-to-date browser version. It has been tested with the latest versions of Internet Explorer, Chrome, Firefox and Safari.

#### Download warning

*DO NOT DOWNLOAD FILES DIRECTLY FROM THE FOLDERS 'DOWNLOAD' OR 'PLUGINS' BUT USE THE LINKS PROVIDED IN THIS 'README.MD' FILE INSTEAD.*

#### Installation

*This describes a first time installation, if you are upgrading from an older version, see the section 'Upgrading'.*

Dynamix webGui is considered a system plugin and is installed in the folder `/boot/plugins`.
This ensures it gets installed first, before any other plugins, and the correct environment is created upfront.

- Login to the command line on your server, e.g., at the console or a telnet session.
- Create the directory `/boot/plugins` directory, if it is not existing.
- Type the below:

```
cd /boot/plugins
wget --no-check-certificate https://raw.github.com/bergware/dynamix/master/plugins/dynamix.webGui-2.0.6-noarch-bergware.plg
```

- Alternatively: [![](/download/dynamix.webGui.png)](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.webGui-2.0.6-noarch-bergware.plg) (right-click and select "save link as")

- Reboot your server to start with a *clean* webGui environment.
- You may need to clear your Browser's cache for correct viewing.

#### Upgrading

You can upgrade to the latest version of [![](/download/dynamix.webGui.png)](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.webGui-2.0.6-noarch-bergware.plg) (right-click and select "save link as")

Save the file in `/boot/plugins` and type the below:

```
cd /boot/plugins
rm dynamix.webGui-2.0.1-noarch-bergware.plg
rm dynamix.webGui-2.0.2-noarch-bergware.plg
rm dynamix.webGui-2.0.3-noarch-bergware.plg
rm dynamix.webGui-2.0.4-noarch-bergware.plg
rm dynamix.webGui-2.0.5-noarch-bergware.plg
installplg dynamix.webGui-2.0.6-noarch-bergware.plg
```

#### Optional plugins

The following plugins can be used to further enhance the functionality of Dynamix. They all require the Dynamix webGui to be present (installation of the add-on will be aborted when the webGui isn't detected). Right-click the links below and select "save link as". These plugins need to be placed in the directory `/boot/config/plugins`.

To install the plugin after copying it, telnet to your unRAID server and type the following:
```
cd /boot/config/plugins
installplg <name-of-plugin>.plg
```

If you are upgrading from an older version, do the following:
```
cd /boot/config/plugins
rm <old-plugin-version>.plg
installplg <new-plugin-version>.plg
```

[![](/download/dynamix.plugin.control.png) Plugin Control](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.plugin.control-2.0.1-noarch-bergware.plg) - Install, Update and Uninstall Dynamix plugins via the GUI *(new v2.0.1)*

[![](/download/dynamix.active.streams.png) Active Streams](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.active.streams-2.0.1-noarch-bergware.plg) - View in real-time which streams are currently open *(v2.0.1)*

[![](/download/dynamix.cache.dirs.png) Cache Dirs](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.cache.dirs-2.0.1-noarch-bergware.plg) - Keep directories in RAM to prevent unnecessary disk spin-up *(v2.0.1)*

[![](/download/dynamix.disk.health.png) Disk Health](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.disk.health-2.0.2-noarch-bergware.plg) - Display SMART information of your hard drives *(updated v2.0.2)*

[![](/download/dynamix.dns.server.png) DNS Server](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.dns.server-2.0.1-noarch-bergware.plg) - Run your own DNS server to maintain your local domain *(v2.0.1)*

[![](/download/dynamix.email.notify.png) Email Notify](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.email.notify-2.0.2-noarch-bergware.plg) - Receive periodic status updates by mail *(v2.0.2)*

[![](/download/dynamix.s3.sleep.png) S3 Sleep](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.s3.sleep-2.0.1-noarch-bergware.plg) - Put your system to sleep on predefined conditions *(v2.0.1)*

[![](/download/dynamix.system.info.png) System Info](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.info-2.0.1-noarch-bergware.plg) - Get detailed hardware information about your system *(v2.0.1)*

[![](/download/dynamix.system.stats.png) System Stats](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.stats-2.0.2-noarch-bergware.plg) - Maintain statistics about your drives and system *(updated v2.0.2)*

[![](/download/dynamix.system.temp.png) System Temp](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.temp-2.0.2-noarch-bergware.plg) - View in real-time CPU and motherboard temperatures *(updated v2.0.2)*

[![](/download/dynamix.web.server.png) Web Server](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.web.server-2.0.2-noarch-bergware.plg) - Run your own web server, including PHP *(v2.0.2)*


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

The optional "plugin control" module allows for easy installation, updating and uninstallation of all of the available Dynamix plugins via the GUI. This way maintaining your plugins becomes a breeze!

![](/screenshots/plugin-control.png)
