#!/bin/sh
# Job Scheduler
#
#  Copyright 2014, Minas Dasygenis, http://arch.icte.uowm.gr/mdasygenis

#  Licensed under the Apache License, Version 2.0 (the "License");
#  you may not use this file except in compliance with the License.
#  You may obtain a copy of the License at

#      http://www.apache.org/licenses/LICENSE-2.0

#  Unless required by applicable law or agreed to in writing, software
#  distributed under the License is distributed on an "AS IS" BASIS,
#  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#  See the License for the specific language governing permissions and
#  limitations under the License.


#Set variables here
jobdir=/tmp/jobs
statusdir=/tmp/status
siddir=/tmp/VHDL
userdir=/home/user/hdl-compiler
maxexectime=10





scheduler=1
localqueue=/tmp/run$scheduler
pidfile=/var/run/scheduler$scheduler
lockfile=/tmp/lock$scheduler
processingstatus=/tmp/status$scheduler
pid=$$
statusfile=$statusdir/$pid

trap 'rm -f $lockfile $statusfile> /dev/null 2>&1 ; exit' INT TERM EXIT

if [ -f $lockfile ] ; then
	echo "Scheduler is running.Cannot start"
	exit 2
fi

#check directory existance
if [ ! -d "$jobdir" ] ; then
	echo "Dir $jobdir absent"
	exit 2
fi

if [ ! -d "$statusdir" ] ; then
        echo "Dir $statusdir absent"
	mkdir -p $statusdir
	if [ ! $?  ] ; then
		echo "Cannot create $statusdir"
		exit 3
	fi
fi


if [ ! -d "$localqueue" ] ; then
	echo Creating LocalQueue
	mkdir -p $localqueue
	if [ ! $?  ] ; then
		echo "Cannot create $localqueue"
		exit 3
	fi
fi

touch $lockfile
echo $pid > $pidfile
echo "Our pid is $pid. Saved at $pidfile"

#Neverending loop to pick jobs
while [ 0 -eq 0 ] ; 
do
sleep 1
jobs=`find $jobdir -type f -print | wc -l`

#is there any work file?
if  [  "$jobs"  -eq 0 ] ; then
	echo Idle at `/bin/date +"%D %H:%M"` | tee $statusfile
	continue
fi

#here we have a job..lets process it
#jobfile=`find $jobdir -print |sort | head -2 | tail -1`
##lets find the oldest one
jobfile=`find $jobdir  -type f -printf '%T+ %p\n' | sort | head -n 1| cut -f 2  -d" "`
number=`basename $jobfile`
echo "Working on Job ID: $number [in queue $jobs] at `/bin/date +"%D %H:%M"` " | tee  $statusfile
mv $jobfile $localqueue
. $localqueue/$number > /dev/null 2>&1 
# pid=$!
# echo Process pid: $pid
# (sleep $maxexectime && kill -9 $pid ) > /dev/null 2>&1 
rm $localqueue/$number
find $siddir -type f -size +10M -delete
find $userdir -type f -size +20M -delete
rm -rf ~/.local/share/Trash/*
done

