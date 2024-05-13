#!/bin/sh
set -e

rsyslogd -iNONE

apache2-foreground
