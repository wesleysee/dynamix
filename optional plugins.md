#### Add-ons (optional plugins)

The following plugins can be used to further enhance the functionality of Dynamix. They all require the Dynamix webGui to be installed (installation of the add-on will be aborted when the webGui isn't detected). Right-click the links below and select "save link as". These plugins need to be placed in the directory `/boot/config/plugins`.

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

[![](/download/dynamix.active.streams.png) Active Streams](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.active.streams-2.0.1-noarch-bergware.plg) - View in real-time which streams are currently open

[![](/download/dynamix.cache.dirs.png) Cache Dirs](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.cache.dirs-2.0.1-noarch-bergware.plg) - Keep directories in RAM to prevent unnecessary disk spin-up

[![](/download/dynamix.disk.health.png) Disk Health](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.disk.health-2.0.2-noarch-bergware.plg) - Display SMART information of your hard drives (updated)

[![](/download/dynamix.dns.server.png) DNS Server](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.dns.server-2.0.1-noarch-bergware.plg) - Run your own DNS server to maintain your local domain

[![](/download/dynamix.email.notify.png) Email Notify](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.email.notify-2.0.2-noarch-bergware.plg) - Receive periodic status updates by mail

[![](/download/dynamix.s3.sleep.png) S3 Sleep](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.s3.sleep-2.0.1-noarch-bergware.plg) - Put your system to sleep on predefined conditions

[![](/download/dynamix.system.info.png) System Info](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.info-2.0.1-noarch-bergware.plg) - Get detailed hardware information about your system

[![](/download/dynamix.system.stats.png) System Stats](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.stats-2.0.2-noarch-bergware.plg) - Maintain statistics about your drives and system (updated)

[![](/download/dynamix.system.temp.png) System Temp](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.system.temp-2.0.2-noarch-bergware.plg) - View in real-time CPU and motherboard temperatures (updated)

[![](/download/dynamix.web.server.png) Web Server](https://raw.github.com/bergware/dynamix/master/plugins/dynamix.web.server-2.0.2-noarch-bergware.plg) - Run your own web server, including PHP
