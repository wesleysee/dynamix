## Dynamix

A dynamic webGui for unRAID with enhanced features.

Dynamix is the next step in the evolution of the original SimpleFeatures enhanced webGui for unRAID systems.
Its goal is to have pages dynamically updated and watch the operation of your unRAID server in real-time

### Overview

Dynamix replaces the stock unRAID GUI which comes with version 5.0, do *not* use Dynamix on any prior versions - including
the earlier RC candidates.

In unRAID OS 5.0-rc the last sequence in system start up is as follows (from `/etc/rc.d/rc.local`):

1. Use `installpkg` to install all slackware packages which exist in `/boot/extra`.
2. Use `installplg` to install all plugins which exist in `/boot/plugins`.
3. Use `installplg` to install all plugins which exist in `/boot/config/plugins`.
4. Invoke the `/boot/go` script.

Normally there should be nothing to install in either `/boot/extra` or `/boot/plugins`.

All community-created plugin `plg` files should be downloaded to:
`/boot/config/plugins`

Any slackware packages needed by the plugin should be referenced and downloaded to:
`/boot/packages`

    Note: for unRAID 5.0 these should be from Slackware version 13.1 unless you absolutely need the functionality
    of a newer package.

Any other files needed by the plugin should be downloaded to:
`/boot/config/plugins/<plugin-name>`

If a plugin requires a saved configuration file, it should exist in
`/boot/config/plugins/<plugin-name>/<plugin-name>.cfg`

#### webGui Installation

With that background, we are going to install the webGui plugin file in `/boot/plugins` in order to ensure that
it gets installed first, since it's possible for subsequent plugins to alter or replace files of the webGui
plugin.

First, login to the command line on your server, e.g., at the console or a telnet session.

Next, make sure you have a `/boot/plugins` directory.  If it already exists, **ensure that it's empty**.

Next, make sure you have a `/boot/config/plugins` directory.

Now type this:

```
cd /boot/plugins
wget --no-check-certificate https://github.com/limetech/webGui/raw/master/webGui-latest.plg
installplg webGui-latest.plg
```

If you have any other plugins installed, reboot your server; otherwise, just bring up the webGui in your browser.

#### Re-install

If you want to download a later version of -latest than what you already have, then delete the two files first:

```
cd /boot/plugins
rm webGui-latest*
wget --no-check-certificate https://github.com/limetech/webGui/raw/master/webGui-latest.plg
installplg webGui-latest.plg
```

#### What is the plugin doing?

When installed for the first time, `installplg webGui-latest.plg` will do this:

* Download some needed slackware packages to `/boot/packages` (if not already there).
* Create the `/boot/config/plugins/webGui` directory where the file `webGui.cfg` will be maintained to
store user webGui preferences.
* Delete the current webGui in `/usr/local/emhttp/plugins/webGui`
* Extract the `webGui-latest.txz` package to `/usr/local/emhttp/plugins/webGui`

#### How is the .txz file created?

On the local server:
```
cd /usr/local/emhttp/plugins/webGui
makepkg ../webGui-latest.txz
```

Note that unlike typical slackware packages, this package root is not `/`.  Looking at the `plg` you'll see that
it's installed using `--root /usr/local/emhttp/plugins/webGui` option.

#### Summary of files locations

* `/boot/extra` - contains "system" slackware packages
* `/boot/plugins` - contains "system" plugins
* `/boot/config/plugins` - contains "community-written" plugins
* `/boot/packages` - contains slackware packages downloaded by plugins
* `/boot/config/plugins/<plugin-name>` - directory for plugin <plugin-name> use
* `/boot/config/plugins/<plugin-name>.cfg` - name and location of saved config data maintaned by plugin <plugin-name>
* `/usr/local/emhttp/plugins/<plugin-name>` - runtime location of plugin <plugin-name> code
* `/var/local/emhttp/plugins` - runtime location of plugin temp files
