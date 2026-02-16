# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.

import logging
import sys
import os
import time
import traceback
import signal
import json
import argparse
import websocket
from threading import Thread
#import threading

from jeedom.jeedom import jeedom_socket, jeedom_utils, jeedom_com, JEEDOM_SOCKET_MESSAGE  # jeedom_serial


def listen():
	my_jeedom_socket.open()
	try:
		while 1:
			time.sleep(0.5)
			read_socket()
	except KeyboardInterrupt:
		logging.error('Listen KeyboardInterrupt: %s', 'xxx')
		shutdown()

def read_socket():
	if not JEEDOM_SOCKET_MESSAGE.empty():
		logging.debug("Message received in socket JEEDOM_SOCKET_MESSAGE")
		message = json.loads(JEEDOM_SOCKET_MESSAGE.get().decode('utf-8'))
		if 'apikey' in message and 'message' in message:
			if message['apikey'] == _apikey:
				logging.debug("message valide : %s", message)
				send_message_gizwitz( json.dumps( message['message']) )
			else:
				logging.error("apikey invalide: %s", message)
		else:
			logging.error("Invalid message: %s", message)


def send_socket( mess ):
	def Send_to_jeedom(*args):
		#logging.debug('Send_to_jeedom : ' + mess)
		my_jeedom_com.send_change_immediate( mess )
	Thread(target=Send_to_jeedom).start()

def handler(signum=None, frame=None):
	logging.debug("Signal %i caught, exiting...", int(signum))
	shutdown()


def shutdown():
	logging.debug("Shutdown")
	logging.debug("Removing PID file %s", _pidfile)

	try:
		os.remove(_pidfile)
		logging.debug('shutdown - os.remove OK')
	except Exception as e:
		logging.error('Error removing PID file (os.remove) : %s', e)

	try:
		my_jeedom_socket.close()
		logging.debug('shutdown - socket.close OK')
	except Exception as e:
		logging.error('Error closing my_jeedom_socket: %s', e)

	# try:  # if you need jeedom_serial
	#     my_jeedom_serial.close()
	# except Exception as e:
	#     logging.warning('Error closing serial: %s', e)

	try:
		# Close websocket
		_websocket.close()
		logging.debug('shutdown - websocket.close OK')
	except Exception as e:
		logging.warning('Error closing _websocket: %s', e)

	
	logging.debug("Exit 0")
	sys.stdout.flush()
	os._exit(0)

def start_websocket():
	logging.debug("Starting websocket with the container")
	websocket.enableTrace(False)
	host = "ws://" + _ws_url + ":" + _ws_port + "/ws/app/v1"

	_ws = websocket.WebSocketApp(host,
                                on_open = on_open,
                                on_message=on_message_gizwits,
                                on_error=on_error,
                                on_close=on_close)
	#_ws.run_forever()
	#th = threading.Thread(target=_ws.run_forever)
	#th.daemon = True
	#th.start()
	#Thread(target=_ws.run_forever, daemon=True , kwargs={'ping_interval': 30, 'ping_timeout' : 2}).start()
	Thread(target=_ws.run_forever, daemon=True ).start()
	logging.info("Websocket started")
	
# lecture du websocket
def on_message_gizwits(ws, msg):
	#global _heartbeat
	#_heartbeat = time.time()
	jsonMsg = json.loads(msg)
	
	if jsonMsg['cmd'] == 'login_res' and 'success' in jsonMsg['data']:
		if jsonMsg['data']['success'] == True :
			logging.debug('on_message_gizwits - Login OK : ' + msg)
			#send_socket( vraimess )
		else:
			logging.debug('on_message_gizwits - ERROR Login KO : ' + msg)
	elif jsonMsg['cmd'] == 's2c_noti':
		logging.debug('on_message_gizwits - message notification reçu : ' + msg)
		if 'did' in jsonMsg['data'] and 'attrs' in jsonMsg['data']:
			send_socket( msg )
	elif jsonMsg['cmd'] == 's2c_invalid_msg' and jsonMsg['data']['error_code'] > 0:
		logging.debug('on_message_gizwits - ERROR : ' + str(jsonMsg['data']['error_code']) + ' - ' + jsonMsg['data']['msg'])
		logging.debug('on_message_gizwits - '+ 'keep_running = ' + str(_websocket.keep_running) )
	elif jsonMsg['cmd'] == 'pong':
		logging.debug('on_message_gizwits - PING OK - ' + msg)
	else:
		logging.debug('on_message_gizwits - message non connu : ' + msg)

def on_error(ws, error):
	logging.error('on_error: '+ str(error) )
	logging.debug('on_error: '+ 'keep_running = ' + str(_websocket.keep_running) )

def on_close(ws, close_status_code, close_msg):
	logging.debug('on_close: '+ "Websocket closed")
	logging.debug('on_close: '+ 'keep_running = ' + str(_websocket.keep_running) )

def on_open(ws):
	global _websocket
	_websocket = ws
	logging.debug('on_open: '+ "Websocket opened")
	logging.debug('on_open: '+ 'keep_running = ' + str(_websocket.keep_running) )
	
	message = '{"cmd": "login_req", "data": { "appid": "' + _heatzy_appid + '", "uid": "' + _heatzy_uid + '", "token": "' + _heatzy_token + '", "p0_type": "attrs_v4", "heartbeat_interval": 180 , "auto_subscribe": true }}'
	send_message_gizwitz(message)
	
	def ping(*args):
		logging.debug('ping: '+ 'keep_running = ' + str(_websocket.keep_running) )
		while _websocket.keep_running:
			if (time.time() - _heartbeat) > 60:
				#logging.debug('_heartbeat : ' + str(_heartbeat) + ' - ' + str(time.time() - _heartbeat) + ' - ' + time.ctime(_heartbeat) + ' - ' + str( (time.time() - _heartbeat) > 60 ) )
				send_message_gizwitz( '{"cmd": "ping"}' )
			time.sleep(10)
	Thread(target=ping).start()
	
	logging.debug('on_open: '+ "Websocket opened FIN")
	#Thread(target=run).start()
	#run()

def send_message_gizwitz( mess ):
	global _heartbeat
	_heartbeat = time.time()
	def SendMessage(*args):
		logging.debug('send_message_gizwitz' + mess)
		_websocket.send(mess)
	Thread(target=SendMessage).start()


_log_level = 'debug'
_socket_port = 55099
_socket_host = 'localhost'
_device = 'auto'
_pidfile = '/tmp/heatzyd.pid'
_apikey = ''
_callback = ''
_cycle = 0.3
_ws_url = 'eusandbox.gizwits.com'
_ws_port = '8080'
_heartbeat = time.time()

parser = argparse.ArgumentParser(description='Desmond Daemon for Jeedom plugin')
parser.add_argument("--device", help="Device", type=str)
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--callback", help="Callback", type=str)
parser.add_argument("--apikey", help="Apikey", type=str)
parser.add_argument("--cycle", help="Cycle to send event", type=float)
parser.add_argument("--pid", help="Pid file", type=str)
parser.add_argument("--socketport", help="Port for socket server", type=str)
parser.add_argument("--appid", help="App id heatzy", type=str)
parser.add_argument("--token", help="token heatzy", type=str)
parser.add_argument("--uid", help="uid heatzy", type=str)
args = parser.parse_args()

if args.device:
	_device = args.device
if args.loglevel:
	_log_level = args.loglevel
if args.callback:
	_callback = args.callback
if args.apikey:
	_apikey = args.apikey
if args.pid:
	_pidfile = args.pid
if args.cycle:
	_cycle = float(args.cycle)
if args.socketport:
	_socket_port = args.socketport
if args.appid:
	_heatzy_appid = args.appid
if args.token:
	_heatzy_token = args.token
if args.appid:
	_heatzy_uid = args.uid

_socket_port = int(_socket_port)

jeedom_utils.set_log_level(_log_level)

logging.info('Start demond %s' , time.ctime())
logging.info('Start demond %s' , time.time())
logging.info('Start demond %s' , time.ctime(time.time()))
logging.info('Log level: %s', _log_level)
logging.info('Socket port: %s', _socket_port)
logging.info('Socket host: %s', _socket_host)
logging.info('PID file: %s', _pidfile)
logging.info('Apikey: %s', _apikey)
logging.info('Device: %s', _device)

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)

try:
	jeedom_utils.write_pid(str(_pidfile))
	my_jeedom_com = jeedom_com(apikey=_apikey, url=_callback, cycle=_cycle)
	if not my_jeedom_com.test():
		logging.error('Network communication issues. Please fixe your Jeedom network configuration.')
		shutdown()
	# my_jeedom_serial = jeedom_serial(device=_device)  # if you need jeedom_serial
	my_jeedom_socket = jeedom_socket(port=_socket_port, address=_socket_host)
    # https://github.com/lxrootard/eufy/blob/322ff841b71cd7db0e901caca8706fa4ce7452ed/resources/eufyd/eufyd.py#L287
	# my_jeedom_com = jeedom_com(_apikey, _callback)
	
	# Start WebSocket connection with the container
	start_websocket()
	
	listen()
except Exception as e:
	#logging.debug('Fatal error')
	logging.error('Fatal error: %s', e)
	logging.info(traceback.format_exc())
	shutdown()